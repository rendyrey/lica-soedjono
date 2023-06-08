<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;
use PDO;

class AnalyticController extends Controller
{
    const STATUS = 1;
    const STATUS_POST_ANALYTIC = 2;
    const RESULT_STATUS_NORMAL = 1;
    const RESULT_STATUS_LOW = 2;
    const RESULT_STATUS_HIGH = 3;
    const RESULT_STATUS_ABNORMAL = 4;
    const RESULT_STATUS_CRITICAL = 5;
    /**
     * 
     */
    public function index()
    {
        $data['title'] = 'Analytics';
        $data['PRINTTESTPERALLGROUP'] = config('licaconfig.PRINTTESTPERALLGROUP');
        $data['PRINTTESTPERGROUP'] = false;
        // dd($data);

        // get verificator data from users
        $verificator_query = \App\User::selectRaw('users.id as user_id, users.name')->get();
        $data['verificators'] = $verificator_query;

        return view('dashboard.analytics.index', $data);
    }

    /**
     * 
     */
    public function datatable($startDate = null, $endDate = null, $group_id = null)
    {
        $area_id = session('area_id');
        $is_igd = 0;
        if ($area_id == 'igd') {
            $is_igd = 1;
        }
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            // if the startDate and endDate is set, the query will be depend on the given date.
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = \App\Transaction::selectRaw('transactions.*, transactions.id as t_id')->where('analytic_time', '>=', $from);
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->leftJoin('transaction_tests', 'transaction_tests.transaction_id', 'transactions.id');
            $model->where('transaction_tests.group_id', '=', $group_id);
        }
        // $model->where('is_igd', $is_igd);
        $model->where('analytic_time', '<=', $to);
        $model->where('status', AnalyticController::STATUS);
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->groupBy('transaction_tests.transaction_id');
        }
        $model->orderBy('cito', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);

    }

    /**
     * 
     */
    public function datatableTest($transactionId)
    {
        $model_query = \App\TransactionTest::selectRaw('transaction_tests.*, transaction_tests.id as tt_id, results.result as res_label, packages.name as package_name')
            ->leftJoin('tests', 'tests.id', 'transaction_tests.test_id')
            ->leftJoin('groups', 'groups.id', 'tests.group_id')
            ->leftJoin('packages', 'packages.id', 'transaction_tests.package_id')
            ->leftJoin('results', 'results.id', 'result_label')
            ->where('transaction_id', $transactionId)
            ->orderBy('groups.id', 'asc')
            ->orderBy('tests.sequence', 'asc');
        $model = $model_query->get();

        $transaction = \App\Transaction::findOrFail($transactionId);

        $data['table'] = $model;
        $data['transaction'] = $transaction;
        $html = view('dashboard.analytics.transaction-test-table', $data)->render();
        return response()->json(['html' => $html, 'data' => $model]);
    }

    /**
     * 
     */
    public function resultLabel($testId)
    {
        $checkMasterRange = \App\Range::where('test_id', $testId)->exists();
        $test = \App\Test::where('id', $testId)->first();

        if ($test->range_type == 'label') {
            $results = \App\Result::where('test_id', $testId)->get();
            $options = '<option value=""></option>';
            foreach ($results as $result) {
                $options .= '<option value="' . $result->result . '">' . $result->result . '</option>';
            }

            return $options;
        } else if ($test->range_type == 'number' && !$checkMasterRange) {
            return response()->json(['message' => 'PLEASE SET RESULT RANGE']);
        }
    }

    /**
     * 
     */
    public function transaction($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);

            return response()->json(['message' => 'SUCCESS', 'data' => $transaction]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function checkFormula($transactionId, $transactionTestId, $testId)
    {
        $query = DB::table('formulas')->where('test_reference_id', $testId)->first();
        $formulaData = $query;
        $formula_split = str_split($query->formulas);
        $arrayLength = count($formula_split);

        // echo "transaction id : " . $formulaData->a_id . ' <br>';
        // echo "tes a : " . $formulaData->a_id . ' <br>';
        // echo "tes b : " . $formulaData->b_id . ' <br>';
        // die;

        // result a
        $test_a_result_query = DB::table('transaction_tests')
            ->select('transaction_tests.result_number')
            ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->where('transactions.id', $transactionId)
            ->where('transaction_tests.test_id', $formulaData->a_id)->first();
        $test_a_result = $test_a_result_query->result_number;
        $test_a_result = str_replace(',', '', $test_a_result);
        // result b
        $test_b_result_query = DB::table('transaction_tests')
            ->select('transaction_tests.result_number')
            ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->where('transactions.id', $transactionId)
            ->where('transaction_tests.test_id', $formulaData->b_id)->first();
        $test_b_result = $test_b_result_query->result_number;
        $test_b_result = str_replace(',', '', $test_b_result);
        // check each test_a , test_b formula , test_c formula
        $a_result = 0;
        $b_result = 0;
        $c_result = 0;
        if (($formulaData->a_operation != null || $formulaData->a_operation != '') && ($formulaData->a_value != null || $formulaData->a_value != '')) {
            // operation test_a
            if ($formulaData->a_operation == "+") {
                $a_result = $test_a_result + $formulaData->a_value;
            } else if ($formulaData->a_operation == "-") {
                $a_result = $test_a_result - $formulaData->a_value;
            } else if ($formulaData->a_operation == "*") {
                $a_result = $test_a_result * $formulaData->a_value;
            } else {
                $a_result = $test_a_result / $formulaData->a_value;
            }
        } else {
            $a_result = $test_a_result;
        }

        if (($formulaData->b_operation != null || $formulaData->b_operation != '') && ($formulaData->b_value != null || $formulaData->b_value != '')) {
            // operation test_b
            if ($formulaData->b_operation == "+") {
                $b_result = $test_b_result + $formulaData->b_value;
            } else if ($formulaData->a_operation == "-") {
                $b_result = $test_b_result - $formulaData->b_value;
            } else if ($formulaData->a_operation == "*") {
                $b_result = $test_b_result * $formulaData->b_value;
            } else {
                $b_result = $test_b_result / $formulaData->b_value;
            }
        } else {
            $b_result = $test_b_result;
        }

        $formula_test1 = '';
        $formula_test2 = '';
        $formula_test3 = '';
        $formula_operation1 = '';
        $formula_operation2 = '';

        if ($arrayLength == 3) {
            $formula_test1 = $formula_split[0];
            $formula_test2 = $formula_split[2];
            $formula_operation1 = $formula_split[1];
        } else if ($arrayLength == 5) {
            $formula_test1 = $formula_split[0];
            $formula_test2 = $formula_split[2];
            $formula_test3 = $formula_split[4];
            $formula_operation1 = $formula_split[1];
            $formula_operation2 = $formula_split[3];

            // result c
            $test_c_result_query = DB::table('transaction_tests')
                ->select('transaction_tests.result_number')
                ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
                ->where('transactions.id', $transactionId)
                ->where('transaction_tests.test_id', $formulaData->c_id)->first();
            $test_c_result = $test_c_result_query->result_number;
            $test_c_result = str_replace(',', '', $test_c_result);

            if (($formulaData->c_operation != null || $formulaData->c_operation != '') && ($formulaData->c_value != null || $formulaData->c_value != '')) {
                // operation test_a
                if ($formulaData->c_operation == "+") {
                    $c_result = $test_c_result + $formulaData->c_value;
                } else if ($formulaData->c_operation == "-") {
                    $c_result = $test_c_result - $formulaData->c_value;
                } else if ($formulaData->c_operation == "*") {
                    $c_result = $test_c_result * $formulaData->c_value;
                } else {
                    $c_result = $test_c_result / $formulaData->c_value;
                }
            } else {
                $c_result = $test_c_result;
            }
        }

        // echo $formula_test1 . '<br>';
        // echo $formula_test2 . '<br>';
        // echo $formula_operation1 . '<br>';
        // die;

        // echo "hasil : " . "<br>";

        $result_temp = 0;
        $result_1 = 0;
        $result_2 = 0;
        $result_3 = 0;

        if ($formula_test1 == 'a') {
            $result_1 = $a_result;
        } else if ($formula_test1 == 'b') {
            $result_1 = $b_result;
        } else {
            $result_1 = $c_result;
        }

        if ($formula_test2 == 'a') {
            $result_2 = $a_result;
        } else if ($formula_test2 == 'b') {
            $result_2 = $b_result;
        } else {
            $result_2 = $c_result;
        }

        if ($formula_test3 != null || $formula_test3 != '') {
            if ($formula_test3 == 'a') {
                $result_3 = $a_result;
            } else if ($formula_test3 == 'b') {
                $result_3 = $b_result;
            } else {
                $result_3 = $c_result;
            }
        }

        if ($formula_operation1 == '+') {
            $result_temp = $result_1 + $result_2;
            if ($formula_operation2 == '+') {
                $result_temp =  $result_temp + $result_3;
            } else if ($formula_operation2 == '-') {
                $result_temp =  $result_temp - $result_3;
            } else if ($formula_operation2 == '*') {
                $result_temp =  $result_temp * $result_3;
            } elseif ($formula_operation2 == '/') {
                $result_temp =  $result_temp / $result_3;
            }
        } else if ($formula_operation1 == '-') {
            $result_temp = $result_1 - $result_2;
            if ($formula_operation2 == '+') {
                $result_temp =  $result_temp + $result_3;
            } else if ($formula_operation2 == '-') {
                $result_temp =  $result_temp - $result_3;
            } else if ($formula_operation2 == '*') {
                $result_temp =  $result_temp * $result_3;
            } elseif ($formula_operation2 == '/') {
                $result_temp =  $result_temp / $result_3;
            }
        } else if ($formula_operation1 == '*') {
            $result_temp = $result_1 * $result_2;
            if ($formula_operation2 == '+') {
                $result_temp =  $result_temp + $result_3;
            } else if ($formula_operation2 == '-') {
                $result_temp =  $result_temp - $result_3;
            } else if ($formula_operation2 == '*') {
                $result_temp =  $result_temp * $result_3;
            } elseif ($formula_operation2 == '/') {
                $result_temp =  $result_temp / $result_3;
            }
        } else {
            $result_temp = $result_1 / $result_2;
            if ($formula_operation2 == '+') {
                $result_temp =  $result_temp + $result_3;
            } else if ($formula_operation2 == '-') {
                $result_temp =  $result_temp - $result_3;
            } else if ($formula_operation2 == '*') {
                $result_temp =  $result_temp * $result_3;
            } elseif ($formula_operation2 == '/') {
                $result_temp =  $result_temp / $result_3;
            }
        }

        // then, config decimal format & result status (flagging)
        $final_result = '';

        $check_format_number = \App\Test::where('id', $testId)->first();
        $format_number = $check_format_number->format_decimal;
        if ($format_number != NULL) {
            if ($format_number == 1) {
                if ($result_temp != '') {
                    $final_result = number_format($result_temp, 1, '.', ',');
                } else {
                    $final_result = $result_temp;
                }
            } elseif ($format_number == 2) {
                if ($result_temp != '') {
                    $final_result = number_format($result_temp, 2, '.', ',');
                } else {
                    $final_result = $result_temp;
                }
            } elseif ($format_number == 3) {
                if ($result_temp != '') {
                    $final_result = number_format($result_temp, 3, '.', ',');
                } else {
                    $final_result = $result_temp;
                }
            } elseif ($format_number == 4) {
                if ($result_temp != '') {
                    $final_result = number_format($result_temp, 4, '.', ',');
                } else {
                    $final_result = $result_temp;
                }
            } else {
                $final_result = $result_temp;
            }
        } else {

            if (strlen($result_temp) >= 4) {
                // bukan ribuan
                if (strpos($result_temp, ".") !== false) {
                    $final_result = (int)$result_temp;
                } else {
                    if (strpos($result_temp, ".") !== false) {
                        $final_result = (int)$result_temp;
                    } else {
                        // ribuan
                        $result_temp = number_format($result_temp);
                        $final_result = $result_temp;
                    }
                }
            } else {
                $final_result = $result_temp;
            }
        }

        $query_patient = DB::table('transactions')
            ->select('patients.birthdate')
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id');
        $patient = $query_patient->first();

        $birthdate = $patient->birthdate;
        $bornDate = $birthdate;
        $ageInDays = Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(Carbon::now());

        $range = \App\Range::where('test_id', $testId)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

        if (!$range) {
            throw new \Exception("The Range ref. doesn't exist");
        }

        $status = $this->checkResultStatusFormula($birthdate, $range, $final_result);
        $result_status = '';

        switch ($status) {
            case 'normal':
                $result_status = AnalyticController::RESULT_STATUS_NORMAL;
                break;
            case 'low':
                $result_status = AnalyticController::RESULT_STATUS_LOW;
                break;
            case 'high':
                $result_status = AnalyticController::RESULT_STATUS_HIGH;
                break;
            case 'critical':
                $result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                break;
            case 'abnormal':
                $result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                break;
            default:
                $result_status = 0;
        }

        // isCritical
        if ($result_status == 5) {
            DB::table('transactions')
                ->where('id', $transactionId)
                ->update([
                    'is_critical' => 1
                ]);
        } else {
            DB::table('transactions')
                ->where('id', $transactionId)
                ->update([
                    'is_critical' => 0
                ]);
        }

        // echo "Final Result : " . $final_result . '<br>';
        // echo "Result Status : " . $result_status . '<br>';

        DB::table('transaction_tests')
            ->where('id', $transactionTestId)
            ->update([
                'result_number' => $final_result,
                'result_status' => $result_status
            ]);
        return response()->json(['final_result' => $final_result, 'label' => $result_status]);
    }

    public function updateResultNumber($transactionTestId, Request $request)
    {
        try {
            $status = '';

            DB::beginTransaction();
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            if (empty($transactionTest)) {
                throw new \Exception("Can't update data because Transaction Test Not Found");
            }
            if ($transactionTest->verify) {
                throw new \Exception("Can't update data because it's been verified");
            }

            // result temp
            $result_temp = $request->result;
            $result = '';

            $check_format_number = \App\Test::where('id', $transactionTest->test_id)->first();
            $format_number = $check_format_number->format_decimal;
            if ($format_number != NULL) {
                if ($format_number == 1) {
                    if ($result_temp != '') {
                        $result = number_format($result_temp, 1, '.', ',');
                    } else {
                        $result = $result_temp;
                    }
                } elseif ($format_number == 2) {
                    if ($result_temp != '') {
                        $result = number_format($result_temp, 2, '.', ',');
                    } else {
                        $result = $result_temp;
                    }
                } elseif ($format_number == 3) {
                    if ($result_temp != '') {
                        $result = number_format($result_temp, 3, '.', ',');
                    } else {
                        $result = $result_temp;
                    }
                } elseif ($format_number == 4) {
                    if ($result_temp != '') {
                        $result = number_format($result_temp, 4, '.', ',');
                    } else {
                        $result = $result_temp;
                    }
                } elseif ($format_number == 404) {
                    if (strpos($result_temp, ".") !== false) {
                        $result = $result_temp;
                    } else {
                        // ribuan
                        $result_temp = number_format($result_temp);
                        $result = $result_temp;
                    }
                }
            } else {

                if (strlen($result_temp) >= 4) {
                    // bukan ribuan
                    if (strpos($result_temp, ".") !== false) {
                        $result = (int)$result_temp;
                        $result = number_format($result);
                    } else {

                        if (strpos($result_temp, ".") !== false) {
                            $result = $result_temp;
                        } else {
                            // ribuan
                            $result_temp = number_format($result_temp);
                            $result = $result_temp;
                        }
                    }
                } else {
                    if (strpos($result_temp, ".") !== false) {
                        $result = (int)$result_temp;
                    } else {
                        $result = $result_temp;
                    }
                }
            }

            $transactionTest->result_number = $result;

            $patient = $transactionTest->transaction->patient;
            $bornDate = $patient->birthdate;

            $ageInDays = Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(Carbon::now());

            $range = \App\Range::where('test_id', $transactionTest->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

            if (!$range) {
                throw new \Exception("The Range ref. doesn't exist");
            }

            $status = $this->checkResultStatus($patient, $range, $request);

            switch ($status) {
                case 'normal':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                    break;
                case 'low':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_LOW;
                    break;
                case 'high':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_HIGH;
                    break;
                case 'critical':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                    break;
                case 'abnormal':
                    $transactionTest->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                    break;
                default:
                    $transactionTest->result_status = 0;
            }

            $transactionTest->save();
            $this->updateIsCriticalStatus($transactionTest->transaction_id);
            DB::commit();

            $diff_test = '\%';

            // diff counting
            $query = DB::table('transaction_tests')
                ->select('transaction_tests.test_id', 'tests.name as test_name', 'tests.initial', 'transaction_tests.result_number')
                ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
                ->where('transaction_tests.transaction_id', $transactionTest->transaction_id)
                ->where('tests.format_diff_count', 1);
            $testData = $query->get();

            $existData = $testData->count();

            $total_diffcount = 0;

            if ($existData > 0) {
                foreach ($testData as $key => $value) {
                    if ($value->result_number != '') {
                        $diffcount = $value->result_number;
                    } else {
                        $diffcount = 0;
                    }
                    $total_diffcount += $diffcount;
                }
            }

            return response()->json(['age' => $ageInDays, 'label' => $transactionTest->result_status, 'total_diffcount' => $total_diffcount]);

            // $range = \App\Range::where('test_id')
        } catch (\Exception $e) {

            return response()->json(['message' => $e->getMessage()], 400);
            DB::rollback();
        }
    }

    private function checkResultStatus($patient, $range, $request)
    {

        $status = '';
        if ($patient->gender == 'M') {
            if ($request->result >= $range->min_male_ref && $request->result <= $range->max_male_ref) {
                $status = 'normal';
            } else if ($request->result < $range->min_crit_male || $request->result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($request->result < $range->min_male_ref) {
                $status = 'low';
            } else if ($request->result > $range->max_male_ref) {
                $status = 'high';
            }
        } else {
            if ($request->result >= $range->min_female_ref && $request->result <= $range->max_female_ref) {
                $status = 'normal';
            } else if ($request->result < $range->min_crit_female || $request->result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($request->result < $range->min_female_ref) {
                $status = 'low';
            } else if ($request->result > $range->max_female_ref) {
                $status = 'high';
            }
        }

        return $status;
    }

    private function checkResultStatusFormula($gender, $range, $result)
    {
        $status = '';
        if ($gender == 'M') {
            if ($result >= $range->min_male_ref && $result <= $range->max_male_ref) {
                $status = 'normal';
            } else if ($result < $range->min_crit_male || $result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($result < $range->min_male_ref) {
                $status = 'low';
            } else if ($result > $range->max_male_ref) {
                $status = 'high';
            }
        } else {
            if ($result >= $range->min_female_ref && $result <= $range->max_female_ref) {
                $status = 'normal';
            } else if ($result < $range->min_crit_female || $result > $range->max_crit_female) {
                $status = 'critical';
            } else if ($result < $range->min_female_ref) {
                $status = 'low';
            } else if ($result > $range->max_female_ref) {
                $status = 'high';
            }
        }

        return $status;
    }

    private function updateIsCriticalStatus($transactionId)
    {
        $transaction = \App\Transaction::findOrFail($transactionId);
        $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->get();

        $isCriticalExists = 0;
        foreach ($transactionTest as $value) {
            if ($value->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                $isCriticalExists = 1;
                break;
            }
        }

        if ($isCriticalExists) {
            $transaction->is_critical = true;
        } else {
            $transaction->is_critical = false;
        }

        $transaction->save();
    }

    public function updateResultLabel($transactionTestId, Request $request)
    {
        try {

            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            if ($transactionTest->verify) {
                throw new \Exception("Can't update data because it's been verified");
            }
            $transactionTest->result_label = $request->input('result');

            if ($request->input('result')) {
                $result = \App\Result::where('id', $request->input('result'))->first();
                // $transactionTest->result_text = $result ? $result->result : '';
                switch ($result->status) {
                    case 'normal':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                        break;
                    case 'abnormal':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                        break;
                    case 'critical':
                        $transactionTest->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                        break;
                }
            } else {
                $transactionTest->result_status = null;
            }
            $transactionTest->save();

            return response()->json(['message' => 'success', 'label' => $transactionTest->result_status]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateResultDescription($transactionTestId, Request $request)
    {
        try {
            // $result = \App\Result::where('id')
            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();
            if ($transactionTest->verify) {
                throw new \Exception("Can't update data because it's been verified");
            }
            $transactionTest->result_text = $request->input('result');
            $transactionTest->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function verifyAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach ($transactionTests as $value) {
                if ($value->result_number || $value->result_number != "" || $value->result_label || $value->result_text) {

                    // update result status for result_label type
                    if ($value->result_label) {

                        if($value->result_status == 0){
                            $checkDefaultLabel = \App\Result::selectRaw('results.id, results.status, tests.range_type')->where('test_id', $value->test_id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
                            if ($checkDefaultLabel) {
                                //$value->result_label = $checkDefaultLabel->id;
    
                                if ($checkDefaultLabel->status == 'normal') {
                                    $value->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                                } else if ($checkDefaultLabel->status == 'abnormal') {
                                    $value->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                                } else if ($checkDefaultLabel->status == 'critical') {
                                    $value->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                                } else {
                                    $value->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                                }
                            }
                        }
                        
                    }

                    $value->verify = true;
                    $value->verify_by = $user->id;
                    $value->verify_time = $now;
                    $value->save();
                }
            }

            $transaction = \App\Transaction::findOrFail($transactionId);
            $transaction->verify_status = 1;
            $transaction->verficator_id = $user->id;
            $transaction->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function verifyTest(Request $request, $transactionTestId)
    {
        try {
            $user = Auth::user();

            $transactionTest = \App\TransactionTest::findOrFail($transactionTestId);
            if (!$transactionTest->result_text && is_null($transactionTest->result_number) && !$transactionTest->result_label && !$transactionTest->result_date && $request->value == 1) {
                throw new \Exception("Unable to verify because result has not been set");
            }

            // update result status for result_label type
            if ($transactionTest->result_label) {

                if($transactionTest->result_status == 0){
                    $checkDefaultLabel = \App\Result::selectRaw('results.id, results.status, tests.range_type')->where('test_id', $transactionTest->test_id)->join('tests', 'tests.id', '=', 'results.test_id')->where('is_default', 1)->where('tests.range_type', '=', 'label')->first();
                    if ($checkDefaultLabel) {
                        // $value->result_label = $checkDefaultLabel->id;
    
                        if ($checkDefaultLabel->status == 'normal') {
                            $transactionTest->result_status = AnalyticController::RESULT_STATUS_NORMAL;
                        } else if ($checkDefaultLabel->status == 'abnormal') {
                            $transactionTest->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                        } else if ($checkDefaultLabel->status == 'critical') {
                            $transactionTest->result_status = AnalyticController::RESULT_STATUS_CRITICAL;
                        } else {
                            $transactionTest->result_status = AnalyticController::RESULT_STATUS_ABNORMAL;
                        }
                    }
                } 
            }

            $transactionTest->verify = $request->value;
            $transactionTest->verify_by = $user->id;
            $transactionTest->verify_time = Carbon::now();

            if ($request->value == 0) {
                $transactionTest->validate = 0;
                $transactionTest->verify_by = null;
                $transactionTest->verify_time = null;
                $transactionTest->validate_by = null;
                $transactionTest->validate_time = null;
            }

            $transactionTest->save();

            $transaction = \App\Transaction::findOrFail($transactionTest->transaction_id);
            $transaction->verify_status = 1;
            $transaction->verficator_id = $user->id;;
            if ($request->value == 0) {
                $transaction->verify_status = 0;
                $transaction->verficator_id = 0;
            }

            $transaction->save();

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function unverifyAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->update([
                'verify' => false,
                'verify_by' => null,
                'verify_time' => null,
                'validate' => 0,
                'validate_by' => null,
                'validate_time' => null
            ]);

            $transaction = \App\Transaction::where('id', $transactionId)->update([
                'verify_status' => 0,
                'verficator_id' => 0
            ]);

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function validateAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)->get();

            foreach ($transactionTests as $value) {
                if ($value->verify) {
                    $value->validate = true;
                    $value->validate_by = $user->id;
                    $value->validate_time = $now;
                    $value->save();
                }
            }

            $transaction = \App\Transaction::findOrFail($transactionId);
            $transaction->validate_status = 1;
            $transaction->validator_id = $user->id;
            $transaction->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function validateTest(Request $request, $transactionTestId)
    {
        try {
            $user = Auth::user();

            $transactionTest = \App\TransactionTest::findOrFail($transactionTestId);
            $transactionTest->validate = $request->value;
            if ($request->value == 0) {
                $transactionTest->validate_by = null;
                $transactionTest->validate_time = null;
            }

            $transactionTest->validate_by = $user->id;
            $transactionTest->validate_time = Carbon::now();

            $transactionTest->save();

            $transaction = \App\Transaction::findOrFail($transactionTest->transaction_id);
            $transaction->validate_status = 1;
            $transaction->validator_id = $user->id;
            if ($request->value == 0) {
                $transaction->validate_status = 0;
                $transaction->validator_id = 0;
            }

            $transaction->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function unvalidateAll($transactionId)
    {
        try {
            $user = Auth::user();
            $now = Carbon::now();
            $transactionTests = \App\TransactionTest::where('transaction_id', $transactionId)
                ->where('verify', 1)->update([
                    'validate' => false,
                    'validate_by' => null,
                    'validate_time' => null
                ]);

            $transaction = \App\Transaction::where('id', $transactionId)->update([
                'validate_status' => 0
            ]);

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateTestMemo(Request $request)
    {
        try {
            $transactionTestId = $request->transaction_test_id;
            $memo = $request->memo;

            $transactionTest = \App\TransactionTest::where('id', $transactionTestId)->first();

            $transactionTest->memo_test = $memo;
            $transactionTest->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function updateMemoResult(Request $request)
    {
        try {
            $transactionId = $request->transaction_id;
            $memoResult = $request->memo_result;

            if ($memoResult != '') {
                $is_print_memo = 1;
            } else {
                $is_print_memo = 0;
            }

            $transaction = \App\Transaction::where('id', $transactionId)->first();
            $transaction->memo_result = $memoResult;
            $transaction->is_print_memo = $is_print_memo;
            $transaction->save();

            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function checkCriticalTest($transactionId)
    {
        $transactionTests = \App\TransactionTest::selectRaw('transaction_tests.*, results.result as res_label')
            ->leftJoin('results', 'results.id', 'transaction_tests.result_label')->where('transaction_id', $transactionId)
            ->where('result_status', AnalyticController::RESULT_STATUS_CRITICAL)
            ->where('report_time', '=', null)
            ->whereIn('verify', [0, null])->get();

        if (count($transactionTests) > 0) {
            return response()->json(['data' => $transactionTests, 'exists' => true]);
        } else {
            return response()->json(['data' => $transactionTests, 'exists' => false]);
        }
    }

    public function reportCriticalTest(Request $request)
    {
        $criticalTestIds = explode(',', $request->transaction_test_ids);
        $reportTo = $request->report_to;
        $reportBy = $request->report_by;

        $tests = \App\TransactionTest::whereIn('id', $criticalTestIds)->update([
            'report_status' => 1,
            'report_by' => $reportBy,
            'report_to' => $reportTo,
            'report_time' => Carbon::now()->toDateTimeString()
        ]);
        return response()->json(['data' => $request->all()]);
    }

    public function checkActionBtnTestStatus($transactionId)
    {
        $unverAndValAll = \App\TransactionTest::where('transaction_id', $transactionId)->where('verify', 1)->exists();
        $unvalAll = \App\TransactionTest::where('transaction_id', $transactionId)->where('validate', 1)->exists();

        return response()->json(['unver_and_val_all' => $unverAndValAll, 'unval_all' => $unvalAll]);
    }

    public function goToPostAnalytics($transactionId)
    {
        try {
            $transaction = \App\Transaction::findOrFail($transactionId);

            if ($transaction->status == '2') {
                throw new \Exception("Transaction has been moved to analytic");
            }

            if ($transaction->no_lab == '' || $transaction->no_lab == null) {
                throw new \Exception("No Lab has not been set");
            }

            $allAnalzerSet = false;
            $transactionTest = \App\TransactionTest::where('transaction_id', $transactionId)->get();


            foreach ($transactionTest as $test) {
                if ($test->verify == 0 || $test->verify == NULL) {
                    throw new \Exception('You need to verify all test');
                }

                if ($test->validate == 0 || $test->validate == NULL) {
                    throw new \Exception('You need to validate all test');
                }

                if ($test->result_status == 5 && $test->report_status == 0) {
                    throw new \Exception('You need to report critical test');
                }
            }

            DB::table('transactions')
                ->where('id', $transactionId)
                ->update(
                    [
                        'status' => AnalyticController::STATUS_POST_ANALYTIC,
                        'post_time' => Carbon::now()->toDateTimeString()
                    ]
                );

            $transactions = \App\Transaction::getTransactionData($transactionId);
            $no_order = $transaction->no_order;

            $ageInDays = Carbon::createFromFormat('Y-m-d', $transactions->patient_birthdate)->diffInDays(Carbon::now());

            $where = array(
                "transaction_id" => $transactionId,
            );
            $transactions_test = \App\TransactionTest::getTransactionTestData($where);

            // echo '<pre>';
            // print_r($transactions_test);
            // die;

            DB::delete('delete from finish_transactions where transaction_id = ?', [$transactionId]);
            DB::delete('delete from finish_transaction_tests where transaction_id = ?', [$transactionId]);

            if ($transactions->memo_result != null) {
                $is_print_memo = 1;
            } else {
                $is_print_memo = 0;
            }

            $data_insert_transactions = [
                'transaction_id' => $transactionId,
                'patient_id' => $transactions->patient_id,
                'patient_name' => $transactions->patient_name,
                'patient_medrec' => $transactions->patient_medrec,
                'patient_address' => $transactions->patient_address,
                'patient_phone' => $transactions->patient_phone,
                'patient_email' => $transactions->patient_email,
                'patient_gender' => $transactions->patient_gender,
                'patient_birthdate' => $transactions->patient_birthdate,
                'room_id' => $transactions->room_id,
                'room_name' => $transactions->room_name,
                'doctor_id' => $transactions->doctor_id,
                'doctor_name' => $transactions->doctor_name,
                'insurance_id' => $transactions->insurance_id,
                'insurance_name' => $transactions->insurance_name,
                'analyzer_id' => $transactions->analyzer_id,
                'analyzer_name' => $transactions->analyzer_name,
                'type' => $transactions->type,
                'no_lab' => $transactions->no_lab,
                'note' => $transactions->note,
                'status' => $transactions->status,
                'cito' => $transactions->cito,
                'check' => $transactions->check,
                'draw' => $transactions->draw,
                'result_status' => $transactions->result_status,
                'verify_status' => $transactions->verify_status,
                'validate_status' => $transactions->validate_status,
                'report_status' => $transactions->report_status,
                'checkin_time' => $transactions->checkin_time,
                'checkin_by' => $transactions->checkin_by,
                'checkin_by_name' => $transactions->checker_name,
                'created_time' => $transactions->created_time,
                'analytic_time' => $transactions->analytic_time,
                'post_time' => $transactions->post_time,
                'memo_result' => $transactions->memo_result,
                'print' => 0,
                'get_status' => $transactions->get_status,
                'verficator_id' => $transactions->verficator_id,
                'verficator_name' => $transactions->verficator_name,
                'validator_id' => $transactions->validator_id,
                'validator_name' => $transactions->validator_name,
                'shipper' => $transactions->shipper,
                'receiver' => $transactions->receiver,
                'no_order' => $transactions->no_order,
                'is_igd' => $transactions->is_igd,
                'is_print_memo' => $is_print_memo,
                'created_at' => Carbon::now()
            ];
            $finish_transaction_id = DB::table('finish_transactions')
                ->insertGetId($data_insert_transactions);

            $data_insert_transactions_test = array();
            foreach ($transactions_test as $key => $value) {
                // code...
                // if (strlen($value->result_number) >= 4) {
                //     $result_number = number_format($value->result_number);
                // } else {
                //     $result_number = $value->result_number;
                // }
                // code...
                $dt = [
                    "finish_transaction_id" => $finish_transaction_id,
                    "transaction_id" => $transactionId,
                    "test_id" => $value->test_id,
                    "test_name" => $value->test_name,
                    "package_id" => $value->package_id,
                    "package_name" => $value->package_name,
                    "price_id" => $value->price_id,
                    "group_id" => $value->group_id,
                    "group_name" => $value->group_name,
                    "analyzer_id" => $value->analyzer_id,
                    "mark_duplo" => $value->mark_duplo,
                    "analyzer_name" => $value->analyzer_name,
                    "specimen_id" => $value->specimen_id,
                    "specimen_name" => $value->specimen_name,
                    "type" => $value->type,
                    "result_number" => $value->result_number,
                    "result_label" => $value->result_label,
                    "result_text" => $value->result_text,
                    "draw" => $value->draw,
                    "draw_by" => $value->draw_by,
                    "draw_by_name" => $value->draw_by_name,
                    "draw_memo" => $value->draw_memo,
                    "undraw_memo" => $value->undraw_memo,
                    "result_status" => $value->result_status,
                    "draw_time" => $value->draw_time,
                    "input_time" => $value->input_time,
                    "verify" => $value->verify,
                    "validate" => $value->validate,
                    "print_package_name" => $value->print_package_name,
                    "report_status" => $value->report_status,
                    "report_by" => $value->report_by,
                    "report_to" => $value->report_to,
                    "memo_test" => $value->memo_test,
                    "verify_by" => $value->verify_by,
                    "verify_by_name" => $value->verify_by_name,
                    "validate_by" => $value->validate_by,
                    "validate_by_name" => $value->validate_by_name,
                    "verify_time" => $value->verify_time,
                    "validate_time" => $value->validate_time,
                    "is_print" => 1,
                    "report_time" => $value->report_time,
                    "sub_group" => $value->sub_group,
                    "initial" => $value->initial,
                    "unit" => $value->unit,
                    "volume" => $value->volume,
                    "normal_notes" => $value->normal_notes,
                    "general_code" => $value->general_code,
                    "sequence" => $value->sequence,
                ];

                //set value global result
                if ($value->result_number || ($value->result_number == 0)) {
                    $dt['global_result'] = $value->result_number;
                }
                if ($value->result_label && $value->result_final) {
                    $dt['global_result'] = $value->result_final;
                }
                if ($value->result_text) {
                    $dt['global_result'] = $value->result_text;
                }
                // else {
                //     $dt['global_result'] = "-";
                // }

                //set normal value
                if ($transactions->patient_gender == 'F') {
                    if ($value->result_number || ($value->result_number == 0)) {
                        $ranges = \App\Range::where('test_id', $value->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();
                        if ($ranges) {
                            $dt['normal_value'] = $ranges->normal_female;
                        } else {
                            $dt['normal_value'] = $value->normal_notes;
                        }
                    } else {
                        $dt['normal_value'] = $value->normal_notes;
                    }
                } else if ($transactions->patient_gender == 'M') {
                    if ($value->result_number || ($value->result_number == 0)) {
                        $ranges = \App\Range::where('test_id', $value->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();
                        if ($ranges) {
                            $dt['normal_value'] = $ranges->normal_male;
                        } else {
                            $dt['normal_value'] = $value->normal_notes;
                        }
                    } else {
                        $dt['normal_value'] = $value->normal_notes;
                    }
                }

                // $dt['normal_value'] = $value->normal_notes;

                if ($value->result_status == AnalyticController::RESULT_STATUS_NORMAL) {
                    $dt['result_status_label'] = "Normal";
                } else if ($test->result_status == AnalyticController::RESULT_STATUS_LOW || $test->result_status == AnalyticController::RESULT_STATUS_HIGH || $test->result_status == AnalyticController::RESULT_STATUS_ABNORMAL) {
                    $dt['result_status_label'] = "Abnormal";
                } else if ($test->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                    $dt['result_status_label'] = "Critical";
                } else {
                    $dt['result_status_label'] = "-";
                }

                array_push($data_insert_transactions_test, $dt);
            }
            if ($data_insert_transactions_test) {
                $finish_transaction_id = DB::table('finish_transaction_tests')
                    ->insert($data_insert_transactions_test);
            }

            if ($no_order != NULL) {
                app(\App\Http\Controllers\ApiController::class)->sendResult($no_order);
            }

            return response()->json(['message' => 'Valid', 'valid' => true]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'valid' => false], 400);
        }
    }

    public function loadHistoryTest($id)
    {
        $data = null;
        $checkPatient = DB::table('transaction_tests')->select('*')
            ->leftJoin('transactions', 'transactions.id', 'transaction_tests.transaction_id')
            ->where('transaction_tests.id', $id)->first();
        if ($checkPatient) {

            $query = DB::table('finish_transaction_tests')
                ->select('finish_transaction_tests.test_name', 'finish_transaction_tests.draw_time', 'finish_transaction_tests.result_number', 'finish_transaction_tests.result_label', 'finish_transaction_tests.result_text', 'finish_transaction_tests.global_result')
                ->leftJoin('finish_transactions', 'finish_transactions.id', 'finish_transaction_tests.finish_transaction_id')
                ->where('finish_transaction_tests.test_id', $checkPatient->test_id)
                ->where('finish_transactions.patient_id', $checkPatient->patient_id)
                ->where('finish_transactions.status', '>=', AnalyticController::STATUS_POST_ANALYTIC);
            $data = $query->get();

            if ($data) {
                foreach ($data as $key => $value) {
                    $data[$key]->result_final = $value->global_result;
                    $data[$key]->test_date = "-";
                    if ($data[$key]->draw_time) {
                        $data[$key]->test_date = date('d/m/Y', strtotime($data[$key]->draw_time));
                    }
                }
                // dd($data);
            }
        }


        return response()->json($data);
    }

    public function deleteTransactionTest($transactionId)
    {
        try {
            $transactionTest = \App\TransactionTest::where('id', $transactionId)->first();
            if ($transactionTest) {
                if ($transactionTest->verify) {
                    throw new \Exception("You can't delete because the test is verified");
                } else if ($transactionTest->validate) {
                    throw new \Exception("You can't delete because the test is validate");
                } else {
                    $data = \App\TransactionTest::findOrFail($transactionId);
                    //check if have duplo or not
                    if ($data) {
                        $duplo =  \App\TransactionTest::where('transaction_id', $data->transaction_id)->where('test_id', $data->test_id)->where('mark_duplo', 1)->first();
                        if ($duplo) {
                            $duplo->mark_duplo = 0; //reset to not duplo
                            $duplo->save();
                        }
                    }
                    $data->delete();
                }
            } else {
                throw new \Exception("You can't delete because the test is validate");
            }
            return response()->json(['message' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function markDuplo(Request $request)
    {
        try {
            $transaction_test_id = $request->transaction_test_id;
            $test_id = $request->test_id;
            $transaction_id = $request->transaction_id;
            $analyzer_id = $request->analyzer_id;

            //check is duplo or not
            $checkduplo = \App\TransactionTest::where('transaction_id', $transaction_id)->where('test_id', $test_id)->where('mark_duplo', 1)->first();

            if ($checkduplo) {
                throw new \Exception("This test already mark as duplo");
            }
            $transactionTest = \App\TransactionTest::where('id', $transaction_test_id)->first();
            if ($transactionTest) {
                if ($transactionTest->verify == 1) {
                    throw new \Exception("You can't mark as duplo because the test is validate");
                } else {

                    //replicate new data duplo
                    $newTransactionTest = $transactionTest->replicate();
                    $newTransactionTest->analyzer_id = $analyzer_id;
                    $newTransactionTest->mark_duplo = 2;
                    $newTransactionTest->save();

                    //mark duplo old data
                    $transactionTest->mark_duplo = 1;
                    $transactionTest->save();

                    return response()->json(['message' => 'success']);
                }
            } else {
                throw new \Exception("You can't mark as Duplo, because Test Not Exist");
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function diffCounting($transactionId)
    {
        $diff_test = '\%';

        // diff counting
        $query = DB::table('transaction_tests')
            ->select('transaction_tests.test_id', 'tests.name as test_name', 'tests.initial', 'transaction_tests.result_number')
            ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
            ->where('transaction_tests.transaction_id', $transactionId)
            ->where('tests.format_diff_count', 1);
        $testData = $query->get();

        $existData = $testData->count();

        $total_diffcount = 0;

        if ($existData > 0) {
            foreach ($testData as $key => $value) {
                if ($value->result_number != '') {
                    $diffcount = $value->result_number;
                } else {
                    $diffcount = 0;
                }
                $total_diffcount += $diffcount;
            }
        }
        return response()->json($total_diffcount);
    }

    public function printPackageName(Request $request, $transactionId, $packageId)
    {
        try {
            $value = $request->value;

            $query = DB::table('transaction_tests')
                ->where('transaction_id', $transactionId)
                ->where('package_id', $packageId);
            $transactionTest = $query->get();

            if ($value == 1) {
                $updateData = array('print_package_name' => 1);
            } else {
                $updateData = array('print_package_name' => 0);
            }

            DB::table('transaction_tests')
                ->where('transaction_id', $transactionId)
                ->where('package_id', $packageId)
                ->update($updateData);

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
