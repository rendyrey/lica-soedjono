<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;
use App\Exports\QcDataExport;
use Maatwebsite\Excel\Facades\Excel;

class QcController extends Controller
{
    public function index()
    {
        $data['title'] = 'Quality Control';

        $query = DB::table('analyzers')->select('id as analyzer_id', 'name as analyzer');
        $data['analyzer_data'] =  $query->get();

        return view('dashboard.qc.index', $data);
    }

    public function getTest($analyzer_id)
    {
        $query = DB::table('interfacings')
            ->select('tests.id as test_id', 'tests.name as test_name')
            ->leftJoin('tests', 'interfacings.test_id', '=', 'tests.id')
            ->where('interfacings.analyzer_id', $analyzer_id)
            ->orderBy('tests.id', 'asc');
        $test_data = $query->get();

        return response()->json($test_data);
    }

    public function getQcId($month, $year, $analyzer_id, $test_id)
    {
        // level 1
        $qc_query1 = DB::table('qcs')
            ->select('qcs.id as qc_id')
            ->leftJoin('qc_references', 'qcs.id', '=', 'qc_references.qc_id')
            ->where('month', $month)
            ->where('year', $year)
            ->where('analyzer_id', $analyzer_id)
            ->where('test_id', $test_id)
            ->where('qc_references.level', 1);
        $qc_data1 = $qc_query1->first();

        // level 2
        $qc_query2 = DB::table('qcs')
            ->select('qcs.id as qc_id')
            ->leftJoin('qc_references', 'qcs.id', '=', 'qc_references.qc_id')
            ->where('month', $month)
            ->where('year', $year)
            ->where('analyzer_id', $analyzer_id)
            ->where('test_id', $test_id)
            ->where('qc_references.level', 2);
        $qc_data2 = $qc_query2->first();

        // level 3
        $qc_query3 = DB::table('qcs')
            ->select('qcs.id as qc_id')
            ->leftJoin('qc_references', 'qcs.id', '=', 'qc_references.qc_id')
            ->where('month', $month)
            ->where('year', $year)
            ->where('analyzer_id', $analyzer_id)
            ->where('test_id', $test_id)
            ->where('qc_references.level', 3);
        $qc_data3 = $qc_query3->first();

        // return response()->json($qc_data1);
        return response()->json(['qc_data1' => $qc_data1, 'qc_data2' => $qc_data2, 'qc_data3' => $qc_data3]);
    }

    // get reference level 1 
    public function getReferenceData1($qc_id)
    {
        $qc_query = DB::table('qc_references')
            ->select('qc_references.*', 'qcs.id as qc_id', 'qcs.month', 'qcs.year', 'tests.name as test_name')
            ->leftJoin('qcs', 'qc_references.qc_id', '=', 'qcs.id')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qc_id', $qc_id)
            ->where('level', 1);
        $qc_data = $qc_query->first();

        return response()->json($qc_data);
    }

    // get reference level 2
    public function getReferenceData2($qc_id)
    {
        $qc_query = DB::table('qc_references')
            ->select('qc_references.*', 'qcs.id as qc_id', 'qcs.month', 'qcs.year', 'tests.name as test_name')
            ->leftJoin('qcs', 'qc_references.qc_id', '=', 'qcs.id')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qc_id', $qc_id)
            ->where('level', 2);
        $qc_data = $qc_query->first();

        return response()->json($qc_data);
    }

    // get reference level 3
    public function getReferenceData3($qc_id)
    {
        $qc_query = DB::table('qc_references')
            ->select('qc_references.*', 'qcs.id as qc_id', 'qcs.month', 'qcs.year', 'tests.name as test_name')
            ->leftJoin('qcs', 'qc_references.qc_id', '=', 'qcs.id')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qc_id', $qc_id)
            ->where('level', 3);
        $qc_data = $qc_query->first();

        return response()->json($qc_data);
    }

