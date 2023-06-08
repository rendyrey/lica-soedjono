<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;
use PDF;

class ReportController extends Controller
{
    const STATUS = 2;

    public function selectOptionsType()
    {
        try {
            $data = array("rawat_inap", "rawat_jalan", "igd");

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function selectOptionsPatient()
    {
        try {
            $query = DB::table('patients')->selectRaw('patients.id as patient_id, patients.name as patient_name');
            $data = $query->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function selectOptionsInsurance()
    {
        try {
            $query = DB::table('insurances')->selectRaw('insurances.id as insurance_id, insurances.name as insurance_name');
            $data = $query->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function selectOptionsDoctor()
    {
        try {
            $query = DB::table('doctors')->selectRaw('doctors.id as doctor_id, doctors.name as doctor_name');
            $data = $query->get();

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    //===============================================================================================
    // CRITICAL REPORT
    //===============================================================================================
    public function criticalReport()
    {
        $data['title'] = 'Critical Report';
        return view('dashboard.report.report_critical.index', $data);
    }

    public function criticalDatatable($startDate = null, $endDate = null, $group_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = \App\FinishTransactionTest::selectRaw('finish_transaction_tests.*, finish_transactions.no_lab, finish_transactions.patient_name, finish_transactions.patient_medrec, finish_transactions.patient_birthdate, finish_transactions.room_name');

        $model->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id');
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->where('finish_transaction_tests.group_id', '=', $group_id);
        }
        $model->where('finish_transaction_tests.input_time', '>=', $from);
        $model->where('finish_transaction_tests.input_time', '<=', $to);
        $model->where('finish_transaction_tests.report_status', '=', 1);
        $model->orderBy('finish_transaction_tests.input_time', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function criticalPrint($startDate = null, $endDate = null, $group_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')->select('finish_transaction_tests.*', 'finish_transactions.no_lab', 'finish_transactions.patient_name', 'finish_transactions.patient_medrec', 'finish_transactions.patient_birthdate', 'finish_transactions.room_name');
        $query->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id');
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query->where('finish_transaction_tests.group_id', '=', $group_id);
        }
        $query->where('finish_transaction_tests.input_time', '>=', $from);
        $query->where('finish_transaction_tests.input_time', '<=', $to);
        $query->where('finish_transaction_tests.report_status', '=', 1);
        $query->orderBy('finish_transaction_tests.input_time', 'desc');

        $data["criticalData"] = $query->get();

        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query_group = DB::table('groups')->select('groups.*')->where('id', $group_id);
            $group = $query_group->first();
            $data["group"] = $group->name;
        } else {
            $data["group"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_critical.print', $data);
    }

    //===============================================================================================
    // DUPLO REPORT
    //===============================================================================================
    public function duploReport()
    {
        $data['title'] = 'Duplo Report';
        return view('dashboard.report.report_duplo.index', $data);
    }

    public function duploDatatable($startDate = null, $endDate = null, $group_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_tests.*, finish_transactions.patient_name, finish_transactions.patient_medrec, finish_transactions.patient_birthdate, finish_transactions.patient_gender, MAX(CASE WHEN finish_transaction_tests.mark_duplo = 1 THEN result_number END) "result", MAX(CASE WHEN finish_transaction_tests.mark_duplo = 2 THEN result_number END) "result_duplo"');
        $model->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id');
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->where('finish_transaction_tests.group_id', '=', $group_id);
        }
        $model->where('finish_transaction_tests.input_time', '>=', $from);
        $model->where('finish_transaction_tests.input_time', '<=', $to);
        $model->where('finish_transaction_tests.mark_duplo', '!=', 0);
        $model->orderBy('finish_transaction_tests.input_time', 'desc');
        $model->groupBy('finish_transaction_tests.finish_transaction_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function duploPrint($startDate = null, $endDate = null, $group_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_tests.*, finish_transactions.patient_name, finish_transactions.patient_medrec, finish_transactions.patient_birthdate, finish_transactions.patient_gender, MAX(CASE WHEN finish_transaction_tests.mark_duplo = 1 THEN global_result END) "result", MAX(CASE WHEN finish_transaction_tests.mark_duplo = 2 THEN global_result END) "result_duplo"');
        $query->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id');
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query->where('finish_transaction_tests.group_id', '=', $group_id);
        }
        $query->where('finish_transaction_tests.input_time', '>=', $from);
        $query->where('finish_transaction_tests.input_time', '<=', $to);
        $query->where('finish_transaction_tests.mark_duplo', '!=', 0);
        $query->orderBy('finish_transaction_tests.input_time', 'desc');
        $query->groupBy('finish_transaction_tests.finish_transaction_id');

        $data["duploData"] = $query->get();

        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query_group = DB::table('groups')->select('groups.*')->where('id', $group_id);
            $group = $query_group->first();
            $data["group"] = $group->name;
        } else {
            $data["group"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_duplo.print', $data);
    }

    //===============================================================================================
    // GROUP TEST REPORT
    //===============================================================================================
    public function groupTestReport()
    {
        $data['title'] = 'Group Test Report';
        return view('dashboard.report.report_group_test.index', $data);
    }

    public function groupTestDatatable($startDate = null, $endDate = null, $group_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = \App\FinishTransactionTest::selectRaw('finish_transaction_tests.id, finish_transaction_tests.group_name, COUNT(finish_transaction_tests.group_id) as total_test')
            ->where('input_time', '>=', $from)
            ->where('input_time', '<=', $to);
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $model->where('group_id', $group_id);
        }
        $model->groupBy('group_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);

        // query backup
        // $query = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name');

        // $query->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        // if ($group_id != null && $group_id != "null" && $group_id != 0) {
        //     $query->where('finish_transaction_tests.group_id', '=', $group_id);
        // }
        // $query->where('finish_transactions.created_time', '>=', $from);
        // $query->where('finish_transactions.created_time', '<=', $to);
        // $query->orderBy('finish_transactions.created_time', 'desc');
        // $query->groupBy('finish_transactions.id');

        // $group_data = $query->get();

        // return response()->json($group_data);

        // query backup
        // $model = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name');

        // $model->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        // if ($group_id != null && $group_id != "null" && $group_id != 0) {
        //     $model->where('finish_transaction_tests.group_id', '=', $group_id);
        // }
        // $model->where('finish_transactions.created_time', '>=', $from);
        // $model->where('finish_transactions.created_time', '<=', $to);
        // $model->orderBy('finish_transactions.created_time', 'desc');
        // $model->groupBy('finish_transactions.id');

        // return DataTables::of($model)
        //     ->addIndexColumn()
        //     ->escapeColumns([])
        //     ->make(true);
    }

    public function groupTestPrint($startDate = null, $endDate = null, $group_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_tests.id, finish_transaction_tests.group_name, COUNT(finish_transaction_tests.group_id) as total_test')
            ->where('input_time', '>=', $from)
            ->where('input_time', '<=', $to);
        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query->where('group_id', $group_id);
        }
        $query->groupBy('group_id');
        $data["groupData"] = $query->get();

        if ($group_id != null && $group_id != "null" && $group_id != 0) {
            $query_group = DB::table('groups')->select('name as group_name');
            $group = $query_group->first();
            $data["group"] = $group->group_name;
        } else {
            $data["group"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        // $query_transactions = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name');
        // $query_transactions->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        // $query_transactions->where('finish_transactions.created_time', '>=', $from);
        // $query_transactions->where('finish_transactions.created_time', '<=', $to);
        // if ($group_id != null && $group_id != "null" && $group_id != 0) {
        //     $query_transactions->where('finish_transaction_tests.group_id', $group_id);
        // }
        // $query_transactions->orderBy('finish_transactions.created_time', 'desc');
        // $query_transactions->groupBy('finish_transaction_tests.group_id');


        return view('dashboard.report.report_group_test.print', $data);
    }

    //===============================================================================================
    // TAT (TURNAROUND TIME) REPORT
    //===============================================================================================
    public function TATReport()
    {
        $data['title'] = 'Turnaround Time Report';
        return view('dashboard.report.report_tat.index', $data);
    }

    public function TATDatatable($startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name, finish_transaction_tests.draw_time, finish_transaction_tests.validate_time');
        $model->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $model->where('finish_transactions.created_time', '>=', $from);
        $model->where('finish_transactions.created_time', '<=', $to);
        $model->orderBy('finish_transactions.created_time', 'desc');
        $model->groupBy('finish_transactions.id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function TATPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name, finish_transaction_tests.draw_time, finish_transaction_tests.validate_time');
        $query->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $query->where('finish_transactions.created_time', '>=', $from);
        $query->where('finish_transactions.created_time', '<=', $to);
        $query->orderBy('finish_transactions.created_time', 'desc');
        $query->groupBy('finish_transactions.id');

        $data["tatData"] = $query->get();

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_tat.print', $data);
    }

    public function TATGroupPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query_group = DB::table('finish_transactions')->selectRaw('finish_transactions.created_time, finish_transaction_tests.group_id, finish_transaction_tests.group_name')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transaction_tests.group_id', 'asc')
            ->groupBy('finish_transaction_tests.group_id');
        $groupData = $query_group->get();

        $query_tat = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_id, finish_transaction_tests.group_name, finish_transaction_tests.draw_time, finish_transaction_tests.validate_time')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.group_id');
        $tatData = $query_tat->get();

        $data["groupData"] = $groupData;
        $data["tatData"] = $tatData; 
        
        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_tat.print-group', $data);
    }

    //===============================================================================================
    // TAT TARGET REPORT
    //===============================================================================================
    public function TATTargetReport()
    {
        $data['title'] = 'TAT Target Report';
        return view('dashboard.report.report_tat_target.index', $data);
    }

    public function TATTargetDatatable($startDate = null, $endDate = null)
    {
        // $startDate = '2023-03-15';
        // $endDate = '2023-03-15';

        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query_group = DB::table('groups')->select('id as group_id', 'name as group_name', 'target_tat');
        $groupData = $query_group->get();

        $final_target_dibawah = 0;
        $final_target_diatas = 0;

        foreach ($groupData as $key => $value) {
            $group_name = $value->group_name;
            $temp_minute = $value->target_tat * 60;

            $target_tat_in_minute = gmdate('H:i:s', $temp_minute);
            $target_tat_in_seconds = $value->target_tat * 60;

            $groupData[$key]->target_tat_in_minute = $target_tat_in_minute;
            $groupData[$key]->target_tat_in_seconds = $target_tat_in_seconds;

            // query jumlah
            $query_jumlah = DB::table('finish_transaction_tests')
                ->select('finish_transaction_tests.group_name', 'finish_transactions.checkin_time', 'finish_transaction_tests.validate_time')
                ->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
                ->whereRaw("date(finish_transaction_tests.input_time) between '" . $from . "' and '" . $to . "'")
                ->where('finish_transactions.type', '!=', 'igd')
                ->where('finish_transactions.room_name', '!=', 'RUANG ICU')
                ->where('group_name', $group_name)
                ->groupBy('finish_transaction_tests.group_name')
                ->groupBy('finish_transaction_tests.transaction_id');
            $jumlahData = $query_jumlah->get();
            $countData = $jumlahData->count();

            $groupData[$key]->jumlah_pemeriksaan = $countData;


            $tat_pergroup_in_seconds = 0;
            $total_tat_pergroup_in_seconds = 0;
            $count_tat_dibawah_target = 0;
            $count_tat_diatas_target = 0;
            foreach ($jumlahData as $key2 => $value_jumlah_data) {
                $checkin_time = \Carbon\Carbon::parse($value_jumlah_data->checkin_time);
                $validate_time = \Carbon\Carbon::parse($value_jumlah_data->validate_time);
                $tat_time = $validate_time->diffInSeconds($checkin_time);
                $tat = gmdate('H:i:s', $tat_time);

                if ($value_jumlah_data->group_name == $group_name) {
                    $total_tat_pergroup_in_seconds += $tat_time;
                    $tat_pergroup_in_seconds = $tat_time;

                    // tat pergorup < TARGET TAT IN SECONDS
                    if ($tat_pergroup_in_seconds < $target_tat_in_seconds) {
                        $count_tat_dibawah_target++;
                    } else {
                        $count_tat_diatas_target++;
                    }
                } else {
                    $total_tat_pergroup_in_seconds = 0;
                }

                $jumlahData[$key2]->tat = $tat;
                $jumlahData[$key2]->tat_in_seconds = $tat_time;

                // echo 'Group Name : ' . $group_name . '<br>';
                // echo 'Checkin Time : ' . $checkin_time . '<br>';
                // echo 'Validate Time : ' . $validate_time . '<br>';
                // echo 'TAT : ' . $tat . '<hr>';
            }
            // echo '<pre>';
            // print_r($jumlahData);


            $groupData[$key]->total_tat_in_seconds = $total_tat_pergroup_in_seconds;

            $groupData[$key]->target_dibawah = $count_tat_dibawah_target;
            $groupData[$key]->target_diatas = $count_tat_diatas_target;

            // persentase
            if ($count_tat_dibawah_target != 0) {
                $final_target_dibawah = $count_tat_dibawah_target / $countData;
                $final_target_dibawah = $final_target_dibawah * 100;
                $final_target_dibawah = round($final_target_dibawah) . '%';
            } else {
                $final_target_dibawah = 0;
                $final_target_dibawah = $final_target_dibawah . '%';
            }

            if ($count_tat_diatas_target != 0) {
                $final_target_diatas = $count_tat_diatas_target / $countData;
                $final_target_diatas = $final_target_diatas * 100;
                $final_target_diatas = round($final_target_diatas) . '%';
            } else {
                $final_target_diatas = 0;
                $final_target_diatas = $final_target_diatas . '%';
            }


            $groupData[$key]->final_target_dibawah = $final_target_dibawah;
            $groupData[$key]->final_target_diatas = $final_target_diatas;
        }

        // echo '<pre>';
        // print_r($groupData);
        // die;

        return DataTables::of($groupData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function TATTargetPrint($startDate = null, $endDate = null)
    {

        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query_group = DB::table('groups')->select('id as group_id', 'name as group_name', 'target_tat');
        $groupData = $query_group->get();

        $final_target_dibawah = 0;
        $final_target_diatas = 0;

        foreach ($groupData as $key => $value) {
            $group_name = $value->group_name;
            $temp_minute = $value->target_tat * 60;

            $target_tat_in_minute = gmdate('H:i:s', $temp_minute);
            $target_tat_in_seconds = $value->target_tat * 60;

            $groupData[$key]->target_tat_in_minute = $target_tat_in_minute;
            $groupData[$key]->target_tat_in_seconds = $target_tat_in_seconds;

            // query jumlah
            $query_jumlah = DB::table('finish_transaction_tests')
                ->select('finish_transaction_tests.group_name', 'finish_transactions.checkin_time', 'finish_transaction_tests.validate_time')
                ->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
                ->whereRaw("date(finish_transaction_tests.input_time) between '" . $from . "' and '" . $to . "'")
                ->where('finish_transactions.type', '!=', 'igd')
                ->where('finish_transactions.room_name', '!=', 'RUANG ICU')
                ->where('group_name', $group_name)
                ->groupBy('finish_transaction_tests.group_name')
                ->groupBy('finish_transaction_tests.transaction_id');
            $jumlahData = $query_jumlah->get();
            $countData = $jumlahData->count();

            $groupData[$key]->jumlah_pemeriksaan = $countData;


            $tat_pergroup_in_seconds = 0;
            $total_tat_pergroup_in_seconds = 0;
            $count_tat_dibawah_target = 0;
            $count_tat_diatas_target = 0;
            foreach ($jumlahData as $key2 => $value_jumlah_data) {
                $checkin_time = \Carbon\Carbon::parse($value_jumlah_data->checkin_time);
                $validate_time = \Carbon\Carbon::parse($value_jumlah_data->validate_time);
                $tat_time = $validate_time->diffInSeconds($checkin_time);
                $tat = gmdate('H:i:s', $tat_time);

                if ($value_jumlah_data->group_name == $group_name) {
                    $total_tat_pergroup_in_seconds += $tat_time;
                    $tat_pergroup_in_seconds = $tat_time;

                    // tat pergorup < TARGET TAT IN SECONDS
                    if ($tat_pergroup_in_seconds < $target_tat_in_seconds) {
                        $count_tat_dibawah_target++;
                    } else {
                        $count_tat_diatas_target++;
                    }
                } else {
                    $total_tat_pergroup_in_seconds = 0;
                }

                $jumlahData[$key2]->tat = $tat;
                $jumlahData[$key2]->tat_in_seconds = $tat_time;

                // echo 'Group Name : ' . $group_name . '<br>';
                // echo 'Checkin Time : ' . $checkin_time . '<br>';
                // echo 'Validate Time : ' . $validate_time . '<br>';
                // echo 'TAT : ' . $tat . '<hr>';
            }
            // echo '<pre>';
            // print_r($jumlahData);


            $groupData[$key]->total_tat_in_seconds = $total_tat_pergroup_in_seconds;

            $groupData[$key]->target_dibawah = $count_tat_dibawah_target;
            $groupData[$key]->target_diatas = $count_tat_diatas_target;

            // persentase
            if ($count_tat_dibawah_target != 0) {
                $final_target_dibawah = $count_tat_dibawah_target / $countData;
                $final_target_dibawah = $final_target_dibawah * 100;
                $final_target_dibawah = round($final_target_dibawah) . '%';
            } else {
                $final_target_dibawah = 0;
                $final_target_dibawah = $final_target_dibawah . '%';
            }

            if ($count_tat_diatas_target != 0) {
                $final_target_diatas = $count_tat_diatas_target / $countData;
                $final_target_diatas = $final_target_diatas * 100;
                $final_target_diatas = round($final_target_diatas) . '%';
            } else {
                $final_target_diatas = 0;
                $final_target_diatas = $final_target_diatas . '%';
            }


            $groupData[$key]->final_target_dibawah = $final_target_dibawah;
            $groupData[$key]->final_target_diatas = $final_target_diatas;
        }

        // echo '<pre>';
        // print_r($groupData);
        // die;

        $data["tatData"] = $groupData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_tat_target.print', $data);
    }

    //===============================================================================================
    // TAT (TURNAROUND TIME) REPORT
    //===============================================================================================
    public function TATCitoReport()
    {
        $data['title'] = 'Turnaround Time CITO Report';
        return view('dashboard.report.report_tat_cito.index', $data);
    }

    public function TATCitoDatatable($startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name, finish_transaction_tests.draw_time, finish_transaction_tests.validate_time');
        $model->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $model->where('finish_transactions.created_time', '>=', $from);
        $model->where('finish_transactions.created_time', '<=', $to);
        $model->where('finish_transactions.cito', 1);
        $model->orderBy('finish_transactions.created_time', 'desc');
        $model->groupBy('finish_transactions.id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function TATCitoPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.group_name, finish_transaction_tests.draw_time, finish_transaction_tests.validate_time');
        $query->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $query->where('finish_transactions.created_time', '>=', $from);
        $query->where('finish_transactions.created_time', '<=', $to);
        $query->where('finish_transactions.cito', 1);
        $query->orderBy('finish_transactions.created_time', 'desc');
        $query->groupBy('finish_transactions.id');

        $data["tatData"] = $query->get();

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_tat_cito.print', $data);
    }

    //===============================================================================================
    // PATIENT REPORT
    //===============================================================================================
    public function patientReport()
    {
        $data['title'] = 'Patient Report';
        return view('dashboard.report.report_patient.index', $data);
    }

    public function patientDatatable($startDate = null, $endDate = null, $type = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transactions')->selectRaw('finish_transactions.*');
        $model->where('created_time', '>=', $from);
        $model->where('created_time', '<=', $to);
        if ($type != null && $type != "null" && $type != 0) {
            $model->where('type', '=', $type);
        }
        $model->orderBy('created_time', 'desc');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function patientPrint($startDate = null, $endDate = null, $type = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')->selectRaw('finish_transactions.*');
        $query->where('created_time', '>=', $from);
        $query->where('created_time', '<=', $to);
        if ($type != null && $type != "null" && $type != 0) {
            $query->where('type', $type);
        }
        $query->orderBy('created_time', 'desc');

        $data["patientData"] = $query->get();

        if ($type != null && $type != "null" && $type != 0) {
            if ($type == 'rawat_inap') {
                $data["type"] = 'Rawat Inap';
            } else if ($type == 'rawat_jalan') {
                $data["type"] = 'Rawat Jalan';
            } else {
                $data["type"] = 'IGD';
            }
        } else {
            $data["type"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_patient.print', $data);
    }

    //===============================================================================================
    // PATIENT DETAIL REPORT
    //===============================================================================================
    public function patientDetailReport()
    {
        $data['title'] = 'Patient Detail Report';
        return view('dashboard.report.report_patient_detail.index', $data);
    }

    public function patientDetailDatatable($startDate = null, $endDate = null, $patient_id = null)
    {
        $query = DB::table('finish_transaction_tests')->select('finish_transaction_tests.finish_transaction_id', 'finish_transaction_tests.input_time', 'finish_transaction_tests.test_name', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->where('finish_transactions.patient_id', $patient_id);
        $query->whereRaw("date(finish_transaction_tests.input_time) between '" . $startDate . "' and '" . $endDate . "'");

        return DataTables::of($query)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function patientDetailPrint($startDate = null, $endDate = null, $patient_id = null)
    {
        $query = DB::table('finish_transaction_tests')->select('finish_transaction_tests.finish_transaction_id', 'finish_transaction_tests.input_time', 'finish_transaction_tests.test_name', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->where('finish_transactions.patient_id', $patient_id);
        $query->whereRaw("date(finish_transaction_tests.input_time) between '" . $startDate . "' and '" . $endDate . "'");

        $data["patientData"] = $query->get();

        if ($patient_id != null && $patient_id != "null" && $patient_id != 0) {
            $query_patient = DB::table('patients')->select('patients.name as patient_name')
                ->where('id', '=', $patient_id);
            $patient = $query_patient->first();
            $data['patient'] = $patient;
        } else {
            $data['patient'] = '';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_patient_detail.print', $data);
    }

    //===============================================================================================
    // TEST REPORT
    //===============================================================================================
    public function testReport()
    {
        $data['title'] = 'Test Report';
        return view('dashboard.report.report_test.index', $data);
    }

    public function testDatatable($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        // query package from finish_transaction_tests
        $query_package = DB::table('finish_transaction_tests')
            ->select('package_id', 'package_name')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->where('package_id', '!=', null);
        $query_package->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('package_name', 'asc')
            ->groupBy('package_id');
        $packageData = $query_package->get();

        // query single test from finish_transaction_tests
        $query_single = DB::table('finish_transaction_tests')
            ->select('test_id', 'test_name')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->where('package_id', '=', null)
            ->where('package_name', '=', null);
        $query_single->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('test_name', 'asc')
            ->groupBy('test_id');
        $singleData = $query_single->get();

        $arrayMerge = collect($packageData)->merge($singleData);

        foreach ($arrayMerge as $key => $value) {
            if (isset($value->package_id)) {
                $arrayMerge[$key]->type = 'package';
                $arrayMerge[$key]->test_id = '';
                $arrayMerge[$key]->test_name = '';
                $arrayMerge[$key]->total = 0;
            }

            if ($value->test_id != '') {
                $arrayMerge[$key]->type = 'single';
                $arrayMerge[$key]->package_id = '';
                $arrayMerge[$key]->package_name = '';
                $arrayMerge[$key]->total = 0;
            }
        }

        // COUNT PACKAGE 
        $query_package = DB::table('finish_transaction_tests')->select('finish_transaction_tests.package_id', 'finish_transaction_tests.package_name', 'finish_transaction_tests.type', 'finish_transaction_tests.input_time');
        $query_package->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.package_id', '!=', null)
            ->orderBy('finish_transactions.created_time', 'asc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.package_id');

        $countDataPackage = $query_package->get();

        // COUNT SINGLE 
        $query_single = DB::table('finish_transaction_tests')->select('finish_transaction_tests.test_id', 'finish_transaction_tests.test_name', 'finish_transaction_tests.type', 'finish_transaction_tests.input_time');
        $query_single->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.package_id', '=', null)
            ->where('finish_transaction_tests.package_name', '=', null)
            ->orderBy('finish_transactions.created_time', 'asc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.test_id');

        $countDataSingle = $query_single->get();

        foreach ($arrayMerge as $key => $value) {
            // count package total
            foreach ($countDataPackage as $key2 => $value2) {
                if ($value2->package_id == $value->package_id) {
                    $arrayMerge[$key]->total++;
                }
            }

            // count single total
            foreach ($countDataSingle as $key3 => $value3) {
                if ($value3->test_id == $value->test_id) {
                    $arrayMerge[$key]->total++;
                }
            }
        }

        return DataTables::of($arrayMerge)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function testPrint($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        // query package from finish_transaction_tests
        $query_package = DB::table('finish_transaction_tests')
            ->select('package_id', 'package_name')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->where('package_id', '!=', null);
        $query_package->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('package_name', 'asc')
            ->groupBy('package_id');
        $packageData = $query_package->get();

        // query single test from finish_transaction_tests
        $query_single = DB::table('finish_transaction_tests')
            ->select('test_id', 'test_name')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->where('package_id', '=', null)
            ->where('package_name', '=', null);
        $query_single->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('test_name', 'asc')
            ->groupBy('test_id');
        $singleData = $query_single->get();

        $arrayMerge = collect($packageData)->merge($singleData);

        foreach ($arrayMerge as $key => $value) {
            if (isset($value->package_id)) {
                $arrayMerge[$key]->type = 'package';
                $arrayMerge[$key]->test_id = '';
                $arrayMerge[$key]->test_name = '';
                $arrayMerge[$key]->total = 0;
            }

            if ($value->test_id != '') {
                $arrayMerge[$key]->type = 'single';
                $arrayMerge[$key]->package_id = '';
                $arrayMerge[$key]->package_name = '';
                $arrayMerge[$key]->total = 0;
            }
        }

        // COUNT PACKAGE 
        $query_package = DB::table('finish_transaction_tests')->select('finish_transaction_tests.package_id', 'finish_transaction_tests.package_name', 'finish_transaction_tests.type', 'finish_transaction_tests.input_time');
        $query_package->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.package_id', '!=', null)
            ->orderBy('finish_transactions.created_time', 'asc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.package_id');

        $countDataPackage = $query_package->get();

        // COUNT SINGLE 
        $query_single = DB::table('finish_transaction_tests')->select('finish_transaction_tests.test_id', 'finish_transaction_tests.test_name', 'finish_transaction_tests.type', 'finish_transaction_tests.input_time');
        $query_single->leftJoin('finish_transactions', 'finish_transaction_tests.finish_transaction_id', '=', 'finish_transactions.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.package_id', '=', null)
            ->where('finish_transaction_tests.package_name', '=', null)
            ->orderBy('finish_transactions.created_time', 'asc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.test_id');

        $countDataSingle = $query_single->get();

        foreach ($arrayMerge as $key => $value) {
            // count package total
            foreach ($countDataPackage as $key2 => $value2) {
                if ($value2->package_id == $value->package_id) {
                    $arrayMerge[$key]->total++;
                }
            }

            // count single total
            foreach ($countDataSingle as $key3 => $value3) {
                if ($value3->test_id == $value->test_id) {
                    $arrayMerge[$key]->total++;
                }
            }
        }

        $data['testData'] = $arrayMerge;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_test.print', $data);
    }

    //===============================================================================================
    // SARS Cov-2 ANTIGENT REPORT
    //===============================================================================================
    public function sarsCovReport()
    {
        $data['title'] = 'Sars Cov-2 Report';
        return view('dashboard.report.report_sars_cov.index', $data);
    }

    public function sarsCovDatatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.*', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.test_id', 856)
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id');
        $patientData = $query->get();

        return DataTables::of($patientData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function sarsCovPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.*', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.test_id', 856)
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id');
        $patientData = $query->get();

        $data['patientData'] = $patientData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_sars_cov.print', $data);
    }

    //===============================================================================================
    // RAPID HIV REPORT
    //===============================================================================================
    public function rapidHivReport()
    {
        $data['title'] = 'Rapid HIV Report';
        return view('dashboard.report.report_rapid_hiv.index', $data);
    }

    public function rapidHivDatatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.*', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.test_name', 'HIV (RAPID)')
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id');
        $patientData = $query->get();

        return DataTables::of($patientData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function rapidHivPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.*', 'finish_transaction_tests.global_result')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.test_name', 'HIV (RAPID)')
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id');
        $patientData = $query->get();

        $data['patientData'] = $patientData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_rapid_hiv.print', $data);
    }

    //===============================================================================================
    // SPECIMEN REPORT
    //===============================================================================================
    public function specimenReport()
    {
        $data['title'] = 'Specimen Report';
        return view('dashboard.report.report_specimen.index', $data);
    }

    public function specimenDatatable($startDate = null, $endDate = null)
    {
        // ====
        // URINE
        // ====
        $total_urine = 0;
        $urine_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $urine_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $urine_query->where('specimen_name', 'Urine');
        $urine_query->groupBy('transaction_id');
        $data_urine = $urine_query->get();
        foreach ($data_urine as $urine) {
            $total_urine++;
        }

        // ====
        // SERUM
        // ====
        $total_serum = 0;
        $query_serum = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $query_serum->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $query_serum->where('specimen_name', 'Serum');
        $query_serum->groupBy('transaction_id');
        $data_serum = $query_serum->get();
        foreach ($data_serum as $serum) {
            $total_serum++;
        }

        // ====
        // EDTA
        // ====
        $total_edta = 0;
        $edta_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $edta_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $edta_query->where('specimen_name', 'DARAH EDTA');
        $edta_query->groupBy('transaction_id');
        $data_edta = $edta_query->get();
        foreach ($data_edta as $edta) {
            $total_edta++;
        }

        // ====
        // VAGINAL-SECRETION
        // ====
        $total_vaginal = 0;
        $vaginal_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $vaginal_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $vaginal_query->where('specimen_name', 'Vaginal-Secretion');
        $vaginal_query->groupBy('transaction_id');
        $data_vaginal = $vaginal_query->get();
        foreach ($data_vaginal as $vaginal) {
            $total_vaginal++;
        }

        // ====
        // SERUM 2JPP
        // ====
        $total_serum_2jam = 0;
        $serum_2jam_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $serum_2jam_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $serum_2jam_query->where('specimen_name', 'Serum 2JPP');
        $serum_2jam_query->groupBy('transaction_id');
        $data_serum_2jam_ = $serum_2jam_query->get();
        foreach ($data_serum_2jam_ as $cairan_tubuh) {
            $total_serum_2jam++;
        }

        // ====
        // FESES
        // ====
        $total_feses = 0;
        $feses_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $feses_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $feses_query->where('specimen_name', 'FESES');
        $feses_query->groupBy('transaction_id');
        $data_feses = $feses_query->get();
        foreach ($data_feses as $feses) {
            $total_feses++;
        }

        // ====
        // SPUTUM
        // ====
        $total_sputum = 0;
        $sputum_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $sputum_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $sputum_query->where('specimen_name', 'Sputum');
        $sputum_query->groupBy('transaction_id');
        $data_sputum = $sputum_query->get();
        foreach ($data_sputum as $sputum) {
            $total_sputum++;
        }

        // ====
        // PLASMA SITRAT
        // ====
        $total_sitrat = 0;
        $sitrat_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $sitrat_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $sitrat_query->where('specimen_name', 'Plasma Sitrat');
        $sitrat_query->groupBy('transaction_id');
        $data_sitrat = $sitrat_query->get();
        foreach ($data_sitrat as $sitrat) {
            $total_sitrat++;
        }

        // ====
        // PLASMA LED
        // ====
        $total_led = 0;
        $led_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $led_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $led_query->where('specimen_name', 'Plasma LED');
        $led_query->groupBy('transaction_id');
        $data_led = $led_query->get();
        foreach ($data_led as $led) {
            $total_led++;
        }

        // ====
        // CAIRAN TUBUH
        // ====
        $total_cairan_tubuh = 0;
        $ct_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $ct_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $ct_query->where('specimen_name', 'Cairan Tubuh');
        $ct_query->groupBy('transaction_id');
        $data_ct = $ct_query->get();
        foreach ($data_ct as $cairan_tubuh) {
            $total_cairan_tubuh++;
        }

        // ====
        // LAIN LAIN
        // ====
        $total_lain_lain = 0;
        $lain_lain_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $lain_lain_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $lain_lain_query->where('specimen_name', 'Lain lain');
        $lain_lain_query->groupBy('transaction_id');
        $data_lain_lain = $lain_lain_query->get();
        foreach ($data_lain_lain as $lain_lain) {
            $total_lain_lain++;
        }

        // ====
        // SPECIMEN ML
        // ====
        $total_specimen_ml = 0;
        $specimen_ml_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_ml_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_ml_query->where('specimen_name', 'Lain lain');
        $specimen_ml_query->groupBy('transaction_id');
        $data_specimen_ml = $specimen_ml_query->get();
        foreach ($data_specimen_ml as $ml) {
            $total_specimen_ml++;
        }

        // ====
        // SPECIMEN JARINGAN
        // ====
        $total_jaringan = 0;
        $specimen_jaringan_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_jaringan_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_jaringan_query->where('specimen_name', 'Lain lain');
        $specimen_jaringan_query->groupBy('transaction_id');
        $data_specimen_jaringan = $specimen_jaringan_query->get();
        foreach ($data_specimen_jaringan as $jaringan) {
            $total_jaringan++;
        }

        // ====
        // SPECIMEN PUS
        // ====
        $total_pus = 0;
        $specimen_pus_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_pus_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_pus_query->where('specimen_name', 'Lain lain');
        $specimen_pus_query->groupBy('transaction_id');
        $data_specimen_pus = $specimen_pus_query->get();
        foreach ($data_specimen_pus as $pus) {
            $total_pus++;
        }

        $query = DB::table('specimens')->select('id', 'name as specimen_name')->orderBy('id', 'asc');
        $specimens = $query->get();

        foreach ($specimens as $specimen => $value) {
            if ($value->specimen_name == 'Urine') {
                $specimens[$specimen]->total = $total_urine;
            } else if ($value->specimen_name == 'Serum') {
                $specimens[$specimen]->total = $total_serum;
            } else if ($value->specimen_name == 'Darah EDTA') {
                $specimens[$specimen]->total = $total_edta;
            } else if ($value->specimen_name == 'Vaginal-Secretion') {
                $specimens[$specimen]->total = $total_vaginal;
            } else if ($value->specimen_name == 'Serum 2JPP') {
                $specimens[$specimen]->total = $total_serum_2jam;
            } else if ($value->specimen_name == 'Feses') {
                $specimens[$specimen]->total = $total_feses;
            } else if ($value->specimen_name == 'Sputum') {
                $specimens[$specimen]->total = $total_sputum;
            } else if ($value->specimen_name == 'Plasma Sitrat') {
                $specimens[$specimen]->total = $total_sitrat;
            } else if ($value->specimen_name == 'Plasma LED') {
                $specimens[$specimen]->total = $total_led;
            } else if ($value->specimen_name == 'Cairan Tubuh') {
                $specimens[$specimen]->total = $total_cairan_tubuh;
            } else if ($value->specimen_name == 'Lain lain') {
                $specimens[$specimen]->total = $total_lain_lain;
            } else if ($value->specimen_name == 'Specimen ML') {
                $specimens[$specimen]->total = $total_specimen_ml;
            } else if ($value->specimen_name == 'Specimen M/L') {
                $specimens[$specimen]->total = $total_specimen_ml;
            } else if ($value->specimen_name == 'Jaringan') {
                $specimens[$specimen]->total = $total_jaringan;
            } else if ($value->specimen_name == 'PUS') {
                $specimens[$specimen]->total = $total_pus;
            }
        }

        return DataTables::of($specimens)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function specimenPrint($startDate = null, $endDate = null)
    {
        // ====
        // URINE
        // ====
        $total_urine = 0;
        $urine_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $urine_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $urine_query->where('specimen_name', 'Urine');
        $urine_query->groupBy('transaction_id');
        $data_urine = $urine_query->get();
        foreach ($data_urine as $urine) {
            $total_urine++;
        }

        // ====
        // SERUM
        // ====
        $total_serum = 0;
        $query_serum = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $query_serum->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $query_serum->where('specimen_name', 'Serum');
        $query_serum->groupBy('transaction_id');
        $data_serum = $query_serum->get();
        foreach ($data_serum as $serum) {
            $total_serum++;
        }

        // ====
        // EDTA
        // ====
        $total_edta = 0;
        $edta_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $edta_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $edta_query->where('specimen_name', 'DARAH EDTA');
        $edta_query->groupBy('transaction_id');
        $data_edta = $edta_query->get();
        foreach ($data_edta as $edta) {
            $total_edta++;
        }

        // ====
        // VAGINAL-SECRETION
        // ====
        $total_vaginal = 0;
        $vaginal_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $vaginal_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $vaginal_query->where('specimen_name', 'Vaginal-Secretion');
        $vaginal_query->groupBy('transaction_id');
        $data_vaginal = $vaginal_query->get();
        foreach ($data_vaginal as $vaginal) {
            $total_vaginal++;
        }

        // ====
        // SERUM 2JPP
        // ====
        $total_serum_2jam = 0;
        $serum_2jam_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $serum_2jam_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $serum_2jam_query->where('specimen_name', 'Serum 2JPP');
        $serum_2jam_query->groupBy('transaction_id');
        $data_serum_2jam_ = $serum_2jam_query->get();
        foreach ($data_serum_2jam_ as $cairan_tubuh) {
            $total_serum_2jam++;
        }

        // ====
        // FESES
        // ====
        $total_feses = 0;
        $feses_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $feses_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $feses_query->where('specimen_name', 'FESES');
        $feses_query->groupBy('transaction_id');
        $data_feses = $feses_query->get();
        foreach ($data_feses as $feses) {
            $total_feses++;
        }

        // ====
        // SPUTUM
        // ====
        $total_sputum = 0;
        $sputum_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $sputum_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $sputum_query->where('specimen_name', 'Sputum');
        $sputum_query->groupBy('transaction_id');
        $data_sputum = $sputum_query->get();
        foreach ($data_sputum as $sputum) {
            $total_sputum++;
        }

        // ====
        // PLASMA SITRAT
        // ====
        $total_sitrat = 0;
        $sitrat_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $sitrat_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $sitrat_query->where('specimen_name', 'Plasma Sitrat');
        $sitrat_query->groupBy('transaction_id');
        $data_sitrat = $sitrat_query->get();
        foreach ($data_sitrat as $sitrat) {
            $total_sitrat++;
        }

        // ====
        // PLASMA LED
        // ====
        $total_led = 0;
        $led_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $led_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $led_query->where('specimen_name', 'Plasma LED');
        $led_query->groupBy('transaction_id');
        $data_led = $led_query->get();
        foreach ($data_led as $led) {
            $total_led++;
        }

        // ====
        // CAIRAN TUBUH
        // ====
        $total_cairan_tubuh = 0;
        $ct_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $ct_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $ct_query->where('specimen_name', 'Cairan Tubuh');
        $ct_query->groupBy('transaction_id');
        $data_ct = $ct_query->get();
        foreach ($data_ct as $cairan_tubuh) {
            $total_cairan_tubuh++;
        }

        // ====
        // LAIN LAIN
        // ====
        $total_lain_lain = 0;
        $lain_lain_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $lain_lain_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $lain_lain_query->where('specimen_name', 'Lain lain');
        $lain_lain_query->groupBy('transaction_id');
        $data_lain_lain = $lain_lain_query->get();
        foreach ($data_lain_lain as $lain_lain) {
            $total_lain_lain++;
        }

        // ====
        // SPECIMEN ML
        // ====
        $total_specimen_ml = 0;
        $specimen_ml_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_ml_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_ml_query->where('specimen_name', 'Lain lain');
        $specimen_ml_query->groupBy('transaction_id');
        $data_specimen_ml = $specimen_ml_query->get();
        foreach ($data_specimen_ml as $ml) {
            $total_specimen_ml++;
        }

        // ====
        // SPECIMEN JARINGAN
        // ====
        $total_jaringan = 0;
        $specimen_jaringan_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_jaringan_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_jaringan_query->where('specimen_name', 'Lain lain');
        $specimen_jaringan_query->groupBy('transaction_id');
        $data_specimen_jaringan = $specimen_jaringan_query->get();
        foreach ($data_specimen_jaringan as $jaringan) {
            $total_jaringan++;
        }

        // ====
        // SPECIMEN PUS
        // ====
        $total_pus = 0;
        $specimen_pus_query = DB::table('finish_transaction_tests')
            ->select('transaction_id', 'test_name', 'specimen_name', 'specimen_id');
        if (($startDate != null) && ($endDate != null)) {
            $specimen_pus_query->whereRaw("date(input_time) between '" . $startDate . "' and '" . $endDate . "'");
        }
        $specimen_pus_query->where('specimen_name', 'Lain lain');
        $specimen_pus_query->groupBy('transaction_id');
        $data_specimen_pus = $specimen_pus_query->get();
        foreach ($data_specimen_pus as $pus) {
            $total_pus++;
        }

        $query = DB::table('specimens')->select('id', 'name as specimen_name')->orderBy('id', 'asc');
        $specimens = $query->get();

        foreach ($specimens as $specimen => $value) {
            if ($value->specimen_name == 'Urine') {
                $specimens[$specimen]->total = $total_urine;
            } else if ($value->specimen_name == 'Serum') {
                $specimens[$specimen]->total = $total_serum;
            } else if ($value->specimen_name == 'Darah EDTA') {
                $specimens[$specimen]->total = $total_edta;
            } else if ($value->specimen_name == 'Vaginal-Secretion') {
                $specimens[$specimen]->total = $total_vaginal;
            } else if ($value->specimen_name == 'Serum 2JPP') {
                $specimens[$specimen]->total = $total_serum_2jam;
            } else if ($value->specimen_name == 'Feses') {
                $specimens[$specimen]->total = $total_feses;
            } else if ($value->specimen_name == 'Sputum') {
                $specimens[$specimen]->total = $total_sputum;
            } else if ($value->specimen_name == 'Plasma Sitrat') {
                $specimens[$specimen]->total = $total_sitrat;
            } else if ($value->specimen_name == 'Plasma LED') {
                $specimens[$specimen]->total = $total_led;
            } else if ($value->specimen_name == 'Cairan Tubuh') {
                $specimens[$specimen]->total = $total_cairan_tubuh;
            } else if ($value->specimen_name == 'Lain lain') {
                $specimens[$specimen]->total = $total_lain_lain;
            } else if ($value->specimen_name == 'Specimen ML') {
                $specimens[$specimen]->total = $total_specimen_ml;
            } else if ($value->specimen_name == 'Specimen M/L') {
                $specimens[$specimen]->total = $total_specimen_ml;
            } else if ($value->specimen_name == 'Jaringan') {
                $specimens[$specimen]->total = $total_jaringan;
            } else if ($value->specimen_name == 'PUS') {
                $specimens[$specimen]->total = $total_pus;
            }
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        $data['specimenData'] = $specimens;

        return view('dashboard.report.report_specimen.print', $data);
    }

    //===============================================================================================
    // PHLEBOTOMY & SAMPLING REPORT
    //===============================================================================================
    public function flebotomiSamplingReport()
    {
        $data['title'] = 'Phlebotomy & Sampling Report';
        return view('dashboard.report.report_flebotomi_sampling.index', $data);
    }

    public function flebotomiSamplingDatatable($startDate = null, $endDate = null, $specimen_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.draw_time, finish_transaction_tests.draw_by_name');
        $model->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $model->where('finish_transactions.created_time', '>=', $from);
        $model->where('finish_transactions.created_time', '<=', $to);
        if ($specimen_id != null && $specimen_id != "null" && $specimen_id != 0) {
            $model->where('finish_transaction_tests.group_id', '=', $specimen_id);
        }
        $model->orderBy('finish_transactions.created_time', 'desc');
        $model->groupBy('finish_transactions.id');
        // $model->groupBy('finish_transaction_tests.specimen_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function flebotomiSamplingPrint($startDate = null, $endDate = null, $specimen_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')->selectRaw('finish_transactions.*, finish_transaction_tests.draw_time, finish_transaction_tests.draw_by_name');
        $query->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $query->where('finish_transactions.created_time', '>=', $from);
        $query->where('finish_transactions.created_time', '<=', $to);
        if ($specimen_id != null && $specimen_id != "null" && $specimen_id != 0) {
            $query->where('finish_transaction_tests.group_id', '=', $specimen_id);
        }
        $query->orderBy('finish_transactions.created_time', 'desc');
        $query->groupBy('finish_transactions.id');

        $data["samplingData"] = $query->get();

        if ($specimen_id != null && $specimen_id != "null" && $specimen_id != 0) {
            $query_specimen = DB::table('specimens')->select('name as specimen_name');
            $specimen = $query_specimen->first();
            $data["specimen"] = $specimen->specimen_name;
        } else {
            $data["specimen"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_flebotomi_sampling.print', $data);
    }

    //===============================================================================================
    // VERIFICATION & VALIDATION REPORT
    //===============================================================================================
    public function verificationValidationReport()
    {
        $data['title'] = 'Verification & Validation Report';
        return view('dashboard.report.report_verification_validation.index', $data);
    }

    public function verificationValidationDatatable($startDate = null, $endDate = null, $test_id = null)
    {
        if ($startDate == null && $endDate == null) {
            // $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            // $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
            $from = date('Y-m-d');
            $to = date('Y-m-d');
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = "SELECT users.id, users.name, 
            (SELECT COUNT(validate_by)
            FROM finish_transaction_tests tt1
            WHERE tt1.validate_by = users.id AND DATE(tt1.input_time) BETWEEN '" . $from . "' AND  '" . $to . "') AS validator,
            (SELECT COUNT(verify_by)
            FROM finish_transaction_tests tt2
            WHERE tt2.verify_by = users.id AND DATE(tt2.input_time) BETWEEN '" . $from . "' AND  '" . $to . "') AS verifikator
        FROM users
        ORDER BY validator DESC , verifikator desc";

        $model = DB::select(DB::raw($query));


        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);

        // query 1
        // $user_query = DB::table('users')->select('users.id as user_id', 'users.name as user_name')->orderBy('name');
        // $user_data = $user_query->get();

        // foreach ($user_data as $key => $value) {
        //     echo 'Id : ' . $value->user_id . '<br>';
        //     echo 'User : ' . $value->user_name . '<br>';
        // }

        // return response()->json(['user' => $user_data, 'verification' => $verification_data, 'validation' => $validation_data]);

        // query 2
        // $query = DB::table('finish_transaction_tests');
        // $query->select('users.name as analyst_name', DB::raw('COUNT(finish_transaction_tests.verify_by) as jumlah_verifikasi'), DB::raw('COUNT(finish_transaction_tests.validate_by) as jumlah_validasi'));
        // $query->leftJoin('users', 'finish_transaction_tests.verify_by', '=', 'users.id');
        // if ($test_id != null) {
        //     $query->where('test_id', $test_id);
        // }
        // $query->groupBy('users.id');
        // $data = $query->get();

        // return response()->json($data);
    }

    public function verificationValidationPrint($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')->select('finish_transaction_tests.id', 'users.name as user_name', DB::raw('COUNT(finish_transaction_tests.verify_by) as jumlah_verifikasi'), DB::raw('COUNT(finish_transaction_tests.validate_by) as jumlah_validasi'));
        $query->leftJoin('users', 'finish_transaction_tests.verify_by', '=', 'users.id');
        $query->where('finish_transaction_tests.input_time', '>=', $from);
        $query->where('finish_transaction_tests.input_time', '<=', $to);
        if ($test_id != null && $test_id != "null" && $test_id != 0) {
            $query->where('test_id', $test_id);
        }
        $query->orderBy('users.name', 'asc');
        $query->groupBy('users.id');

        $data['verf_val_data'] = $query->get();

        if ($test_id != null && $test_id != "null" && $test_id != 0) {
            $query_test = DB::table('finish_transaction_tests')->select('finish_transaction_tests.test_name')->where('test_id', $test_id);
            $test = $query_test->first();
            $data["test_name"] = $test->test_name;
        } else {
            $data["test_name"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_verification_validation.print', $data);
    }

    //===============================================================================================
    // INSURANCE REPORT
    //===============================================================================================
    public function insuranceReport()
    {
        $data['title'] = 'Insurance Report';
        return view('dashboard.report.report_insurance.index', $data);
    }

    public function insuranceDatatable($startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = DB::table('finish_transactions')->select('id', 'created_time', 'insurance_name', DB::raw('COUNT(insurance_id) as total_pasien'))
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->orderBy('insurance_id', 'asc')
            ->groupBy('insurance_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function insurancePrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')->select('id', 'created_time', 'insurance_name', DB::raw('COUNT(insurance_id) as total_pasien'))
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to)
            ->orderBy('insurance_id', 'asc')
            ->groupBy('insurance_id');

        $data['insuranceData'] = $query->get();

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_insurance.print', $data);
    }

    //===============================================================================================
    // BPJS REPORT
    //===============================================================================================
    public function bpjsReport()
    {
        $data['title'] = 'BPJS Report';
        return view('dashboard.report.report_bpjs.index', $data);

        // $from = '2023-04-12';
        // $to = '2023-04-12';

        // $query = DB::table('finish_transactions')
        //     ->select('finish_transactions.id', 'finish_transactions.transaction_id', 'finish_transactions.created_time', 'finish_transactions.patient_medrec', 'finish_transactions.patient_name', 'finish_transactions.room_name', 'finish_transaction_tests.group_id', 'finish_transaction_tests.group_name', 'finish_transaction_tests.package_name')
        //     ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
        //     ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
        //     ->where('finish_transactions.insurance_id', 2)
        //     ->orderBy('finish_transactions.created_time', 'desc')
        //     ->groupBy('finish_transactions.id')
        //     ->groupBy('finish_transaction_tests.group_id');
        // $patientData = $query->get();

        // $packageNameArray = [];
        // $testNameArray = [];
        // foreach ($patientData as $key => $value) {
        //     $packageNameArray = [];
        //     $testNameArray = [];

        //     $query_package = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, package_name')
        //         ->where('package_id', '!=', NULL)
        //         ->where('group_id', $value->group_id)
        //         ->where('transaction_id', $value->transaction_id)
        //         ->groupBy('package_id');
        //     $packageData = $query_package->get();

        //     $query_single_test = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, test_name')
        //         ->where('package_id', '=', NULL)
        //         ->where('group_id', $value->group_id)
        //         ->where('transaction_id', $value->transaction_id);
        //     $testData = $query_single_test->get();

        //     // echo '<pre>';
        //     // print_r($testData);

        //     // package name 
        //     if ($packageData) {
        //         foreach ($packageData as $packageData) {
        //             if ($value->group_id == $packageData->group_id) {
        //                 array_push($packageNameArray, $packageData->package_name);
        //             }
        //         }
        //     }
        //     if ($testData) {
        //         foreach ($testData as $test_data) {
        //             if ($value->group_id == $test_data->group_id) {
        //                 array_push($testNameArray, $test_data->test_name);
        //             }
        //         }
        //     }

        //     $packageNameString = implode("\n", $packageNameArray);
        //     $testNameString = implode("\n", $testNameArray);
        //     $patientData[$key]->package_name_custom = $packageNameString;
        //     $patientData[$key]->test_name_custom = $testNameString;
        // }

        // echo '<pre>';
        // print_r($patientData);
        // die;
    }

    public function bpjsDatatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.id', 'finish_transactions.transaction_id', 'finish_transactions.created_time', 'finish_transactions.patient_medrec', 'finish_transactions.patient_name', 'finish_transactions.room_name', 'finish_transaction_tests.group_id', 'finish_transaction_tests.group_name', 'finish_transaction_tests.package_name')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transactions.insurance_id', 2)
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.group_id');
        $patientData = $query->get();

        $packageNameArray = [];
        $testNameArray = [];
        foreach ($patientData as $key => $value) {
            $packageNameArray = [];
            $testNameArray = [];

            $query_package = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, package_name, sequence')
                ->where('package_id', '!=', NULL)
                ->where('group_id', $value->group_id)
                ->where('transaction_id', $value->transaction_id)
                ->orderBy('sequence')
                ->groupBy('package_id');
            $packageData = $query_package->get();

            $query_single_test = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, test_name, sequence')
                ->where('package_id', '=', NULL)
                ->where('group_id', $value->group_id)
                ->where('transaction_id', $value->transaction_id)
                ->orderBy('sequence');
            $testData = $query_single_test->get();

            // echo '<pre>';
            // print_r($testData);

            // package name 
            if ($packageData) {
                foreach ($packageData as $packageData) {
                    if ($value->group_id == $packageData->group_id) {
                        array_push($packageNameArray, $packageData->package_name);
                    }
                }
            }
            if ($testData) {
                foreach ($testData as $test_data) {
                    if ($value->group_id == $test_data->group_id) {
                        array_push($testNameArray, $test_data->test_name);
                    }
                }
            }

            // $packageNameString = implode("\n", $packageNameArray);
            // $testNameString = implode("\n", $testNameArray);
            // $patientData[$key]->package_name_custom = $packageNameString;
            // $patientData[$key]->test_name_custom = $testNameString;

            $array_merge = array_merge($packageNameArray, $testNameArray);
            $testNameString = "<p>" . implode("<br>", $array_merge) . "</p>";
            $patientData[$key]->test_name_merge = $testNameString;
        }

        return DataTables::of($patientData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function bpjsPrint($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transactions')
            ->select('finish_transactions.id', 'finish_transactions.transaction_id', 'finish_transactions.created_time', 'finish_transactions.patient_medrec', 'finish_transactions.patient_name', 'finish_transactions.room_name', 'finish_transaction_tests.group_id', 'finish_transaction_tests.group_name', 'finish_transaction_tests.package_name')
            ->leftJoin('finish_transaction_tests', 'finish_transactions.transaction_id', '=', 'finish_transaction_tests.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transactions.insurance_id', 2)
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transactions.id')
            ->groupBy('finish_transaction_tests.group_id');
        $patientData = $query->get();

        $packageNameArray = [];
        $testNameArray = [];
        foreach ($patientData as $key => $value) {
            $packageNameArray = [];
            $testNameArray = [];

            $query_package = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, package_name, sequence')
                ->where('package_id', '!=', NULL)
                ->where('group_id', $value->group_id)
                ->where('transaction_id', $value->transaction_id)
                ->orderBy('sequence')
                ->groupBy('package_id');
            $packageData = $query_package->get();

            $query_single_test = DB::table('finish_transaction_tests')->selectRaw('finish_transaction_id, group_id, test_name, sequence')
                ->where('package_id', '=', NULL)
                ->where('group_id', $value->group_id)
                ->where('transaction_id', $value->transaction_id)
                ->orderBy('sequence');
            $testData = $query_single_test->get();

            // echo '<pre>';
            // print_r($testData);

            // package name 
            if ($packageData) {
                foreach ($packageData as $packageData) {
                    if ($value->group_id == $packageData->group_id) {
                        array_push($packageNameArray, $packageData->package_name);
                    }
                }
            }
            if ($testData) {
                foreach ($testData as $test_data) {
                    if ($value->group_id == $test_data->group_id) {
                        array_push($testNameArray, $test_data->test_name);
                    }
                }
            }

            // $packageNameString = implode("\n", $packageNameArray);
            // $testNameString = implode("\n", $testNameArray);
            // $patientData[$key]->package_name_custom = $packageNameString;
            // $patientData[$key]->test_name_custom = $testNameString;

            $array_merge = array_merge($packageNameArray, $testNameArray);
            $testNameString = "<p>" . implode("<br>", $array_merge) . "</p>";
            $patientData[$key]->test_name_merge = $testNameString;
        }

        $data['patientData'] = $patientData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_bpjs.print', $data);
    }

    //===============================================================================================
    // BILLING REPORT
    //===============================================================================================
    public function billingReport()
    {
        $data['title'] = 'Billing Report';
        return view('dashboard.report.report_billing.index', $data);
    }

    public function billingDatatable($startDate = null, $endDate = null, $insurance_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $queryPackagePrice = "(select price from prices where package_id = finish_transaction_tests.package_id LIMIT 1) as package_price";
        $queryTestPrice = "(select price from prices where test_id = finish_transaction_tests.test_id LIMIT 1) as test_price";

        $model = DB::table('finish_transactions')->selectRaw("finish_transactions.*, finish_transaction_tests.test_name, finish_transaction_tests.package_id, finish_transaction_tests.package_name, prices.price, $queryPackagePrice, $queryTestPrice");
        $model->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $model->leftJoin('prices', 'finish_transaction_tests.price_id', '=', 'prices.id');
        $model->where('finish_transactions.created_time', '>=', $from);
        $model->where('finish_transactions.created_time', '<=', $to);
        if ($insurance_id != null && $insurance_id != "null" && $insurance_id != 0) {
            $model->where('finish_transactions.insurance_id', '=', $insurance_id);
        }
        $model->orderBy('finish_transactions.created_time', 'desc');
        // $query->groupBy('finish_transaction_tests.package_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function billingPrint($startDate = null, $endDate = null, $insurance_id = null)
    {
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $queryPackagePrice = "(select price from prices where package_id = finish_transaction_tests.package_id LIMIT 1) as package_price";
        $queryTestPrice = "(select price from prices where test_id = finish_transaction_tests.test_id LIMIT 1) as test_price";

        $query = DB::table('finish_transactions')->selectRaw("finish_transactions.*, finish_transaction_tests.test_name, finish_transaction_tests.package_id, finish_transaction_tests.package_name, prices.price, $queryPackagePrice, $queryTestPrice");
        $query->leftJoin('finish_transaction_tests', 'finish_transactions.id', '=', 'finish_transaction_tests.finish_transaction_id');
        $query->leftJoin('prices', 'finish_transaction_tests.price_id', '=', 'prices.id');
        $query->where('finish_transactions.created_time', '>=', $from);
        $query->where('finish_transactions.created_time', '<=', $to);
        if ($insurance_id != null && $insurance_id != "null" && $insurance_id != 0) {
            $query->where('finish_transactions.insurance_id', '=', $insurance_id);
        }
        $query->orderBy('finish_transactions.created_time', 'desc');
        // $query->groupBy('finish_transaction_tests.package_id');

        $data["insuranceData"] = $query->get();

        if ($insurance_id != null && $insurance_id != "null" && $insurance_id != 0) {
            $query_insurance = DB::table('insurances')->select('name as insurance_name');
            $insurance = $query_insurance->first();
            $data["insurance"] = $insurance->insurance_name;
        } else {
            $data["insurance"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_billing.print', $data);
    }

    //===============================================================================================
    // DOCTOR REPORT
    //===============================================================================================
    public function doctorReport()
    {
        $data['title'] = 'Doctor Report';
        return view('dashboard.report.report_doctor.index', $data);
    }

    public function doctorDatatable($startDate = null, $endDate = null, $doctor_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $model = \App\FinishTransaction::selectRaw('COUNT(finish_transactions.patient_id) as total_patient, finish_transactions.id, finish_transactions.doctor_name, finish_transactions.created_time')
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to);
        if ($doctor_id != null && $doctor_id != "null" && $doctor_id != 0) {
            $model->where('finish_transactions.doctor_id', '=', $doctor_id);
        }
        $model->groupBy('doctor_id');

        return DataTables::of($model)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function doctorPrint($startDate = null, $endDate = null, $doctor_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = \App\FinishTransaction::selectRaw('COUNT(finish_transactions.patient_id) as total_patient, finish_transactions.id, finish_transactions.doctor_name, finish_transactions.created_time')
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to);
        if ($doctor_id != null && $doctor_id != "null" && $doctor_id != 0) {
            $query->where('finish_transactions.doctor_id', '=', $doctor_id);
        }
        $query->groupBy('doctor_id');

        $data["doctorData"] = $query->get();

        if ($doctor_id != null && $doctor_id != "null" && $doctor_id != 0) {
            $query_doctor = DB::table('doctors')->select('doctors.*')->where('id', $doctor_id);
            $doctor = $query_doctor->first();
            $data["doctor"] = $doctor->name;
        } else {
            $data["doctor"] = '-';
        }

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_doctor.print', $data);
    }

    //===============================================================================================
    // VISIT REPORT
    //===============================================================================================
    public function visitReport()
    {
        $data['title'] = 'Visit Report';
        return view('dashboard.report.report_visit.index', $data);
    }

    public function visitDatatable($startDate = null, $endDate = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $queryPatientData = DB::table('finish_transactions')->selectRaw('id, patient_id, patient_medrec, patient_name, COUNT(patient_id) as total_visit, created_time')
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to);
        $queryPatientData->groupBy('patient_id');
        $patientData = $queryPatientData->get();

        return DataTables::of($patientData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function visitPrint($startDate = null, $endDate = null, $doctor_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $queryPatientData = DB::table('finish_transactions')->selectRaw('id, patient_id, patient_medrec, patient_name, COUNT(patient_id) as total_visit, created_time')
            ->where('created_time', '>=', $from)
            ->where('created_time', '<=', $to);
        $queryPatientData->groupBy('patient_id');
        $patientData = $queryPatientData->get();

        $data['patientData'] = $patientData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_visit.print', $data);
    }

    //===============================================================================================
    // ANALYZER REPORT
    //===============================================================================================
    public function analyzerReport()
    {
        $data['title'] = 'Analyzer Report';
        return view('dashboard.report.report_analyzer.index', $data);
        
    }

    public function analyzerDatatable($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')
            ->selectRaw('finish_transaction_tests.id, finish_transaction_tests.analyzer_id, finish_transaction_tests.analyzer_name, COUNT(finish_transaction_tests.analyzer_id) as total_test')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.analyzer_id', '!=', null)
            ->groupBy('finish_transaction_tests.analyzer_id');
        $analyzerData = $query->get();

        // echo '<pre>';
        // print_r($analyzerData);

        return DataTables::of($analyzerData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function analyzerPrint($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')
            ->selectRaw('finish_transaction_tests.id, finish_transaction_tests.analyzer_id, finish_transaction_tests.analyzer_name, COUNT(finish_transaction_tests.analyzer_id) as total_test')
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->where('finish_transaction_tests.analyzer_id', '!=', null)
            ->groupBy('finish_transaction_tests.analyzer_id');
        $analyzerData = $query->get();

        // echo '<pre>';
        // print_r($analyzerData);

        $data['analyzerData'] = $analyzerData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_analyzer.print', $data);
    }

    //===============================================================================================
    // USER REPORT
    //===============================================================================================
    public function userReport()
    {
        $data['title'] = 'User Report';
        return view('dashboard.report.report_user.index', $data); 
    }

    public function userDatatable($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('users')->select('id as user_id', 'name as user');
        $userData = $query->get();

        $query_process = DB::table('finish_transaction_tests')
            ->select(
                'finish_transactions.transaction_id',
                'finish_transactions.created_time',
                'finish_transactions.no_lab',
                'finish_transaction_tests.type',
                'finish_transaction_tests.package_id',
                'finish_transaction_tests.package_name',
                'finish_transaction_tests.test_id',
                'finish_transaction_tests.test_name',
                'finish_transactions.checkin_by as checkin_by_id',
                'users.name as checkin_by_name',
                'finish_transaction_tests.draw_by as draw_by_id',
                'finish_transaction_tests.draw_by_name',
                'finish_transaction_tests.verify_by as verify_by_id',
                'finish_transaction_tests.verify_by_name',
                'finish_transaction_tests.validate_by as validate_by_id',
                'finish_transaction_tests.validate_by_name'
            )
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->leftJoin('users', 'finish_transactions.checkin_by', '=', 'users.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transaction_tests.package_id')
            ->groupBy('finish_transactions.transaction_id');
        $processData = $query_process->get();

        // echo '<pre>';
        // print_r($processData);
        // die;

        foreach($userData as $key => $value){
            
            if(!isset($userData[$key]->checkin)){
                $userData[$key]->checkin = 0;
            }
            if(!isset($userData[$key]->draw)){
                $userData[$key]->draw = 0;
            }
            if(!isset($userData[$key]->process)){
                $userData[$key]->process = 0;
            }
            if(!isset($userData[$key]->verify)){
                $userData[$key]->verify = 0;
            }
            if(!isset($userData[$key]->validate)){
                $userData[$key]->validate = 0;
            }

            foreach($processData as $process_data){
                if($value->user_id == $process_data->checkin_by_id){
                    $userData[$key]->checkin++;
                }
                if($value->user_id == $process_data->draw_by_id){
                    $userData[$key]->draw++;
                }
                if($value->user_id == $process_data->draw_by_id){
                    $userData[$key]->process++;
                }
                if($value->user_id == $process_data->verify_by_id){
                    $userData[$key]->verify++;
                }
                if($value->user_id == $process_data->validate_by_id){
                    $userData[$key]->validate++;
                }
            }
            
        }

        // echo '<pre>';
        // print_r($userData);

        return DataTables::of($userData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function userPrint($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('users')->select('id as user_id', 'name as user');
        $userData = $query->get();

        $query_process = DB::table('finish_transaction_tests')
            ->select(
                'finish_transactions.transaction_id',
                'finish_transactions.created_time',
                'finish_transactions.no_lab',
                'finish_transaction_tests.type',
                'finish_transaction_tests.package_id',
                'finish_transaction_tests.package_name',
                'finish_transaction_tests.test_id',
                'finish_transaction_tests.test_name',
                'finish_transactions.checkin_by as checkin_by_id',
                'users.name as checkin_by_name',
                'finish_transaction_tests.draw_by as draw_by_id',
                'finish_transaction_tests.draw_by_name',
                'finish_transaction_tests.verify_by as verify_by_id',
                'finish_transaction_tests.verify_by_name',
                'finish_transaction_tests.validate_by as validate_by_id',
                'finish_transaction_tests.validate_by_name'
            )
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->leftJoin('users', 'finish_transactions.checkin_by', '=', 'users.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transaction_tests.package_id')
            ->groupBy('finish_transactions.transaction_id');
        $processData = $query_process->get();

        // echo '<pre>';
        // print_r($processData);
        // die;

        foreach($userData as $key => $value){
            
            if(!isset($userData[$key]->checkin)){
                $userData[$key]->checkin = 0;
            }
            if(!isset($userData[$key]->draw)){
                $userData[$key]->draw = 0;
            }
            if(!isset($userData[$key]->process)){
                $userData[$key]->process = 0;
            }
            if(!isset($userData[$key]->verify)){
                $userData[$key]->verify = 0;
            }
            if(!isset($userData[$key]->validate)){
                $userData[$key]->validate = 0;
            }

            foreach($processData as $process_data){
                if($value->user_id == $process_data->checkin_by_id){
                    $userData[$key]->checkin++;
                }
                if($value->user_id == $process_data->draw_by_id){
                    $userData[$key]->draw++;
                }
                if($value->user_id == $process_data->draw_by_id){
                    $userData[$key]->process++;
                }
                if($value->user_id == $process_data->verify_by_id){
                    $userData[$key]->verify++;
                }
                if($value->user_id == $process_data->validate_by_id){
                    $userData[$key]->validate++;
                }
            }
            
        }

        // echo '<pre>';
        // print_r($userData);

        $data['userData'] = $userData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_user.print', $data);
    }

    //===============================================================================================
    // USER PROCESS REPORT
    //===============================================================================================
    public function userProcessReport()
    {
        $data['title'] = 'User Process Report';
        return view('dashboard.report.report_user_process.index', $data); 
    }

    public function userProcessDatatable($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')
            ->select(
                'finish_transactions.transaction_id',
                'finish_transactions.created_time',
                'finish_transactions.no_lab',
                'finish_transaction_tests.type',
                'finish_transaction_tests.package_id',
                'finish_transaction_tests.package_name',
                'finish_transaction_tests.test_id',
                'finish_transaction_tests.test_name',
                'users.name as checkin_by_name',
                'finish_transaction_tests.draw_by_name',
                'finish_transaction_tests.verify_by_name',
                'finish_transaction_tests.validate_by_name'
            )
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->leftJoin('users', 'finish_transactions.checkin_by', '=', 'users.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transaction_tests.package_id')
            ->groupBy('finish_transactions.transaction_id');
        $processData = $query->get();

        return DataTables::of($processData)
            ->addIndexColumn()
            ->escapeColumns([])
            ->make(true);
    }

    public function userProcessPrint($startDate = null, $endDate = null, $test_id = null)
    {
        // if the startDate and the endDate not set, the query will be only for today's transactions
        if ($startDate == null && $endDate == null) {
            $from = Carbon::today()->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::today()->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        } else {
            $from = Carbon::parse($startDate)->addHours(0)->addMinutes(0)->addSeconds(0)->toDateTimeString();
            $to = Carbon::parse($endDate)->addHours(23)->addMinutes(59)->addSeconds(59)->toDateTimeString();
        }

        $query = DB::table('finish_transaction_tests')
            ->select(
                'finish_transactions.transaction_id',
                'finish_transactions.created_time',
                'finish_transactions.no_lab',
                'finish_transaction_tests.type',
                'finish_transaction_tests.package_id',
                'finish_transaction_tests.package_name',
                'finish_transaction_tests.test_id',
                'finish_transaction_tests.test_name',
                'users.name as checkin_by_name',
                'finish_transaction_tests.draw_by_name',
                'finish_transaction_tests.verify_by_name',
                'finish_transaction_tests.validate_by_name'
            )
            ->leftJoin('finish_transactions', 'finish_transaction_tests.transaction_id', '=', 'finish_transactions.transaction_id')
            ->leftJoin('users', 'finish_transactions.checkin_by', '=', 'users.id')
            ->whereRaw("date(finish_transactions.created_time) between '" . $from . "' and '" . $to . "'")
            ->orderBy('finish_transactions.created_time', 'desc')
            ->groupBy('finish_transaction_tests.package_id')
            ->groupBy('finish_transactions.transaction_id');
        $processData = $query->get();

        $data['processData'] = $processData;

        if ($startDate != null && $endDate != null) {
            $data["startDate"] = date('d/m/Y', strtotime($startDate));
            $data["endDate"] = date('d/m/Y', strtotime($endDate));
        } else {
            $data["startDate"] = '-';
            $data["endDate"] = '-';
        }

        return view('dashboard.report.report_user_process.print', $data);
    }
}
