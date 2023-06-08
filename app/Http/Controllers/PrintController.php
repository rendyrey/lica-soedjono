<?php

namespace App\Http\Controllers;

use Illuminate\http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade as PDF;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
// require './vendor/picqer/php-barcode-generator/src/BarcodeGeneratorHTML.php';
// require_once base_path().'./vendor/autoload.php';
use Picqer;
use File;

class PrintController extends Controller
{
    // public function index(){
    // 	return view('pages.prints.viewPrint');
    // }

    const COVID_PACKAGE_NAME = "RAPID ANTIBODI SARS COV 2";

    public function printPreview($id)
    {

        $query = DB::table('transactions')
            ->select('transactions.*', 'patients.name as patient_name', 'patients.birthdate as patient_birth')
            ->leftJoin('patients', 'transactions.master_patient_id', '=', 'patients.id')
            ->where('transactions.id', '=', $id);
        $patient_join = $query->get();

        // dd($data_package_test);

        $data['patients_join'] = $patient_join;

        return view('pages.prints.printPreview', $data);
    }

    public function printConsent($id)
    {
        $user = Auth::user()->name;
        $patient = DB::table('transactions')
            ->select('transactions.no_lab as no_lab', 'transactions.shipper', 'transactions.receiver', DB::raw('DATE(transactions.created_time) as created_date'), 'patients.name as patient_name', 'patients.birthdate as patient_birth', 'patients.address as patient_address', 'patients.phone as patient_phone', 'patients.medrec as patient_medrec', 'transactions.master_room_id as room')
            ->leftJoin('patients', 'transactions.master_patient_id', '=', 'patients.id')
            // ->leftJoin('master_room', 'transactions.master_room_id', '=', 'master_room.id')
            ->where('transactions.id', '=', $id)
            ->first();
        // dd($patient->room);

        $rooms = DB::table('master_room')->where('id', $patient->room)->first();
        // dd($rooms);

        $born = Carbon::createFromFormat('Y-m-d', $patient->patient_birth);
        $birthdate = $born->diff(Carbon::now())->format('%y Tahun / %m Bulan / %d Hari');

        $roman = $this->integerToRoman(Carbon::now()->format('m'));
        $today = Carbon::now()->format('d M Y');

        $tests = DB::table('transactions')
            ->select('draw_time', 'master_tests.name as test_name', 'master_specimens.name as specimen_name', 'volume', 'unit')
            ->leftJoin('transaction_tests', 'transaction_id', '=', 'transactions.id')
            ->leftJoin('master_tests', 'master_test_id', '=', 'master_tests.id')
            ->leftJoin('master_specimens', 'master_tests.master_specimen_id', '=', 'master_specimens.id')
            ->where('transactions.id', '=', $id)
            ->get();
        $samples = DB::table('transactions')
            ->select(DB::raw('master_specimens.id as specimen_id, master_specimens.name as specimen_name, sum(master_tests.volume) as total_vol, master_tests.unit'))
            ->leftJoin('transaction_tests', 'transaction_id', '=', 'transactions.id')
            ->leftJoin('master_tests', 'master_test_id', '=', 'master_tests.id')
            ->leftJoin('master_specimens', 'master_tests.master_specimen_id', '=', 'master_specimens.id')
            ->groupBy('master_specimens.id')
            ->where('transactions.id', '=', $id)
            ->get();
        $specimen_array = [];
        $specimen_labels = '';
        $test_array = [];
        $test_labels = '';
        $draw_time = '';
        $total_volume = 0;

        foreach ($tests as $test) {
            if (!in_array($test->test_name, $test_array)) {
                $test_array[] = $test->test_name;

                if ($test_labels == '') {
                    $test_labels = ucwords(strtolower($test->test_name));
                } else {
                    $test_labels = $test_labels . ', ' . ucwords(strtolower($test->test_name));
                }
            }

            if (!in_array($test->specimen_name, $specimen_array)) {
                $specimen_array[] = $test->specimen_name;

                if ($specimen_labels == '') {
                    $specimen_labels = ucwords(strtolower($test->specimen_name));
                } else {
                    $specimen_labels = $specimen_labels . ', ' . ucwords(strtolower($test->specimen_name));
                }
            }

            if ($draw_time == '') {
                $draw_time = $test->draw_time;
            } else {
                if ($draw_time < $test->draw_time) {
                    $draw_time = $test->draw_time;
                }
            }

            $total_volume = $total_volume + $test->volume;
        }

        if ($draw_time != '') {
            $draw_time = Carbon::createFromFormat('Y-m-d H:i:s', $draw_time)->format('H:i / d M Y');
        }

        $data = [
            'user' => $user,
            'patient' => $patient,
            'birth_label' => $birthdate,
            'today' => $today,
            'roman' => $roman,
            'test_labels' => $test_labels,
            'specimen_labels' => $specimen_labels,
            'draw_time' => $draw_time,
            'total_volume' => $total_volume,
            'samples' => $samples,
            'rooms' => $rooms
        ];

        return view('pages.prints.printConsent', $data);
    }

    function integerToRoman($integer)
    {
        // Convert the integer into an integer (just to make sure)
        $integer = intval($integer);
        $result = '';

        // Create a lookup array that contains all of the Roman numerals.
        $lookup = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        );

        foreach ($lookup as $roman => $value) {
            // Determine the number of matches
            $matches = intval($integer / $value);

            // Add the same number of characters to the string
            $result .= str_repeat($roman, $matches);

            // Set the integer to be the remainder of the integer and the value
            $integer = $integer % $value;
        }