    // add reference level 1
    public function addReference1(Request $request)
    {
        $qc_id = DB::table('qcs')
            ->insertGetId([
                'analyzer_id' => $request->analyzer,
                'test_id' => $request->test,
                'month' => $request->month,
                'year' => $request->year,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);

        $qc_reference_insert = [
            'qc_id' => $qc_id,
            'no_lot' => $request->no_lot,
            'standard_deviation' => $request->standard_deviation,
            'control_name' => $request->control_name,
            'level' => 1,
            'low_value' => $request->low_value,
            'high_value' => $request->high_value,
            'target_value' => $request->target_value,
            'deviation' => $request->deviation,
            'created_at' => Carbon::now(),
            'updated_at' => null
        ];

        DB::table('qc_references')->insert($qc_reference_insert);
        DB::commit();
        return response()->json(['message' => 'Create success!']);
    }

    // add reference level 2
    public function addReference2(Request $request)
    {
        $qc_id = DB::table('qcs')
            ->insertGetId([
                'analyzer_id' => $request->analyzer,
                'test_id' => $request->test,
                'month' => $request->month,
                'year' => $request->year,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);

        $qc_reference_insert = [
            'qc_id' => $qc_id,
            'no_lot' => $request->no_lot,
            'standard_deviation' => $request->standard_deviation,
            'control_name' => $request->control_name,
            'level' => 2,
            'low_value' => $request->low_value,
            'high_value' => $request->high_value,
            'target_value' => $request->target_value,
            'deviation' => $request->deviation,
            'created_at' => Carbon::now(),
            'updated_at' => null
        ];

        DB::table('qc_references')->insert($qc_reference_insert);
        DB::commit();
        return response()->json(['message' => 'Create success!']);
    }

    // add reference level 3
    public function addReference3(Request $request)
    {
        $qc_id = DB::table('qcs')
            ->insertGetId([
                'analyzer_id' => $request->analyzer,
                'test_id' => $request->test,
                'month' => $request->month,
                'year' => $request->year,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ]);

        $qc_reference_insert = [
            'qc_id' => $qc_id,
            'no_lot' => $request->no_lot,
            'standard_deviation' => $request->standard_deviation,
            'control_name' => $request->control_name,
            'level' => 3,
            'low_value' => $request->low_value,
            'high_value' => $request->high_value,
            'target_value' => $request->target_value,
            'deviation' => $request->deviation,
            'created_at' => Carbon::now(),
            'updated_at' => null
        ];

        DB::table('qc_references')->insert($qc_reference_insert);
        DB::commit();
        return response()->json(['message' => 'Create success!']);
    }

    // ============================
    // datatable level 1, 2, 3
    // ============================
    // datatable QC Data
    public function datatableQcData($qc_id, $startDate = null, $endDate = null)
    {
        if ($startDate == null && $endDate == null) {
            $query = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->orderBy('date', 'asc');
            $qc_data = $query->get();
        } else {
            $query = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                ->orderBy('date', 'asc');
            $qc_data = $query->get();
        }

        return response()->json($qc_data);
    }
    // ============================
    // end datatable level 1, 2, 3
    // ============================

    // ============================
    // check position level 1, 2, 3
    // ============================
    public function checkPositionQCData1($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 1);
        $reference_data = $reference_query->first();
        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }

    public function checkPositionQCDataEdit1($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 1);
        $reference_data = $reference_query->first();

        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }

