<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InterfacingController extends Controller
{
    public function insert(Request $request)
    {
        $results = $request->input("lists");

        if ($results != null) {
            //foreach but data only one
            foreach ($results as $result) {
                $lab_no = $result['patientId'];
                $analyzer_id = $result['analyzerId'];
                // $transaction = DB::table('transactions')->where('no_lab', $lab_no)->first();
                $transaction = DB::table('transactions')
                    ->select('patients.gender', 'patients.birthdate', 'transactions.*', 'rooms.auto_draw')
                    ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
                    ->leftJoin('rooms', 'transactions.patient_id', '=', 'patients.id')
                    ->where('no_lab', $lab_no)->first();

                if ($transaction) {
                    $born = Carbon::createFromFormat('Y-m-d', $transaction->birthdate);
                    $ageInDays = Carbon::createFromFormat('Y-m-d', $transaction->birthdate)->diffInDays(Carbon::now());
                    $birthdate = $born->diff(Carbon::now())->format('%yY / %mM / %dD');
                    $birthday = $born->diff(Carbon::now())->days;

                    foreach ($result['results'] as $test) {
                        // if($test['name']=='GluP'){
                        //     $interfacing_test = DB::table('interfacings')->select('transaction_tests.test_id', 'tests.name', 'tests.initial', 'interfacings.*')
                        //         ->leftJoin('tests', 'tests.id', '=', 'interfacings.test_id')
                        //         ->leftJoin('transaction_tests', 'interfacings.test_id', '=', 'transaction_tests.test_id')
                        //         ->where('transaction_tests.transaction_id', $transaction->id)
                        //         ->where('interfacings.code', $test['name'])
                        //         ->where('interfacings.analyzer_id', $analyzer_id)->first();
                        // }else {
                        // }
                        $interfacing_test = DB::table('interfacings')->where('code', $test['name'])->where('analyzer_id', $analyzer_id)->first();

                        if ($interfacing_test != null) {
                            $transaction_id = $transaction->id;
                            $test_id = $interfacing_test->test_id;
                            $trans_tests = DB::table('transaction_tests')
                                ->where('test_id', $test_id)
                                ->where('transaction_id', $transaction_id)
                                ->first();

                            $result_value = $test['result'];
                            $result = '';

                            //hardcode handle for previous version ok jenis kelamin/gender
                            if ($transaction->gender == "L") {
                                $transaction->gender = "M";
                            } elseif ($transaction->gender == "P") {
                                $transaction->gender = "F";
                            }

                            $test = DB::table('tests')->select('tests.*', 'prices.id as price_id')->leftJoin('prices', 'prices.test_id', '=', 'tests.id')->where('tests.id', $test_id)->first();
                            $range = \App\Range::where('test_id', $test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

                            if ($range) {
                                $status = $this->checkResultStatus($transaction->gender, $range, $result_value);

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
                                    default:
                                        $result_status = 0;
                                }

                                if (!$trans_tests) {
                                    $now = Carbon::now();
                                    $data_test = array(
                                        "transaction_id" => $transaction_id,
                                        "test_id" => $test_id,
                                        "price_id" => $test->price_id,
                                        "group_id" => $test->group_id,
                                        "type" => 'single', //from interfacing test always single
                                        "package_id" => NULL,
                                        "input_time" => $now,
                                    );
                                    if ($transaction->auto_draw == 1) {
                                        $data_test['draw'] = true;
                                        $data_test['draw_time'] = $now;
                                    }
                                }

                                $check_format_number = \App\Test::where('id', $test_id)->first();
                                $format_number = $check_format_number->format_decimal;

                                /*check type*/
                                if ($test->range_type == 'number') {
                                    if ($trans_tests) {

                                        // check format number
                                        if ($format_number != NULL) {
                                            if ($format_number == 1) {
                                                if ($result_value != '') {
                                                    $result = number_format($result_value, 1, '.', ',');
                                                } else {
                                                    $result = $result_value;
                                                }
                                            } elseif ($format_number == 2) {
                                                if ($result_value != '') {
                                                    $result = number_format($result_value, 2, '.', ',');
                                                } else {
                                                    $result = $result_value;
                                                }
                                            } elseif ($format_number == 3) {
                                                if ($result_value != '') {
                                                    $result = number_format($result_value, 3, '.', ',');
                                                } else {
                                                    $result = $result_value;
                                                }
                                            } elseif ($format_number == 4) {
                                                if ($result_value != '') {
                                                    $result = number_format($result_value, 4, '.', ',');
                                                } else {
                                                    $result = $result_value;
                                                }
                                            } elseif ($format_number == 404) {
                                                if (strpos($result_value, ".") !== false) {
                                                    $result = $result_value;
                                                } else {
                                                    // ribuan
                                                    $result_value = number_format($result_value);
                                                    $result = $result_value;
                                                }
                                            }
                                        } else {


                                            if (strlen($result_value) >= 4) {
                                                // bukan ribuan
                                                if (strpos($result_value, ".") !== false) {
                                                    $result = (int)$result_value;
                                                    $result = number_format($result);
                                                } else {

                                                    if (strpos($result_value, ".") !== false) {
                                                        $result = $result_value;
                                                    } else {
                                                        // ribuan
                                                        $result_value = number_format($result_value);
                                                        $result = $result_value;
                                                    }
                                                }
                                            } else {
                                                if (strpos($result_value, ".") !== false) {
                                                    $result = (int)$result_value;
                                                } else {
                                                    $result = $result_value;
                                                }
                                            }
                                        }

                                        $trans_tests = DB::table('transaction_tests')
                                            ->where('test_id', $test_id)
                                            ->where('transaction_id', $transaction_id)
                                            ->where('mark_duplo', 0)
                                            ->update([
                                                'result_number' => $result,
                                                'result_status' => $result_status,
                                                'input_time' => Carbon::now()->toDateTimeString()
                                            ]);
                                    } else {
                                        $data_test['result_number'] = $result_value;
                                        $data_test['result_status'] = $result_status;
                                        $trans_tests = true;
                                        // $$trans_tests = DB::table('transaction_tests')->insert($data_test);
                                    }
                                }
                            } else {
                                if ($test->range_type == 'label') {
                                    if ($trans_tests) {
                                        $query = DB::table('results')
                                            ->select('results.*')
                                            ->where('test_id', $test_id)
                                            ->where('min_range', '<=', $result_value)
                                            ->where('max_range', '>=', $result_value);
                                        $master_result = $query->first();

                                        if ($master_result != null) {

                                            // set result_status
                                            if ($master_result->status == 'normal') {
                                                $result_status = 1;
                                            } else if ($master_result->status == 'abnormal') {
                                                $result_status = 2;
                                            } else {
                                                $result_status = 3;
                                            }

                                            /*update value ke t_transaction_test ke field result_label*/
                                            $trans_tests = DB::table('transaction_tests')
                                                ->where('test_id', $test_id)
                                                ->where('transaction_id', $transaction_id)
                                                ->update([
                                                    'result_label' => $master_result->id,
                                                    'result_status' => $result_status,
                                                    'input_time' => Carbon::now()->toDateTimeString()
                                                ]);
                                        } else {
                                            return response()->json('Result from Analyzer Not Found in LICA');
                                        }
                                    }
                                }
                                // return response()->json('range not found');
                            }
                        } else {
                            return response()->json('Interfacing Not Found');
                        }
                    }
                } else {
                    return response()->json('No Lab or Patients Not Found');
                }
            }
            return response()->json($trans_tests);
        } else {
            return response()->json('No Results');
        }
    }

    private function checkResultStatus($gender, $range, $result)
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
            } else if ($result < $range->min_crit_female || $result > $range->max_crit_male) {
                $status = 'critical';
            } else if ($result < $range->min_female_ref) {
                $status = 'abnormal';
            } else if ($result > $range->max_female_ref) {
                $status = 'high';
            }
        }

        return $status;
    }
}