        // The Roman numeral should be built, return it
        return $result;
    }

    public function exportPDF($id, $testlist, $memostat)
    {
        $testArr = json_decode($testlist);
        $judul = "Print Result";
        $data['title'] = $judul;

        DB::table('transactions')
            ->where('id', $id)
            ->update([
                'status' => 4,
                'print' => DB::raw('print + 1'),
            ]);

        $query_patient = DB::table('finish_transactions')
            ->select('finish_transactions.*', 'transactions.created_time', 'transactions.checkin_time', 'transactions.analytic_time', 'transactions.post_time', 'transactions.print')
            ->leftJoin('transactions', 'transactions.id', '=', 'finish_transactions.transaction_id')
            ->where('finish_transactions.transaction_id', '=', $id);

        $query_transaction = DB::table('transactions')
            // ->select('created_time', 'analytic_time')
            ->where('id', $id)->first();
        // dd($testArr);

        $query_tests = DB::table('finish_transaction_tests')->select('finish_transaction_tests.*')
            // ->leftJoin('master_tests', 'finish_transaction_tests')
            ->where('transaction_id', '=', $id)
            ->whereIn('finish_transaction_tests.id', $testArr)

            ->orderBy('sequence', 'asc')
            ->orderBy('sub_group', 'asc');

        $query_group = DB::table('finish_transaction_tests')
            ->select('group_name')
            ->where('transaction_id', '=', $id)
            ->orderBy('sequence', 'asc')
            ->groupBy('group_name')
            ->get();

        $query_subgroup = DB::table('finish_transaction_tests')
            ->selectRaw("CASE WHEN sub_group IS NULL OR sub_group = '' THEN '' ELSE sub_group END as sub_group_grouped")
            ->where('transaction_id', '=', $id)
            ->groupBy('sub_group_grouped')
            ->get();


        // dd($query_transaction);
        $reg_time = Carbon::parse($query_transaction->created_time)->format('d/m/Y H:i');
        $analitik_time = Carbon::parse($query_transaction->analytic_time)->format('d/m/Y H:i');
        $print_time = Carbon::now()->format('d/m/Y H:i');

        $patient = $query_patient->first();
        $tests = $query_tests->get();

        $bulan_1 = Carbon::now()->format('F');
        $tgl = Carbon::now()->format('d');
        $tahun = Carbon::now()->format('Y');

        if ($bulan_1 == "January") {
            $bulan_st = "Januari";
        } else if ($bulan_1 == "February") {
            $bulan_st = "Februari";
        } else if ($bulan_1 == "March") {
            $bulan_st = "Maret";
        } else if ($bulan_1 == "April") {
            $bulan_st = "April";
        } else if ($bulan_1 == "May") {
            $bulan_st = "Mei";
        } else if ($bulan_1 == "June") {
            $bulan_st = "Juni";
        } else if ($bulan_1 == "July") {
            $bulan_st = "Juli";
        } else if ($bulan_1 == "August") {
            $bulan_st = "Agustus";
        } else if ($bulan_1 == "September") {
            $bulan_st = "September";
        } else if ($bulan_1 == "October") {
            $bulan_st = "Oktober";
        } else if ($bulan_1 == "November") {
            $bulan_st = "November";
        } else if ($bulan_1 == "December") {
            $bulan_st = "Desember";
        }

        $copy = $query_transaction->print;
        $user = Auth::user();
        DB::table('log_print')
            ->insert([
                'id_transaction' => $id,
                'printed_at' => Carbon::now()->toDateTimeString(),
                'copied' => $copy,
                'print_by' => $user->id
            ]);

        $tgl_result = $tgl . " " . $bulan_st . " " . $tahun;
        $sorted_test = [];
        $group_array = [];
        // print_r($query_group); die;
        foreach ($query_group as $group) {
            $group_array[$group->group_name] = [];
            array_push($group_array[$group->group_name], $group->group_name);
            foreach ($query_subgroup as $subgroup) {
                foreach ($tests as $test) {
                    if (($test->sub_group == $subgroup->sub_group_grouped) && ($test->group_name == $group->group_name)) {
                        if (($subgroup->sub_group_grouped != '') && (!in_array($subgroup->sub_group_grouped, $group_array[$group->group_name]))) {
                            array_push($group_array[$group->group_name], $subgroup->sub_group_grouped);
                        }
                        array_push($group_array[$group->group_name], $test->test_name);
                        array_push($sorted_test, $test);
                        // echo $group->group_name ." - ". $subgroup->sub_group_grouped ." - ". $test->test_name . "<br>";
                    }
                }
            }
        }
        // foreach($group_array as $arr){
        // 	foreach($arr as $t){
        // 		echo $t . "<br>";
        // 	}
        // }
        // die;
        // dd(count($tests) + count($query_group) + count($query_subgroup));
        // dd($tgl_result);
        $data = [
            'patient' => $patient,
            'tests' => $sorted_test,
            'groups' => $group_array,
            'reg_time' => $reg_time,
            'analitik_time' => $analitik_time,
            'print_time' => $print_time,
            'tgl_result' => $tgl_result,
            'memo_stat' => $memostat,
        ];

        // dd($data['patient']);
        $filename = str_replace(' ', '_', strtoupper($patient->patient_name)) . '_' . $patient->patient_medrec . '_' . 'LICA.pdf';
        $pdf = PDF::loadview('pages.prints.printResult', $data);
        return $pdf->download($filename);
    }

    public function hasilTest($id)
    {
        // $id = 31;
        // dd($id);
        // $testArr = json_decode($testlist);
        $judul = "Print Result";
        $data['title'] = $judul;

        DB::table('finish_transactions')
            ->where('id', $id)
            ->update([
                'status' => 4,
                'print' => DB::raw('print + 1'),
            ]);

        $transaction = DB::table('finish_transactions')
            ->select('finish_transactions.*')
            ->where('finish_transactions.id', '=', $id)->first();

        $query_tests = DB::table('finish_transaction_tests')
            ->where('finish_transaction_id', '=', $id)
            ->where('is_print', '=', 1)
            ->orderBy('sequence', 'asc')
            ->orderBy('sub_group', 'asc');

        $query_group = DB::table('finish_transaction_tests')
            ->select('group_name')
            ->where('finish_transaction_id', '=', $id)
            // ->orderBy('sequence', 'asc')
            ->groupBy('group_name')
            ->get();

        $query_subgroup = DB::table('finish_transaction_tests')
            ->selectRaw("CASE WHEN sub_group IS NULL OR sub_group = '' THEN '' ELSE sub_group END as sub_group_grouped")
            ->where('finish_transaction_id', '=', $id)
            ->groupBy('sub_group_grouped')
            ->get();
        // dd($query_transaction);
        $reg_time = Carbon::parse($transaction->created_time)->format('d/m/Y H:i');
        $analitik_time = Carbon::parse($transaction->analytic_time)->format('d/m/Y H:i');

        // print time       
        $print_time_query = DB::table('finish_transaction_tests')
            ->select(DB::raw('MIN(validate_time) AS validate_time'))
            ->where('finish_transaction_id', '=', $id)->first();
        if ($print_time_query) {
            $print_time = $print_time_query->validate_time;
        } else {
            $print_time = Carbon::now();
        }

        $tests = $query_tests->get();

        $bulan_1 = Carbon::now()->format('F');
        $tgl = Carbon::now()->format('d');
        $tahun = Carbon::now()->format('Y');

        if ($bulan_1 == "January") {
            $bulan_st = "Januari";
        } else if ($bulan_1 == "February") {
            $bulan_st = "Februari";
        } else if ($bulan_1 == "March") {
            $bulan_st = "Maret";
        } else if ($bulan_1 == "April") {
            $bulan_st = "April";
        } else if ($bulan_1 == "May") {
            $bulan_st = "Mei";
        } else if ($bulan_1 == "June") {
            $bulan_st = "Juni";
        } else if ($bulan_1 == "July") {
            $bulan_st = "Juli";
        } else if ($bulan_1 == "August") {
            $bulan_st = "Agustus";
        } else if ($bulan_1 == "September") {
            $bulan_st = "September";
        } else if ($bulan_1 == "October") {
            $bulan_st = "Oktober";
        } else if ($bulan_1 == "November") {
            $bulan_st = "November";
        } else if ($bulan_1 == "December") {
            $bulan_st = "Desember";
        }

        $copy = $transaction->print;
        $user = Auth::user();

        DB::table('log_print')
            ->insert([
                'finish_transaction_id' => $id,
                'printed_at' => Carbon::now()->toDateTimeString(),
                'copied' => $copy,
                'print_by' => $user->id,
            ]);

        $tgl_result = $tgl . " " . $bulan_st . " " . $tahun;
        $sorted_test = [];
        $group_array = [];

        // print_r($query_group);
        // die;

        foreach ($query_group as $group) {
            $group_array[$group->group_name] = [];
            array_push($group_array[$group->group_name], $group->group_name);
            foreach ($query_subgroup as $subgroup) {
                foreach ($tests as $test) {
                    if ($test->result_status == AnalyticController::RESULT_STATUS_NORMAL) {
                        $status = "Normal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_LOW || $test->result_status == AnalyticController::RESULT_STATUS_HIGH || $test->result_status == AnalyticController::RESULT_STATUS_ABNORMAL) {
                        $status = "Abnormal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                        $status = "Critical";
                    } else {
                        $status = "-";
                    }
                    $test->result_status_label = $status;
                    if (($test->sub_group == $subgroup->sub_group_grouped) && ($test->group_name == $group->group_name)) {
                        if (($subgroup->sub_group_grouped != '') && (!in_array($subgroup->sub_group_grouped, $group_array[$group->group_name]))) {
                            array_push($group_array[$group->group_name], $subgroup->sub_group_grouped);
                        }
                        array_push($group_array[$group->group_name], $test->test_name);
                        array_push($sorted_test, $test);
                        //echo $group->group_name . " - " . $subgroup->sub_group_grouped . " - " . $test->test_name . "<br>";
                    }
                }
            }
        }
        $born = Carbon::createFromFormat('Y-m-d', $transaction->patient_birthdate);
        $age = $born->diff(Carbon::now())->format('%y Thn %m Bln %d Hr');
        $first_draw_time_query = DB::table('finish_transaction_tests')
            ->select(DB::raw('MIN(draw_time) AS draw_time'))
            ->where('finish_transaction_id', '=', $id)->first();
        if ($first_draw_time_query) {
            $first_draw_time = $first_draw_time_query->draw_time;
        } else {
            $first_draw_time = Carbon::now();
        }

        // last draw time
        $last_draw_time_query = DB::table('finish_transaction_tests')
            ->select(DB::raw('MAX(draw_time) AS draw_time'))
            ->where('finish_transaction_id', '=', $id)->first();
        if ($last_draw_time_query) {
            $last_draw_time = $last_draw_time_query->draw_time;
        } else {
            $last_draw_time = Carbon::now();
        }

        $data = [
            // 'tests' => $sorted_test,
            'tests' => $tests,
            'groups' => $group_array,
            'reg_time' => $reg_time,
            'analitik_time' => $analitik_time,
            'print_time' => $print_time,
            'tgl_result' => $tgl_result,
            'last_draw_time' => $last_draw_time,
            "transaction" => $transaction,
            "age" => $age,
        ];

        // echo "<pre>";
        // print_r($sorted_test);
        // die();

        return view('prints.hasil-test', $data);
    }

    public function printAnalytic($id, $group_id = null)
    {
        // dd($id);
        $judul = "Print Result";
        $data['title'] = $judul;
        DB::table('transactions')
            ->where('id', $id)
            ->update([
                'print' => DB::raw('print + 1'),
            ]);

        // $query_patient = DB::table('finish_transactions')
        //  ->select('finish_transactions.*', 'transactions.created_time', 'transactions.checkin_time', 'transactions.analytic_time', 'transactions.post_time', 'transactions.print')
        //  ->leftJoin('transactions', 'transactions.id', '=', 'finish_transactions.transaction_id')
        //  ->where('finish_transactions.transaction_id', '=', $id);
        $query_transaction = DB::table('transactions')
            ->select(
                'transactions.id as transaction_id',
                'transactions.checkin_time',
                'transactions.analytic_time',
                'transactions.post_time',
                'transactions.created_time',
                'transactions.memo_result',
                'transactions.is_print_memo',
                'transactions.print',
                'patients.name as patient_name',
                'patients.medrec as patient_medrec',
                'patients.birthdate as patient_birthdate',
                'patients.address as patient_address',
                'patients.gender as patient_gender',
                'rooms.room as room_name',
                'doctors.name as doctor_name',
                'insurances.name as insurance_name',
                'insurances.discount as insurance_discount',
                'transactions.type as type',
                'transactions.no_lab as no_lab',
                'transactions.memo_result as memo',
                'transactions.cito as cito',
                'transactions.note as note',
                'users.name as verificator_name',
            )
            ->leftJoin('users', 'users.id', '=', 'transactions.verficator_id')
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->leftJoin('doctors', 'transactions.doctor_id', '=', 'doctors.id')
            ->leftJoin('insurances', 'transactions.insurance_id', '=', 'insurances.id')
            // ->select('created_time', 'analytic_time')
            ->where('transactions.id', $id)->first();

        $query_transaction_tests = DB::table('transactions')
            ->select(
                'transactions.id as transaction_id',
                'tests.name as test_name',
                'tests.sub_group as sub_group',
                'tests.unit as test_unit',
                'packages.name as package_name',
                'transaction_tests.result_number as result_number',
                'results.result as result_label',
                'transaction_tests.print_package_name',
                'transaction_tests.result_text as result_text',
                'transaction_tests.report_by as report_by',
                'transaction_tests.report_to as report_to',
                'transaction_tests.memo_test as memo_test',
                'transaction_tests.test_id as test_id',
                'transaction_tests.result_status as result_status',
                'transaction_tests.report_time',
                'groups.name as group_name',
            )
            ->leftJoin('transaction_tests', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
            ->leftJoin('groups', 'tests.group_id', '=', 'groups.id')
            ->leftJoin('results', 'transaction_tests.result_label', '=', 'results.id')
            ->leftJoin('packages', 'transaction_tests.package_id', '=', 'packages.id')
            // ->select('created_time', 'analytic_time')
            ->where('transactions.id', $id)
            ->where('transaction_tests.validate', 1)
            ->orderBy('sequence', 'asc')
            ->orderBy('sub_group', 'asc');

        if ($group_id) {
            $query_transaction_tests = $query_transaction_tests->where('groups.id', $group_id);
        }

        $query_transaction_tests = $query_transaction_tests->get();

        // dd($query_transaction_tests);
        $query_group = DB::table('transaction_tests')
            ->select('groups.name as group_name')
            ->leftJoin('tests', 'tests.id', '=', 'transaction_tests.test_id')
            ->leftJoin('groups', 'tests.group_id', '=', 'groups.id')
            ->where('transaction_id', '=', $id)
            ->where('transaction_tests.validate', 1);

        if ($group_id) {
            $query_group = $query_group->where('groups.id', $group_id);
        }

        $query_group = $query_group
            ->orderBy('sequence', 'asc')
            ->groupBy('group_name')
            ->get();

        $query_subgroup = DB::table('transaction_tests')
            ->selectRaw("CASE WHEN tests.sub_group IS NULL OR tests.sub_group = '' THEN '' ELSE tests.sub_group END as sub_group_grouped")
            ->leftJoin('tests', 'tests.id', '=', 'transaction_tests.test_id')
            ->where('transaction_id', '=', $id)
            ->where('transaction_tests.validate', 1)
            // ->orderBy('sequence', 'asc')
            ->groupBy('sub_group_grouped')
            ->get();

        $reg_time = Carbon::parse($query_transaction->created_time)->format('d/m/Y H:i');
        $analitik_time = Carbon::parse($query_transaction->analytic_time)->format('d/m/Y H:i');

        $copy = $query_transaction->print;
        if (!$copy) {
            $copy = 1;
        } else {
            $copy++;
        }
        $user = Auth::user();
        DB::table('log_print')
            ->insert([
                'transaction_id' => $id,
                'printed_at' => Carbon::now()->toDateTimeString(),
                'copied' => $copy,
                'print_by' => $user->id
            ]);

        $log_print = DB::table('log_print')
            ->select('*')
            ->where('transaction_id', '=', $id)->orderBy('id', 'asc')->first();
        if ($log_print) {

            $print_time = $log_print->printed_at;
        } else {
            $print_time = Carbon::now();
        }

        $tests = $query_transaction_tests;

        $bulan_1 = Carbon::now()->format('F');
        $tgl = Carbon::now()->format('d');
        $tahun = Carbon::now()->format('Y');

        if ($bulan_1 == "January") {
            $bulan_st = "Januari";
        } else if ($bulan_1 == "February") {
            $bulan_st = "Februari";
        } else if ($bulan_1 == "March") {
            $bulan_st = "Maret";
        } else if ($bulan_1 == "April") {
            $bulan_st = "April";
        } else if ($bulan_1 == "May") {
            $bulan_st = "Mei";
        } else if ($bulan_1 == "June") {
            $bulan_st = "Juni";
        } else if ($bulan_1 == "July") {
            $bulan_st = "Juli";
        } else if ($bulan_1 == "August") {
            $bulan_st = "Agustus";
        } else if ($bulan_1 == "September") {
            $bulan_st = "September";
        } else if ($bulan_1 == "October") {
            $bulan_st = "Oktober";
        } else if ($bulan_1 == "November") {
            $bulan_st = "November";
        } else if ($bulan_1 == "December") {
            $bulan_st = "Desember";
        }

        // DB::table('log_print')
        //     ->insert([
        //         'id_transaction' => $id,
        //         'printed_at' => Carbon::now()->toDateTimeString(),
        //         'copied' => $copy,
        //     ]);

        $tgl_result = $tgl . " " . $bulan_st . " " . $tahun;
        $sorted_test = [];
        $group_array = [];
        $show_unit = 1;
        // print_r($query_group); die;
        $born = Carbon::createFromFormat('Y-m-d', $query_transaction->patient_birthdate);
        $birthdate = $born->diff(Carbon::now())->format('%y Thn / %m Bln / %d Hr');
        $birthday = $born->diff(Carbon::now())->days;
        $query_transaction->patient_age = $birthdate;
        // dd($tests);
        foreach ($query_group as $group) {
            $group_array[$group->group_name] = [];
            array_push($group_array[$group->group_name], $group->group_name);
            foreach ($query_subgroup as $subgroup) {
                foreach ($tests as $test) {
                    $current_test = DB::table('tests')->where('id', $test->test_id)->first();
                    if ($query_transaction->patient_gender == "M") {
                        $raw_male = DB::raw('ranges.min_age, ranges.max_age, ranges.min_male_ref as min_ref, ranges.max_male_ref as max_ref, ranges.min_crit_male as min_crit, ranges.max_crit_male as max_crit, normal_male as normal');
                        $range = DB::table('ranges')
                            ->select($raw_male)
                            ->where('ranges.test_id', $test->test_id)
                            ->where('ranges.min_age', '<=', $birthday)
                            ->where('ranges.max_age', '>=', $birthday)
                            ->first();
                    } else {
                        $raw_female = DB::raw('ranges.min_age, ranges.max_age, ranges.min_female_ref as min_ref, ranges.max_female_ref as max_ref, ranges.min_crit_female as min_crit, ranges.max_crit_female as max_crit, normal_female as normal');
                        $range = DB::table('ranges')
                            ->select($raw_female)
                            ->where('ranges.test_id', $test->test_id)
                            ->where('ranges.min_age', '<=', $birthday)
                            ->where('ranges.max_age', '>=', $birthday)
                            ->first();
                    }
                    if ($current_test->range_type == "number") {

                        // dd($range);
                        // print_r($range->min_ref);
                        if (!$range->min_ref) {
                            $min_ref_tostring = 0;
                        } else {
                            $min_ref_tostring = (string)$range->min_ref;
                        }
                        if (($range->normal != '') || ($range->normal != NULL)) {
                            $normal = $range->normal;
                        } else if (strlen($min_ref_tostring) > 0) {
                            // $min = $range->min_ref;
                            $normal = $range->min_ref . '-' . $range->max_ref;
                        } else {
                            $normal = 0;
                        }

                        $value = $test->result_number;
                    } else if ($current_test->range_type == "label") {

                        $label = DB::table('results')->select('result')
                            ->where('test_id', $test->test_id)
                            ->where('status', '=', 'Normal')
                            ->first();
                        // dd($label);
                        $label_name = DB::table('results')->select('result')
                            ->where('test_id', $test->test_id)
                            ->where('result', $test->result_label)
                            ->first();
                        $notes = DB::table('tests')->select('normal_notes')
                            ->where('id', $test->test_id)
                            ->first();
                        // dd($label_name,$test);
                        if ((!empty($range->normal)) && (($range->normal != '') || ($range->normal != NULL))) {
                            $normal = $range->normal;
                        } else {
                            $normal = $notes->normal_notes;
                        }
                        $value = $label_name->result;
                    } else {
                        $notes = DB::table('tests')->select('normal_notes')
                            ->where('id', $test->test_id)
                            ->first();

                        if ((!empty($range->normal)) && (($range->normal != '') || ($range->normal != NULL))) {
                            $normal = $range->normal;
                        } else if (!empty($notes)) {
                            $normal = $notes->normal_notes;
                            // $normal = 'syalalalalasdfghsssssssssssssssssssssssssssssss';
                        } else {
                            $normal = "-";
                        }
                        $value = $test->result_text;
                    }

                    if ($test->result_status == AnalyticController::RESULT_STATUS_NORMAL) {
                        $status = "Normal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_LOW || $test->result_status == AnalyticController::RESULT_STATUS_HIGH || $test->result_status == AnalyticController::RESULT_STATUS_ABNORMAL) {
                        $status = "Abnormal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                        $status = "Critical";
                    } else {
                        $status = "-";
                    }
                    $test->result_status_label = $status;
                    if ($test->package_name == self::COVID_PACKAGE_NAME) {
                        $show_unit = 0;
                    }
                    $test->normal_value = $normal;
                    $test->result = $value;
                    if (($test->sub_group == $subgroup->sub_group_grouped) && ($test->group_name == $group->group_name)) {
                        if (($subgroup->sub_group_grouped != '') && (!in_array($subgroup->sub_group_grouped, $group_array[$group->group_name]))) {
                            array_push($group_array[$group->group_name], $subgroup->sub_group_grouped);
                        }
                        array_push($group_array[$group->group_name], $test->test_name);
                        array_push($sorted_test, $test);
                        // echo $group->group_name ." - ". $subgroup->sub_group_grouped ." - ". $test->test_name . "<br>";
                    }
                }
            }
        }
        $born = Carbon::createFromFormat('Y-m-d', $query_transaction->patient_birthdate);
        $age = $born->diff(Carbon::now())->format('%y Thn %m Bln %d Hr');
        $first_draw_time_query = DB::table('transaction_tests')
            ->select(DB::raw('MIN(draw_time) AS draw_time'))
            ->where('transaction_id', '=', $id)->first();
        if ($first_draw_time_query) {
            $first_draw_time = $first_draw_time_query->draw_time;
        } else {
            $first_draw_time = Carbon::now();
        }

        $data = [
            // 'tests' => $sorted_test,
            'tests' => $tests,
            'groups' => $group_array,
            'reg_time' => $reg_time,
            'analitik_time' => $analitik_time,
            'print_time' => $print_time,
            'tgl_result' => $tgl_result,
            'first_draw_time' => $first_draw_time,
            "transaction" => $query_transaction,
            "age" => $age,
        ];

        return view('prints.group-result-analytic', $data);
    }

    public function printTestGroup($groupId, $id)
    {

        // dd($id);
        $judul = "Print Result";
        $data['title'] = $judul;
        DB::table('transactions')
            ->where('id', $id)
            ->update([
                'print' => DB::raw('print + 1'),
            ]);

        $query_transaction = DB::table('transactions')
            ->select(
                'transactions.id as transaction_id',
                'transactions.checkin_time',
                'transactions.analytic_time',
                'transactions.post_time',
                'transactions.created_time',
                'transactions.memo_result',
                'transactions.is_print_memo',
                'transactions.print',
                'patients.name as patient_name',
                'patients.medrec as patient_medrec',
                'patients.birthdate as patient_birthdate',
                'patients.address as patient_address',
                'patients.gender as patient_gender',
                'rooms.room as room_name',
                'doctors.name as doctor_name',
                'insurances.name as insurance_name',
                'insurances.discount as insurance_discount',
                'transactions.type as type',
                'transactions.no_lab as no_lab',
                'transactions.memo_result as memo',
                'transactions.cito as cito',
                'transactions.note as note',
                'users.name as validator_name',
            )
            ->leftJoin('users', 'users.id', '=', 'transactions.validator_id')
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->leftJoin('doctors', 'transactions.doctor_id', '=', 'doctors.id')
            ->leftJoin('insurances', 'transactions.insurance_id', '=', 'insurances.id')
            // ->select('created_time', 'analytic_time')
            ->where('transactions.id', $id)->first();

        $query_transaction_tests = DB::table('transactions')
            ->select(
                'transactions.id as transaction_id',
                'tests.name as test_name',
                'tests.sub_group as sub_group',
                'tests.unit as test_unit',
                'packages.name as package_name',
                'transaction_tests.result_number as result_number',
                'results.result as result_label',
                'transaction_tests.print_package_name',
                'transaction_tests.result_text as result_text',
                'transaction_tests.report_by as report_by',
                'transaction_tests.report_to as report_to',
                'transaction_tests.memo_test as memo_test',
                'transaction_tests.test_id as test_id',
                'transaction_tests.result_status as result_status',
                'transaction_tests.report_time',
                'groups.name as group_name',
            )
            ->leftJoin('transaction_tests', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
            ->leftJoin('groups', 'tests.group_id', '=', 'groups.id')
            ->leftJoin('results', 'transaction_tests.result_label', '=', 'results.id')
            ->leftJoin('packages', 'transaction_tests.package_id', '=', 'packages.id')
            // ->select('created_time', 'analytic_time')
            ->where('transactions.id', $id)
            ->where('transaction_tests.validate', 1)
            ->where('groups.id', $groupId)
            ->orderBy('sequence', 'asc')
            ->orderBy('sub_group', 'asc');

        $query_transaction_tests = $query_transaction_tests->get();

        // dd($query_transaction_tests);
        $query_group = DB::table('transaction_tests')
            ->select('groups.name as group_name')
            ->leftJoin('tests', 'tests.id', '=', 'transaction_tests.test_id')
            ->leftJoin('groups', 'tests.group_id', '=', 'groups.id')
            ->where('transaction_id', '=', $id)
            ->where('transaction_tests.validate', 1)
            ->where('groups.id', $groupId);

        $query_group = $query_group
            ->orderBy('sequence', 'asc')
            ->groupBy('group_name')
            ->get();

        $query_subgroup = DB::table('transaction_tests')
            ->selectRaw("CASE WHEN tests.sub_group IS NULL OR tests.sub_group = '' THEN '' ELSE tests.sub_group END as sub_group_grouped")
            ->leftJoin('tests', 'tests.id', '=', 'transaction_tests.test_id')
            ->where('transaction_id', '=', $id)
            ->where('transaction_tests.validate', 1)
            // ->orderBy('sequence', 'asc')
            ->groupBy('sub_group_grouped')
            ->get();

        $reg_time = Carbon::parse($query_transaction->created_time)->format('d/m/Y H:i');
        $analitik_time = Carbon::parse($query_transaction->analytic_time)->format('d/m/Y H:i');

        $copy = $query_transaction->print;
        if (!$copy) {
            $copy = 1;
        } else {
            $copy++;
        }
        $user = Auth::user();
        DB::table('log_print')
            ->insert([
                'transaction_id' => $id,
                'printed_at' => Carbon::now()->toDateTimeString(),
                'copied' => $copy,
                'print_by' => $user->id
            ]);

        $log_print = DB::table('log_print')
            ->select('*')
            ->where('transaction_id', '=', $id)->orderBy('id', 'asc')->first();
        if ($log_print) {

            $print_time = $log_print->printed_at;
        } else {
            $print_time = Carbon::now();
        }

        $tests = $query_transaction_tests;

        $bulan_1 = Carbon::now()->format('F');
        $tgl = Carbon::now()->format('d');
        $tahun = Carbon::now()->format('Y');

        if ($bulan_1 == "January") {
            $bulan_st = "Januari";
        } else if ($bulan_1 == "February") {
            $bulan_st = "Februari";
        } else if ($bulan_1 == "March") {
            $bulan_st = "Maret";
        } else if ($bulan_1 == "April") {
            $bulan_st = "April";
        } else if ($bulan_1 == "May") {
            $bulan_st = "Mei";
        } else if ($bulan_1 == "June") {
            $bulan_st = "Juni";
        } else if ($bulan_1 == "July") {
            $bulan_st = "Juli";
        } else if ($bulan_1 == "August") {
            $bulan_st = "Agustus";
        } else if ($bulan_1 == "September") {
            $bulan_st = "September";
        } else if ($bulan_1 == "October") {
            $bulan_st = "Oktober";
        } else if ($bulan_1 == "November") {
            $bulan_st = "November";
        } else if ($bulan_1 == "December") {
            $bulan_st = "Desember";
        }

        // DB::table('log_print')
        //     ->insert([
        //         'id_transaction' => $id,
        //         'printed_at' => Carbon::now()->toDateTimeString(),
        //         'copied' => $copy,
        //     ]);

        $tgl_result = $tgl . " " . $bulan_st . " " . $tahun;
        $sorted_test = [];
        $group_array = [];
        $show_unit = 1;
        // print_r($query_group); die;
        $born = Carbon::createFromFormat('Y-m-d', $query_transaction->patient_birthdate);
        $birthdate = $born->diff(Carbon::now())->format('%y Thn / %m Bln / %d Hr');
        $birthday = $born->diff(Carbon::now())->days;
        $query_transaction->patient_age = $birthdate;
        // dd($tests);
        foreach ($query_group as $group) {
            $group_array[$group->group_name] = [];
            array_push($group_array[$group->group_name], $group->group_name);
            foreach ($query_subgroup as $subgroup) {
                foreach ($tests as $test) {
                    $current_test = DB::table('tests')->where('id', $test->test_id)->first();
                    if ($query_transaction->patient_gender == "M") {
                        $raw_male = DB::raw('ranges.min_age, ranges.max_age, ranges.min_male_ref as min_ref, ranges.max_male_ref as max_ref, ranges.min_crit_male as min_crit, ranges.max_crit_male as max_crit, normal_male as normal');
                        $range = DB::table('ranges')
                            ->select($raw_male)
                            ->where('ranges.test_id', $test->test_id)
                            ->where('ranges.min_age', '<=', $birthday)
                            ->where('ranges.max_age', '>=', $birthday)
                            ->first();
                    } else {
                        $raw_female = DB::raw('ranges.min_age, ranges.max_age, ranges.min_female_ref as min_ref, ranges.max_female_ref as max_ref, ranges.min_crit_female as min_crit, ranges.max_crit_female as max_crit, normal_female as normal');
                        $range = DB::table('ranges')
                            ->select($raw_female)
                            ->where('ranges.test_id', $test->test_id)
                            ->where('ranges.min_age', '<=', $birthday)
                            ->where('ranges.max_age', '>=', $birthday)
                            ->first();
                    }
                    if ($current_test->range_type == "number") {

                        // dd($range);
                        // print_r($range->min_ref);
                        if (!$range->min_ref) {
                            $min_ref_tostring = 0;
                        } else {
                            $min_ref_tostring = (string)$range->min_ref;
                        }
                        if (($range->normal != '') || ($range->normal != NULL)) {
                            $normal = $range->normal;
                        } else if (strlen($min_ref_tostring) > 0) {
                            // $min = $range->min_ref;
                            $normal = $range->min_ref . '-' . $range->max_ref;
                        } else {
                            $normal = 0;
                        }

                        $value = $test->result_number;
                    } else if ($current_test->range_type == "label") {

                        $label = DB::table('results')->select('result')
                            ->where('test_id', $test->test_id)
                            ->where('status', '=', 'Normal')
                            ->first();
                        // dd($label);
                        $label_name = DB::table('results')->select('result')
                            ->where('test_id', $test->test_id)
                            ->where('result', $test->result_label)
                            ->first();
                        $notes = DB::table('tests')->select('normal_notes')
                            ->where('id', $test->test_id)
                            ->first();
                        // dd($label_name,$test);
                        if ((!empty($range->normal)) && (($range->normal != '') || ($range->normal != NULL))) {
                            $normal = $range->normal;
                        } else {
                            $normal = $notes->normal_notes;
                        }
                        $value = $label_name->result;
                    } else if ($current_test->range_type == "date") {
                        $value = $test->result_date;
                    } else {
                        $notes = DB::table('tests')->select('normal_notes')
                            ->where('id', $test->test_id)
                            ->first();

                        if ((!empty($range->normal)) && (($range->normal != '') || ($range->normal != NULL))) {
                            $normal = $range->normal;
                        } else if (!empty($notes)) {
                            $normal = $notes->normal_notes;
                            // $normal = 'syalalalalasdfghsssssssssssssssssssssssssssssss';
                        } else {
                            $normal = "-";
                        }
                        $value = $test->result_text;
                    }

                    if ($test->result_status == AnalyticController::RESULT_STATUS_NORMAL) {
                        $status = "Normal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_LOW || $test->result_status == AnalyticController::RESULT_STATUS_HIGH || $test->result_status == AnalyticController::RESULT_STATUS_ABNORMAL) {
                        $status = "Abnormal";
                    } else if ($test->result_status == AnalyticController::RESULT_STATUS_CRITICAL) {
                        $status = "Critical";
                    } else {
                        $status = "-";
                    }
                    $test->result_status_label = $status;
                    if ($test->package_name == self::COVID_PACKAGE_NAME) {
                        $show_unit = 0;
                    }
                    $test->normal_value = $normal;
                    $test->result = $value;
                    if (($test->sub_group == $subgroup->sub_group_grouped) && ($test->group_name == $group->group_name)) {
                        if (($subgroup->sub_group_grouped != '') && (!in_array($subgroup->sub_group_grouped, $group_array[$group->group_name]))) {
                            array_push($group_array[$group->group_name], $subgroup->sub_group_grouped);
                        }
                        array_push($group_array[$group->group_name], $test->test_name);
                        array_push($sorted_test, $test);
                        // echo $group->group_name ." - ". $subgroup->sub_group_grouped ." - ". $test->test_name . "<br>";
                    }
                }
            }
        }
        $born = Carbon::createFromFormat('Y-m-d', $query_transaction->patient_birthdate);
        $age = $born->diff(Carbon::now())->format('%y Thn %m Bln %d Hr');
        $group_draw_time_query = DB::table('transaction_tests')
            ->select(DB::raw('MIN(draw_time) AS draw_time'))
            ->where('transaction_id', '=', $id)
            ->where('group_id', '=', $groupId)->first();
        if ($group_draw_time_query) {
            $group_draw_time = $group_draw_time_query->draw_time;
        } else {
            $group_draw_time = Carbon::now();
        }

        // echo '<pre>';
        // print_r($tests);
        // die;

        $data = [
            // 'tests' => $sorted_test,
            'tests' => $tests,
            'groups' => $group_array,
            'reg_time' => $reg_time,
            'analitik_time' => $analitik_time,
            'print_time' => $print_time,
            'tgl_result' => $tgl_result,
            'group_draw_time' => $group_draw_time,
            "transaction" => $query_transaction,
            "age" => $age,
        ];

        // echo '<pre>';
        // print_r($data);
        // die;

        return view('prints.print-test-group', $data);
    }

    public function barcode($transaction_id)
    {
        $transaction = \App\Transaction::findOrFail($transaction_id);

        if ($transaction) {
            // This will output the barcode as HTML output to display in the browser
            // $generator = new Picqer\Barcode\BarcodeGeneratorHTML();
            $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
            $barcodefional = '<img style="width:35mm" src="data:image/png;base64,' . base64_encode($generator->getBarcode($transaction->no_lab, $generator::TYPE_CODE_128)) . '">';

            // $barcodefional = $generator->getBarcode($transaction, $generator::TYPE_CODE_128);
            $gender = $transaction->patient->gender == "M" ? "L" : "P";
            $data = array(
                "no_lab" => $transaction->no_lab,
                "patient_name" => $transaction->patient->name . " (" . $gender . ")",
                "barcode" => $barcodefional,
            );

            return view('prints.barcode', $data);
        } else {
            echo "No Data";
        }
    }

    public function showBarcode($id)
    {
        $transaction_id = $id;

        $rawselect = DB::raw('transaction_tests.*, patients.name as patient_name, patients.gender, transactions.no_lab, patients.medrec, patients.birthdate as birth_date, transactions.no_lab, specimens.name as specimen_name, specimens.id as specimen_id , sum(tests.volume) as volume, tests.specimen_id, tests.unit');

        $query = DB::table('transaction_tests')
            ->select($rawselect)
            ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
            ->leftJoin('specimens', 'tests.specimen_id', '=', 'specimens.id')
            ->where('transaction_tests.transaction_id', $transaction_id)
            ->groupBy('tests.specimen_id');
        $data_transactions = $query->get();

        $width = 50; //mm
        $height = 20; //mm
        $resolution = 8;
        $padleft = $width * $resolution;
        $ip_address = "10.0.3.7";
        $printer_name = "Barcode";
        $base_path =  base_path();
        $dataToWrite = "";

        foreach ($data_transactions as $transaction) {
            //list test
            $dataToWrite = $dataToWrite . "^XA";
            $dataToWrite = $dataToWrite . "^CFB,20";
            $dataToWrite = $dataToWrite . "^FB" . $padleft . ",1,0,C"; //center
            $dataToWrite = $dataToWrite . "^FO0,15^FD" . substr($transaction->patient_name, 0, 35) . "\&^FS";
            $dataToWrite = $dataToWrite . "^BY2,3,70";
            $dataToWrite = $dataToWrite . "^FO85,40^BC^FD" . substr($transaction->no_lab, 2) . "^FS";
            $dataToWrite = $dataToWrite . "^FB" . $padleft . ",1,0,C"; //center
            $dataToWrite = $dataToWrite . "^CFA,20";
            $dataToWrite = $dataToWrite . "^FO0,135^FD" . substr($transaction->medrec, 0, 20) . " / " . $transaction->birth_date . " / " . $transaction->gender . "\&^FS";

            $dataToWrite = $dataToWrite . "^XZ";
        }

        // echo $dataToWrite;
        // die;

        $file = time() . rand() . '_print.zpl';
        $destinationPath = base_path() . "/temp_print/";
        if (!is_dir($destinationPath)) {
            mkdir($destinationPath, 0777, true);
        }
        File::put($destinationPath . $file, $dataToWrite);
        $print_file = $destinationPath . $file;
        copy($print_file, "//" . $ip_address . "/" . $printer_name);

        // echo $print_file, "//" . $ip_address . "/" . $printer_name;
    }

    public function showBarcodeSingleSpecimen($transaction_id, $specimen_id)
    {
        $rawselect = DB::raw('transaction_tests.*, patients.name as patient_name, patients.gender, transactions.no_lab, patients.medrec, patients.birthdate as birth_date, transactions.no_lab, specimens.name as specimen_name, specimens.id as specimen_id , sum(tests.volume) as volume, tests.specimen_id, tests.unit');

        $query = DB::table('transaction_tests')
            ->select($rawselect)
            ->leftJoin('transactions', 'transaction_tests.transaction_id', '=', 'transactions.id')
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
            ->leftJoin('specimens', 'tests.specimen_id', '=', 'specimens.id')
            ->where('transaction_tests.transaction_id', $transaction_id)
            ->where('specimens.id', $specimen_id)
            ->groupBy('tests.specimen_id');
        $data_transactions = $query->get();

        // print_r($data_transactions);
        // die;

        // $width = 50; //mm
        // $height = 20; //mm
        // $resolution = 8;
        // $padleft = $width * $resolution;
        // $ip_address = "192.168.6.120";
        // $printer_name = "Barcode";
        // $base_path =  base_path();
        // $dataToWrite = "";

        // foreach ($data_transactions as $transaction) {
        //     //list test
        //     $dataToWrite = $dataToWrite . "^XA";
        //     $dataToWrite = $dataToWrite . "^CFB,20";
        //     $dataToWrite = $dataToWrite . "^FB" . $padleft . ",1,0,C"; //center
        //     $dataToWrite = $dataToWrite . "^FO0,15^FD" . substr($transaction->patient_name, 0, 35) . "\&^FS";
        //     $dataToWrite = $dataToWrite . "^BY2,3,70";
        //     $dataToWrite = $dataToWrite . "^FO55,40^BC^FD" . substr($transaction->no_lab, 2) . "^FS";
        //     $dataToWrite = $dataToWrite . "^FB" . $padleft . ",1,0,C"; //center
        //     $dataToWrite = $dataToWrite . "^CFA,20";
        //     $dataToWrite = $dataToWrite . "^FO0,135^FD" . substr($transaction->medrec, 0, 20) . " / " . $transaction->birth_date . " / " . $transaction->gender . "\&^FS";
        //     // $dataToWrite = $dataToWrite . "^FB" . $padleft . ",1,0,C"; //center
        //     // $dataToWrite = $dataToWrite . "^^FO0,125^FD" . $transaction->birth_date . "\&^FS";
        //     $dataToWrite = $dataToWrite . "^XZ";
        // }

        // $file = time() . rand() . '_print.zpl';
        // $destinationPath = base_path() . "/temp_print/";
        // if (!is_dir($destinationPath)) {
        //     mkdir($destinationPath, 0777, true);
        // }
        // File::put($destinationPath . $file, $dataToWrite);
        // $print_file = $destinationPath . $file;
        // copy($print_file, "//" . $ip_address . "/" . $printer_name);

        return response()->json(['message' => 'Ini print Single Specimen. Tinggal Setting Barcode di PrintController']);
    }
}