    public function checkPositionQCData2($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 2);
        $reference_data = $reference_query->first();

        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }

    public function checkPositionQCDataEdit2($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 2);
        $reference_data = $reference_query->first();

        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }

    public function checkPositionQCData3($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 3);
        $reference_data = $reference_query->first();

        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }

    public function checkPositionQCDataEdit3($qc_id, $qc_value)
    {

        // Statistics step for Quality Control :
        // 1. Find Mean (Average) value
        // 2. Standard Deviation (SD) value

        $reference_query = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id)
            ->where('level', 3);
        $reference_data = $reference_query->first();

        $low_value = $reference_data->low_value;
        $high_value = $reference_data->high_value;
        $target_value = $reference_data->target_value;
        $sd = $reference_data->deviation;

        $amount_query = DB::table('qc_datas')
            ->select(DB::raw('COUNT(qc_datas.id) as amount'))
            ->where('qc_id', $qc_id);
        $data = $amount_query->first();
        $amountData = $data->amount;

        $record_query = DB::table('qc_datas')
            ->select('qc_datas.data as qc_value')
            ->where('qc_id', $qc_id);
        $recordData = $record_query->get();

        $position = ($qc_value - $target_value) / $sd;
        $position_value = $position;

        return response()->json(round($position_value, 1));
    }
    // ================================
    // end check position level 1, 2, 3
    // ================================

    // ================
    // QC graph 1, 2, 3
    // ================

    public function loadGraphData($qc_id1, $qc_id2, $qc_id3, $startDate = null, $endDate = null)
    {
        $query_qc1 = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qcs.id', $qc_id1);
        $qc1 = $query_qc1->first();

        $query_reference_data1 = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id1);
        $reference_data1 = $query_reference_data1->first();

        // ===================
        // LEVEL 1
        // ===================
        if ($qc_id1 != 0) {

            if ($startDate == null && $endDate == null) {
                $query_qc_data1 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id1)
                    ->orderBy('date', 'asc');
                $qc_data1 = $query_qc_data1->get();
            } else {
                $query_qc_data1 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id1)
                    ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                    ->orderBy('date', 'asc');
                $qc_data1 = $query_qc_data1->get();
            }

            // echo '<pre>';
            // print_r($reference_data1);
            // echo '<hr>';
            // echo '<pre>';
            // print_r($qc_data1);
        } else {
            $qc_data1 = [];
        }

        // ===================
        // LEVEL 2
        // ===================
        if ($qc_id2 != 0) {
            if ($startDate == null && $endDate == null) {
                $query_qc_data2 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id2)
                    ->orderBy('date', 'asc');
                $qc_data2 = $query_qc_data2->get();
            } else {
                $query_qc_data2 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id2)
                    ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                    ->orderBy('date', 'asc');
                $qc_data2 = $query_qc_data2->get();
            }

            // echo '<pre>';
            // print_r($reference_data2);
            // echo '<hr>';
            // echo '<pre>';
            // print_r($qc_data2);
        } else {
            $qc_data2 = [];
        }

        // ===================
        // LEVEL 3
        // ===================
        if ($qc_id3 != 0) {
            if ($startDate == null && $endDate == null) {
                $query_qc_data3 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id3)
                    ->orderBy('date', 'asc');
                $qc_data3 = $query_qc_data3->get();
            } else {
                $query_qc_data3 = DB::table('qc_datas')
                    ->select('qc_datas.*')
                    ->where('qc_id', $qc_id3)
                    ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                    ->orderBy('date', 'asc');
                $qc_data3 = $query_qc_data3->get();
            }

            // echo '<pre>';
            // print_r($reference_data3);
            // echo '<hr>';
            // echo '<pre>';
            // print_r($qc_data3);
        } else {
            $qc_data3 = [];
        }

        return response()->json(['qc1' => $qc1, 'reference_data1' => $reference_data1, 'qc_data1' => $qc_data1, 'qc_data2' => $qc_data2, 'qc_data3' => $qc_data3]);
    }

    public function loadGraphData1($qc_id, $startDate = null, $endDate = null)
    {

        $query_qc = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qcs.id', $qc_id);
        $qc = $query_qc->first();

        $query_reference_data = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id);
        $reference_data = $query_reference_data->first();

        if ($startDate == null && $endDate == null) {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->orderBy('date', 'asc');
            $qc_data = $query_qc_data->get();
        } else {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                ->orderBy('date', 'asc');
            $qc_data = $query_qc_data->get();
        }

        // echo '<pre>';
        // print_r($reference_data);
        // echo '<hr>';
        // echo '<pre>';
        // print_r($qc_data);

        return response()->json(['qc' => $qc, 'reference_data' => $reference_data, 'qc_data' => $qc_data]);
    }

    public function loadGraphData2($qc_id)
    {

        $query_qc = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->where('qcs.id', $qc_id);
        $qc = $query_qc->first();

        $query_reference_data = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id);
        $reference_data = $query_reference_data->first();

        $query_qc_data = DB::table('qc_datas')
            ->select('qc_datas.*')
            ->where('qc_id', $qc_id)
            ->orderBy('date', 'asc');
        $qc_data = $query_qc_data->get();

        // echo '<pre>';
        // print_r($qc);
        // echo '<hr>';
        // echo '<pre>';
        // print_r($reference_data);
        // echo '<hr>';
        // echo '<pre>';
        // print_r($qc_data);

        return response()->json(['qc' => $qc, 'reference_data' => $reference_data, 'qc_data' => $qc_data]);
    }
    // ====================
    // End QC graph 1, 2, 3
    // ====================


    // ==========================
    // Create QC Data 1, 2, 3
    // ==========================
    // create qc data level 1
    public function createQcDataLevel1(Request $request)
    {
        DB::beginTransaction(); // begin of transaction
        try {

            $date = date('Y-m-d', strtotime($request->date));
            $qc_id = $request->qc_id1;

            $data[] = [
                'qc_id' => $qc_id,
                'date' => $date,
                'data' => $request->qc_data,
                'position' => $request->position,
                'qc' => $request->qc,
                'atlm' => $request->atlm,
                'recommendation' => $request->recommendation,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ];

            \App\QcData::insert($data); // insert all test data in one query

            $this->logActivity(
                "Create QC data level 1",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit(); // commit into DB if successfully created the data into masters.
            return response()->json(['message' => ucwords('QC Data Level 1') . ' added successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            DB::rollback(); // rollback the database if in the middle way of creation there is any error.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // create qc data level 2
    public function createQcDataLevel2(Request $request)
    {
        DB::beginTransaction(); // begin of transaction
        try {

            $date = date('Y-m-d', strtotime($request->date_2));
            $qc_id = $request->qc_id2_2;

            $data[] = [
                'qc_id' => $qc_id,
                'date' => $date,
                'data' => $request->qc_data_2,
                'position' => $request->position_2,
                'qc' => $request->qc_2,
                'atlm' => $request->atlm_2,
                'recommendation' => $request->recommendation_2,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ];

            \App\QcData::insert($data); // insert all test data in one query

            $this->logActivity(
                "Create QC data level 2",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit(); // commit into DB if successfully created the data into masters.
            return response()->json(['message' => ucwords('QC Data Level 2') . ' added successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            DB::rollback(); // rollback the database if in the middle way of creation there is any error.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // create qc data level 3
    public function createQcDataLevel3(Request $request)
    {
        DB::beginTransaction(); // begin of transaction
        try {

            $date = date('Y-m-d', strtotime($request->date_3));
            $qc_id = $request->qc_id3_3;

            $data[] = [
                'qc_id' => $qc_id,
                'date' => $date,
                'data' => $request->qc_data_3,
                'position' => $request->position_3,
                'qc' => $request->qc_3,
                'atlm' => $request->atlm_3,
                'recommendation' => $request->recommendation_3,
                'created_at' => Carbon::now(),
                'updated_at' => null
            ];

            \App\QcData::insert($data); // insert all test data in one query

            $this->logActivity(
                "Create QC data level 3",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit(); // commit into DB if successfully created the data into masters.
            return response()->json(['message' => ucwords('QC Data Level 3') . ' added successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            DB::rollback(); // rollback the database if in the middle way of creation there is any error.
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    // ==========================
    // End Create QC Data 1, 2, 3
    // ==========================

    // ==========================
    // Edit QC Data 1, 2, 3
    // ==========================
    // edit qc data level 1
    public function editQcDataLevel1($qc_id)
    {
        $query = DB::table('qc_datas')
            ->select('qc_datas.*')
            ->where('id', $qc_id);
        $qc_data = $query->first();

        return response()->json($qc_data);
    }

    // edit qc data level 2
    public function editQcDataLevel2($qc_id)
    {
        $query = DB::table('qc_datas')
            ->select('qc_datas.*')
            ->where('id', $qc_id);
        $qc_data = $query->first();

        return response()->json($qc_data);
    }

    // edit qc data level 3
    public function editQcDataLevel3($qc_id)
    {
        $query = DB::table('qc_datas')
            ->select('qc_datas.*')
            ->where('id', $qc_id);
        $qc_data = $query->first();

        return response()->json($qc_data);
    }
    // ==========================
    // End Edit QC Data 1, 2, 3
    // ==========================


    // ==========================
    // Update QC Data 1, 2, 3
    // ==========================
    // update qc data level 1
    public function updateQcDataLevel1(Request $request)
    {

        DB::beginTransaction(); // begin of transaction database
        try {

            $date = date('Y-m-d', strtotime($request->date));

            $updateData = [
                'date' => $date,
                'data' => $request->qc_data,
                'position' => $request->position,
                'qc' => $request->qc,
                'atlm' => $request->atlm,
                'recommendation' => $request->recommendation,
                'updated_at' => Carbon::now()
            ];

            DB::table('qc_datas')
                ->where('id', $request->id)
                ->update($updateData);

            $this->logActivity(
                "Update QC data level 1 with ID $request->id",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", 'QC data')) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // update qc data level 2
    public function updateQcDataLevel2(Request $request)
    {

        DB::beginTransaction(); // begin of transaction database
        try {

            $date = date('Y-m-d', strtotime($request->date));

            $updateData = [
                'date' => $date,
                'data' => $request->qc_data,
                'position' => $request->position,
                'qc' => $request->qc,
                'atlm' => $request->atlm,
                'recommendation' => $request->recommendation,
                'updated_at' => Carbon::now()
            ];

            DB::table('qc_datas')
                ->where('id', $request->id)
                ->update($updateData);

            $this->logActivity(
                "Update QC data level 2 with ID $request->id",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", 'QC data')) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    // update qc data level 3
    public function updateQcDataLevel3(Request $request)
    {

        DB::beginTransaction(); // begin of transaction database
        try {

            $date = date('Y-m-d', strtotime($request->date));

            $updateData = [
                'date' => $date,
                'data' => $request->qc_data,
                'position' => $request->position,
                'qc' => $request->qc,
                'atlm' => $request->atlm,
                'recommendation' => $request->recommendation,
                'updated_at' => Carbon::now()
            ];

            DB::table('qc_datas')
                ->where('id', $request->id)
                ->update($updateData);

            $this->logActivity(
                "Update QC data level 3 with ID $request->id",
                json_encode($request->except(['_method', '_token']))
            );

            DB::commit();
            return response()->json(['message' => ucwords(str_replace("_", " ", 'QC data')) . ' updated successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    // ==========================
    // End Update QC Data 1, 2, 3
    // ==========================

    // ==========================
    // Delete QC Data 1, 2, 3
    // ==========================
    public function deleteQcDataLevel1($id, $qc_id)
    {
        try {
            $data = \App\QcData::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete QC data level 1 with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords('QC data') . ' deleted successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteQcDataLevel2($id, $qc_id)
    {
        try {
            $data = \App\QcData::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete QC data level 2 with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords('QC data') . ' deleted successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function deleteQcDataLevel3($id, $qc_id)
    {
        try {
            $data = \App\QcData::findOrFail($id);
            $data->delete();

            $this->logActivity(
                "Delete QC data level 3 with ID $id",
                json_encode($data)
            );

            return response()->json(['message' => ucwords('QC data') . ' deleted successfully', 'qc_id' => $qc_id]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    // ==========================
    // Print QC Data 1, 2, 3
    // ==========================

    // ==========================
    // End Print QC Data 1, 2, 3
    // ==========================
    public function printQcData1($qc_id, $startDate = null, $endDate = null)
    {

        $query_qc = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name', 'analyzers.name as analyzer_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->leftJoin('analyzers', 'qcs.analyzer_id', '=', 'analyzers.id')
            ->where('qcs.id', $qc_id);

        $query_reference = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id);

        if ($startDate == null && $endDate == null) {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->orderBy('date', 'asc');
        } else {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                ->orderBy('date', 'asc');
        }
        echo '<pre>';
        print_r($query_qc);
        echo '<pre>';
        print_r($query_reference);
        echo '<pre>';
        print_r($query_qc_data);

        // $data['qc'] = $query_qc->first();
        // $data['qc_reference'] = $query_reference->first();
        // $data['qc_data'] = $query_qc_data->get();
        // $data['startDate'] = $startDate;
        // $data['endDate'] = $endDate;
        // return view('dashboard.report.report_qc.print-level-1', $data);
    }

    public function printQcData2($qc_id, $startDate = null, $endDate = null)
    {

        $query_qc = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name', 'analyzers.name as analyzer_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->leftJoin('analyzers', 'qcs.analyzer_id', '=', 'analyzers.id')
            ->where('qcs.id', $qc_id);

        $query_reference = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id);

        if ($startDate == null && $endDate == null) {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->orderBy('date', 'asc');
        } else {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                ->orderBy('date', 'asc');
        }

        $data['qc'] = $query_qc->first();
        $data['qc_reference'] = $query_reference->first();
        $data['qc_data'] = $query_qc_data->get();
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        return view('dashboard.report.report_qc.print-level-2', $data);
    }

    public function printQcData3($qc_id, $startDate = null, $endDate = null)
    {

        $query_qc = DB::table('qcs')
            ->select('qcs.*', 'tests.name as test_name', 'analyzers.name as analyzer_name')
            ->leftJoin('tests', 'qcs.test_id', '=', 'tests.id')
            ->leftJoin('analyzers', 'qcs.analyzer_id', '=', 'analyzers.id')
            ->where('qcs.id', $qc_id);

        $query_reference = DB::table('qc_references')
            ->select('qc_references.*')
            ->where('qc_id', $qc_id);

        if ($startDate == null && $endDate == null) {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->orderBy('date', 'asc');
        } else {
            $query_qc_data = DB::table('qc_datas')
                ->select('qc_datas.*')
                ->where('qc_id', $qc_id)
                ->whereRaw("date(date) between '" . $startDate . "' and '" . $endDate . "'")
                ->orderBy('date', 'asc');
        }

        $data['qc'] = $query_qc->first();
        $data['qc_reference'] = $query_reference->first();
        $data['qc_data'] = $query_qc_data->get();
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        return view('dashboard.report.report_qc.print-level-3', $data);
    }

    public function exportQcData()
    {
        return Excel::download(new QcDataExport, 'Data QC.xlsx');
    }
}
