<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use DB;
use Illuminate\Support\Carbon;
use Auth;

class ManipulationController extends Controller
{

    public function index()
    {
        // DATA PASIEN

        $query = DB::table('m_pasien')->where('mrpasien', '!=', '');
        $data_pasien = $query->get();

        // echo '<pre>';
        // print_r($data_pasien);
        // die;

        foreach ($data_pasien as $data) {

            if ($data->alamat != '') {
                $address = $data->alamat;
            } else {
                $address = '-';
            }

            if ($data->jkelm == 'Perempuan') {
                $jenis_kelamin = 'F';
            } else {
                $jenis_kelamin = 'M';
            }

            DB::table('patients')
                ->insert([
                    'medrec' => $data->mrpasien,
                    'name' => $data->nmpasien,
                    'gender' => $jenis_kelamin,
                    'birthdate' => $data->tgllahir,
                    'address' => $address,
                    'phone' => null,
                    'email' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
    }

    // ===================
    // MASTER MANIPULATE
    // ===================

    public function roomData()
    {
        $query = DB::table('m_room');
        $data_room = $query->get();

        // echo '<pre>';
        // print_r($data_room);
        // die;

        foreach ($data_room as $ruangan) {

            $nmroom = $ruangan->asal . ' ' . $ruangan->nmroom;
            $kelas = $ruangan->kelas;
            $auto_cekin = $ruangan->auto_cekin;
            $auto_draw = $ruangan->auto_draw;

            if ($ruangan->type == 1 || $ruangan->type == 4) {
                $type = 'rawat_jalan';
            } else if ($ruangan->type == 2) {
                $type = 'rawat_inap';
            } else if ($ruangan->type == 3) {
                $type = 'igd';
            }

            DB::table('rooms')
                ->insert([
                    'room' => $nmroom,
                    'room_code' => 0,
                    'class' => $kelas,
                    'auto_checkin' => $auto_cekin,
                    'auto_draw' => $auto_draw,
                    'auto_undraw' => 0,
                    'auto_nolab' => 0,
                    'type' => $type,
                    'referral_address' => null,
                    'referral_no_phone' => null,
                    'referral_email' => null,
                    'general_code' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
    }

    public function doctorData()
    {
        $query = DB::table('m_dokter');
        $data_doctor = $query->get();

        // echo '<pre>';
        // print_r($data_doctor);
        // die;
        foreach ($data_doctor as $dokter) {

            $title = trim($dokter->title);
            $nmdokter = trim($dokter->nmdokter);
            $spesialis = '';
            if ($dokter->spesialis != '') {
                $spesialis = trim($dokter->spesialis);
            }

            $nama_dokter = $title . ' ' . $nmdokter . ' ' . $spesialis;

            DB::table('doctors')
                ->insert([
                    'name' => $nama_dokter,
                    'general_code' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
    }

    public function insuranceData()
    {
        $query = DB::table('m_penjamin');
        $data_penjamin = $query->get();

        // echo '<pre>';
        // print_r($data_room);

        foreach ($data_penjamin as $penjamin) {

            $penjamin = trim($penjamin->penjamin);

            DB::table('insurances')
                ->insert([
                    'name' => $penjamin,
                    'discount' => 0,
                    'general_code' => null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
        }
    }

    public function patientData()
    {
        // DATA TRANSAKSI
        $startDate = '2023-04-01';
        $endDate = '2023-04-30';

        $query = DB::table('t_tran')
                    ->select('t_tran.*')
                    ->whereRaw("date(tgltran) between '" . $startDate . "' and '" . $endDate . "'")
                    ->orderBy('tgltran', 'asc');
        $data_transaksi = $query->get();

        // echo '<pre>';
        // print_r($data_transaksi);
        // die;

        $index = 0;
        foreach ($data_transaksi as $transaksi) {

            $mrpasien = $transaksi->mrpasien;
            $patient_medrec = trim($mrpasien);
            $nmpasien = $transaksi->nmpasien;
            $patient_name = trim($nmpasien);
            $alamat = $transaksi->alamat;
            $patient_address = trim($alamat);

            // PASIEN DATA
            $query_patient = DB::table('patients')->where('medrec', $patient_medrec);
            $data_pasien = $query_patient->first();

            if (empty($data_pasien)) {
                if ($patient_address != '') {
                    $address = $patient_address;
                } else {
                    $address = '-';
                }

                if ($transaksi->jkelm == 'Perempuan') {
                    $jenis_kelamin = 'F';
                } else {
                    $jenis_kelamin = 'M';
                }

                DB::table('patients')
                    ->insert([
                        'medrec' => $patient_medrec,
                        'name' => $patient_name,
                        'gender' => $jenis_kelamin,
                        'birthdate' => $transaksi->tgllahir,
                        'address' => $address,
                        'phone' => null,
                        'email' => null,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
            }
            $index++;
        }
        echo 'Transaksi Ke - ' . $index . ' = DONE' . '<hr>';
    }

    public function priceData()
    {
        $query = DB::table('m_test');
        $tes_tiyos = $query->get();

        foreach ($tes_tiyos as $tes_tiyo) {

            $query_tes = DB::table('tests')->where('tiyo_id', $tes_tiyo->kdtest);
            $test_data = $query_tes->first();

            for ($i = 0; $i < 3; $i++) {

                if ($i == 0) {
                    $class = 1;
                    $price = $tes_tiyo->harga1;
                } else if ($i == 1) {
                    $class = 2;
                    $price = $tes_tiyo->harga2;
                } else {
                    $class = 3;
                    $price = $tes_tiyo->harga3;
                }

                DB::table('prices')
                    ->insert([
                        'package_id' => null,
                        'test_id' => $test_data->id,
                        'type' => 'test',
                        'price' => $price,
                        'class' => $class,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
            }
        }
    }

    public function deleteMasterTest()
    {
        // delete all test data in price table where test name ==> package name (packages table)

        $query_test = DB::table('tests')->get();
        $masterTest = $query_test;

        $index = 0;
        foreach ($masterTest as $testData) {

            $query_package = DB::table('packages')->where('name', $testData->name)->first();
            $package = $query_package;

            if ($package) {

                $query_price = DB::table('prices')->where('test_id', $testData->id)->first();
                $price = $query_price;

                echo $price->test_id . '<br>';
            }
        }
    }

    // ======================
    // TRANSACTION MANIPULATE
    // ======================

    public function transactionData()
    {
        // DATA TRANSAKSI
        $startDate = '2023-04-01';
        $endDate = '2023-04-30';

        $query = DB::table('t_tran')->where('mrpasien', '!=', '');
        $query->whereRaw("date(tgltran) between '" . $startDate . "' and '" . $endDate . "'");
        $query->orderBy('tgltran', 'asc');
        $data_transaksi = $query->get();

        // echo count($data_transaksi);
        // die;

        // echo '<pre>';
        // print_r($data_transaksi);
        // die;

        $ada = 0;
        $tidak_ada = 0;
        $index = 0;
        foreach ($data_transaksi as $transaksi) {

            $mrpasien = $transaksi->mrpasien;
            $patient_medrec = trim($mrpasien);

            // PASIEN DATA
            $query_patient = DB::table('patients')->where('medrec', $patient_medrec);
            $data_pasien = $query_patient->first();

            if ($data_pasien) {
                // $ada++;
                $patient_unique = $data_pasien->id;
            } else {
                // $tidak_ada++;
            }

            // ROOM DATA
            $query_room = DB::table('rooms')->where('room', $transaksi->room);
            $data_ruangan = $query_room->first();

            if ($data_ruangan) {
                $ada++;
                $room = $data_ruangan->id;
                $transaction_type = $data_ruangan->type;
            } else {
                $tidak_ada++;
                // room_id 52 --> 404error (general code)
                $room = 52;
                $transaction_type = 'rawat_inap';
            }

            // DOCTOR DATA
            $query_doctor = DB::table('doctors')->where('name', $transaksi->nmdokter);
            $data_dokter = $query_doctor->first();

            if ($data_dokter) {
                // $ada++;
                $doctor = $data_dokter->id;
            } else {
                // $tidak_ada++;
                // doctor_id 32 --> 404error (general code)
                $doctor = 32;
            }

            // INSURANCE DATA
            $query_insurance = DB::table('insurances')->where('name', $transaksi->penjamin);
            $data_asurance = $query_insurance->first();

            if ($data_asurance) {
                $insurance = $data_asurance->id;
            } else {
                // insurance_id 6 --> 404error (general code)
                $insurance = 6;
            }

            $patient_id = $patient_unique;
            $room_id = $room;
            $doctor_id = $doctor;
            $insurance_id = $insurance;
            $analyzer_id = null;
            $type = $transaction_type;
            $transaction_id_label = $transaksi->nokuit;
            $no_lab = $transaksi->nolab;
            $no_order = $transaksi->nokuit;
            $note = null;
            $status = 2;    // post analytic
            $is_igd = null;
            $is_critical = 0;
            $cito = $transaksi->cito;
            $check = 1;
            $draw = 1;
            $result_status = null;
            $verify_status = 1;
            $validate_status = 1;
            $report_status = null;
            $created_time = $transaksi->jamtran;
            $checkin_time = $transaksi->jamtran;
            $analytic_time = $transaksi->jamtran;
            $post_time_temp = $transaksi->jamtran;
            $post_time = date("Y-m-d H:i:s", strtotime('+2 hours', strtotime($post_time_temp)));
            $memo_result = null;
            $is_print_memo = null;
            $print = $transaksi->status_print;
            $get_status = null;
            $checkin_by = null;
            $verficator_id = null;
            $validator_id = null;
            $shipper = null;
            $receiver = null;
            $created_at = Carbon::now();
            $updated_at = Carbon::now();

            DB::table('transactions')
                ->insert([
                    'patient_id' => $patient_id,
                    'room_id' => $room_id,
                    'doctor_id' => $doctor_id,
                    'insurance_id' => $insurance_id,
                    'analyzer_id' => $analyzer_id,
                    'type' => $type,
                    'transaction_id_label' => $transaction_id_label,
                    'no_lab' => $no_lab,
                    'note' => $note,
                    'status' => $status,
                    'is_igd' => $is_igd,
                    'is_critical' => $is_critical,
                    'cito' => $cito,
                    'check' => $check,
                    'draw' => $draw,
                    'result_status' => $result_status,
                    'verify_status' => $verify_status,
                    'validate_status' => $validate_status,
                    'report_status' => $report_status,
                    'created_time' => $created_time,
                    'checkin_time' => $checkin_time,
                    'analytic_time' => $analytic_time,
                    'post_time' => $post_time,
                    'memo_result' => $memo_result,
                    'is_print_memo' => $is_print_memo,
                    'print' => $print,
                    'get_status' => $get_status,
                    'checkin_by' => $checkin_by,
                    'verficator_id' => $verficator_id,
                    'validator_id' => $validator_id,
                    'shipper' => $shipper,
                    'receiver' => $receiver,
                    'no_order' => $no_order,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ]);

            $index++;
        }
        echo 'Transaksi ke - ' . $index . ' = DONE' . '<hr>';
        // echo "Ada = " . $ada . '<br>';
        // echo "Tidak Ada = " . $tidak_ada . '<br>';
    }

    public function transactionTestData()
    {
        // DATA TRANSAKSI TES
        $startDate = '2023-04-21';
        $endDate = '2023-04-30';

        $query = DB::table('t_pas')->where('nolab', '!=', '');
        $query->whereRaw("date(tgltran) between '" . $startDate . "' and '" . $endDate . "'");
        $query->orderBy('tgltran', 'asc');
        $data_transaksi_tes = $query->get();

        $ada = 0;
        $tidak_ada = 0;
        $index = 0;
        foreach ($data_transaksi_tes as $transaksi_tes) {

            // DATA TRANSAKSI
            $query_transaksi = DB::table('transactions')->where('no_lab', $transaksi_tes->nolab);
            $data_transaksi = $query_transaksi->first();

            if ($data_transaksi) {
                $transaction_unique = $data_transaksi->id;
                $checkin_time = $data_transaksi->checkin_time;
                $created_time = $data_transaksi->created_time;
                $post_time = $data_transaksi->post_time;
            } else {
                $transaction_unique = null;
                $checkin_time = null;
                $created_time = null;
                $post_time = null;
            }

            // DATA TEST
            $kdtest = $transaksi_tes->kdtest;
            $kdtest = ltrim($kdtest, '0');

            $query_tes = DB::table('tests')->where('id', $kdtest);
            $data_tes = $query_tes->first();

            if ($data_tes) {
                $test_unique = $data_tes->id;
                if ($data_tes->range_type == 'number') {
                    $range_type = 'number';
                } else if ($data_tes->range_type == 'label') {
                    $range_type = 'label';
                } else if ($data_tes->range_type == 'description') {
                    $range_type = 'description';
                } else if ($data_tes->range_type == 'free_formatted_text') {
                    $range_type = 'free_formatted_text';
                } else{
                    $range_type = 'error';
                }

                if ($data_tes->general_code != null) {
                    $general_code = $data_tes->general_code;
                } else {
                    $general_code = null;
                }
            } else {
                $test_unique = null;
                $general_code = null;
                $range_type = "error";
            }

            // DATA GROUP
            $query_group = DB::table('groups')->where('name', $transaksi_tes->nmgrup);
            $data_group = $query_group->first();

            if ($data_group) {
                $group_unique = $data_group->id;
            } else {
                $group_unique = null;
            }

            // DATA ANALYZER
            $query_analyzer = DB::table('analyzers')->where('name', $transaksi_tes->analyzer);
            $data_analyzer = $query_analyzer->first();

            if ($data_analyzer) {
                $analyzer_unique = $data_analyzer->id;
            } else {
                $analyzer_unique = null;
            }

            $transaction_id = $transaction_unique;
            $test_id = $test_unique;
            $package_id = null;
            $price_id = null;
            $group_id = $group_unique;
            $analyzer_id = $analyzer_unique;
            $mark_duplo = 0;
            $type = 'single';
            if ($range_type == 'number') {
                $result_number = trim($transaksi_tes->hasil);
                $result_label = null;
                $result_text = null;
            } else if ($range_type == 'label') {
                $result_label = null;
                $result_number = null;
                $result_text = null;
            } else if ($range_type == 'description') {
                $result_text = trim($transaksi_tes->hasil_m);
                $result_number = null;
                $result_label = null;
            } else if ($range_type == 'free_formatted_text') {
                $result_text = trim($transaksi_tes->hasil_m);
                $result_number = null;
                $result_label = null;
            }else if ($range_type == 'error') {
                $result_text = null;
                $result_number = null;
                $result_label = null;
            }

            $draw = 1;
            $draw_memo = null;
            $undraw_memo = null;
            $result_status = 1;
            $draw_time = $checkin_time;
            $input_time = $created_time;
            $verify = 1;
            $validate = 1;
            $report_status = 0;
            $report_by = null;
            $report_to = null;
            $memo_test = null;
            $verify_by = null;
            $validate_by = null;
            $verify_time = $post_time;
            $validate_time = $post_time;
            $report_time = null;
            $check_code = $general_code;
            $draw_by = null;
            $created_at = Carbon::now();
            $updated_at = Carbon::now();

            DB::table('transaction_tests')
                ->insert([
                    'transaction_id' => $transaction_id,
                    'test_id' => $test_id,
                    'package_id' => $package_id,
                    'price_id' => $price_id,
                    'group_id' => $group_id,
                    'analyzer_id' => $analyzer_id,
                    'mark_duplo' => $mark_duplo,
                    'type' => $type,
                    'result_number' => $result_number,
                    'result_label' => $result_label,
                    'result_text' => $result_text,
                    'draw' => $draw,
                    'draw_memo' => $draw_memo,
                    'undraw_memo' => $undraw_memo,
                    'result_status' => $result_status,
                    'draw_time' => $draw_time,
                    'input_time' => $input_time,
                    'verify' => $verify,
                    'validate' => $validate,
                    'report_status' => $report_status,
                    'report_by' => $report_by,
                    'report_to' => $report_to,
                    'memo_test' => $memo_test,
                    'verify_by' => $verify_by,
                    'validate_by' => $validate_by,
                    'verify_time' => $verify_time,
                    'validate_time' => $validate_time,
                    'report_time' => $report_time,
                    'check_code' => $check_code,
                    'draw_by' => $draw_by,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                ]);
            $index++;
        }

        echo 'Transaksi ke - ' . $index . ' = DONE' . '<hr>';
    }
    public function finishTransactionData()
    {
        // DATA TRANSAKSI
        $startDate = '2023-04-01';
        $endDate = '2023-04-30';

        $query = DB::table('transactions')
            ->select(
                'transactions.*',
                'patients.id as patient_id',                            // PATIENTS
                'patients.name as patient_name',                        // PATIENTS
                'patients.medrec as patient_medrec',                    // PATIENTS
                'patients.address as patient_address',                  // PATIENTS
                'patients.gender as patient_gender',                    // PATIENTS
                'patients.birthdate as patient_birthdate',              // PATIENTS
                'patients.email as patient_email',                      // PATIENTS
                'patients.phone as patient_phone',                      // PATIENTS
                'rooms.id as room_id',                                  // ROOMS
                'rooms.room as room_name',                              // ROOMS
                'doctors.id as doctor_id',                              // DOCTORS
                'doctors.name as doctor_name',                          // DOCTORS
                'insurances.id as insurance_id',                        // INSURANCES
                'insurances.name as insurance_name',                    // INSURANCES
                'analyzers.id as analyzer_id',                          // ANALYZERS
                'analyzers.name as analyzer_name',                      // ANALYZERS
            )
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->leftJoin('doctors', 'transactions.doctor_id', '=', 'doctors.id')
            ->leftJoin('insurances', 'transactions.insurance_id', '=', 'insurances.id')
            ->leftJoin('analyzers', 'transactions.analyzer_id', '=', 'analyzers.id');
        $query->whereRaw("date(created_time) between '" . $startDate . "' and '" . $endDate . "'");
        $query->orderBy('created_time', 'asc');
        $data_transaksi = $query->get();

        foreach ($data_transaksi as $transaksi) {

            DB::table('finish_transactions')
                ->insert([
                    'patient_id' => $transaksi->patient_id,
                    'transaction_id' => $transaksi->id,
                    'patient_name' => $transaksi->patient_name,
                    'patient_medrec' => $transaksi->patient_medrec,
                    'patient_address' => $transaksi->patient_address,
                    'patient_phone' => $transaksi->patient_phone,
                    'patient_email' => $transaksi->patient_email,
                    'patient_gender' => $transaksi->patient_gender,
                    'patient_birthdate' => $transaksi->patient_birthdate,
                    'room_id' => $transaksi->room_id,
                    'room_name' => $transaksi->room_name,
                    'doctor_id' => $transaksi->doctor_id,
                    'doctor_name' => $transaksi->doctor_name,
                    'insurance_id' => $transaksi->insurance_id,
                    'insurance_name' => $transaksi->insurance_name,
                    'analyzer_id' => $transaksi->analyzer_id,
                    'analyzer_name' => $transaksi->analyzer_name,
                    'type' => $transaksi->type,
                    'no_lab' => $transaksi->no_lab,
                    'note' => $transaksi->note,
                    'status' => $transaksi->status,
                    'is_igd' => $transaksi->is_igd,
                    'cito' => $transaksi->cito,
                    'check' => $transaksi->check,
                    'draw' => $transaksi->draw,
                    'result_status' => $transaksi->result_status,
                    'verify_status' => $transaksi->verify_status,
                    'validate_status' => $transaksi->validate_status,
                    'report_status' => $transaksi->report_status,
                    'created_time' => $transaksi->created_time,
                    'checkin_time' => $transaksi->checkin_time,
                    'analytic_time' => $transaksi->analytic_time,
                    'post_time' => $transaksi->post_time,
                    'memo_result' => $transaksi->memo_result,
                    'print' => $transaksi->print,
                    'get_status' => $transaksi->get_status,
                    'checkin_by' => null,
                    'verficator_id' => null,
                    'verficator_name' => null,
                    'validator_id' => null,
                    'validator_name' => null,
                    'shipper' => $transaksi->shipper,
                    'receiver' => $transaksi->receiver,
                    'no_order' => $transaksi->no_order,
                    'created_at' => $transaksi->created_at,
                    'updated_at' => $transaksi->updated_at,
                    'is_print_memo' => $transaksi->is_print_memo,
                    'checkin_by_name' => null,
                    'completed' => 1,
                    'completed_time' => $transaksi->post_time
                ]);
        }
    }

    public function finishTransactionTestData()
    {
        // DATA TRANSAKSI TES
        $startDate = '2023-04-01';
        $endDate = '2023-04-30';

        $query = DB::table('finish_transactions');
        $query->whereRaw("date(created_time) between '" . $startDate . "' and '" . $endDate . "'");
        $query->orderBy('created_time', 'asc');
        $data_finish_transaksi = $query->get();

        foreach ($data_finish_transaksi as $finish_transaction) {

            $query_test = DB::table('transaction_tests')
                ->select(
                    'transaction_tests.*',
                    'tests.id as test_id',
                    'tests.name as test_name',
                    'tests.sequence as sequence',
                    'tests.sub_group as sub_group',
                    'tests.initial as initial',
                    'tests.unit as unit',
                    'tests.volume as volume',
                    'tests.normal_notes as normal_notes',
                    'tests.general_code as general_code',
                    'groups.id as package_id',
                    'groups.name as group_name',
                    'specimens.id as specimen_id',
                    'specimens.name as specimen_name'
                )
                ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
                ->leftJoin('specimens', 'tests.specimen_id', '=', 'specimens.id')
                ->leftJoin('groups', 'transaction_tests.group_id', '=', 'groups.id')
                ->where('transaction_id', $finish_transaction->transaction_id);
            $transaksi_test = $query_test->get();

            foreach ($transaksi_test as $transaksi_tes) {

                // set global result
                if ($transaksi_tes->result_number) {
                    $global_result = $transaksi_tes->result_number;
                } else if ($transaksi_tes->result_label) {
                    $global_result = $transaksi_tes->result_label;
                } else {
                    $global_result = $transaksi_tes->result_text;
                }

                $patient_birthdate = $finish_transaction->patient_birthdate;
                $bornDate = $patient_birthdate;

                $ageInDays = Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(Carbon::now());

                //set normal value
                if ($finish_transaction->patient_gender == 'F') {
                    if ($transaksi_tes->result_number) {
                        $ranges = \App\Range::where('test_id', $transaksi_tes->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();
                        if ($ranges) {
                            $normal_value = $ranges->normal_female;
                        } else {
                            $normal_value = "-";
                        }
                    } else {
                        $normal_value = $transaksi_tes->normal_notes;
                    }
                } else if ($finish_transaction->patient_gender == 'M') {
                    if ($transaksi_tes->result_number) {
                        $ranges = \App\Range::where('test_id', $transaksi_tes->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();
                        if ($ranges) {
                            $normal_value = $ranges->normal_male;
                        } else {
                            $normal_value = "-";
                        }
                    } else {
                        $normal_value = $transaksi_tes->normal_notes;
                    }
                }

                DB::table('finish_transaction_tests')
                    ->insert([
                        'finish_transaction_id' => $finish_transaction->id,
                        'transaction_id' => $finish_transaction->transaction_id,
                        'test_id' => $transaksi_tes->test_id,
                        'test_name' => $transaksi_tes->test_name,
                        'package_id' => null,
                        'package_name' => null,
                        'price_id' => null,
                        'group_id' => $transaksi_tes->group_id,
                        'group_name' => $transaksi_tes->group_name,
                        'analyzer_id' => null,
                        'analyzer_name' => null,
                        'specimen_id' => $transaksi_tes->specimen_id,
                        'specimen_name' => $transaksi_tes->specimen_name,
                        'mark_duplo' => $transaksi_tes->mark_duplo,
                        'type' => $transaksi_tes->type,
                        'result_number' => $transaksi_tes->result_number,
                        'result_label' => $transaksi_tes->result_label,
                        'result_text' => $transaksi_tes->result_text,
                        'draw' => $transaksi_tes->draw,
                        'draw_memo' => $transaksi_tes->draw_memo,
                        'undraw_memo' => $transaksi_tes->undraw_memo,
                        'result_status' => $transaksi_tes->result_status,
                        'draw_time' => $transaksi_tes->draw_time,
                        'input_time' => $transaksi_tes->input_time,
                        'verify' => $transaksi_tes->verify,
                        'validate' => $transaksi_tes->validate,
                        'report_status' => $transaksi_tes->report_status,
                        'report_by' => $transaksi_tes->report_by,
                        'report_to' => $transaksi_tes->report_to,
                        'memo_test' => $transaksi_tes->memo_test,
                        'verify_by' => $transaksi_tes->verify_by,
                        'verify_by_name' => null,
                        'validate_by' => $transaksi_tes->validate_by,
                        'validate_by_name' => null,
                        'verify_time' => $transaksi_tes->verify_time,
                        'validate_time' => $transaksi_tes->validate_time,
                        'report_time' => $transaksi_tes->report_time,
                        'is_print' => 1,
                        'global_result' => $global_result,
                        'sub_group' => $transaksi_tes->sub_group,
                        'initial' => $transaksi_tes->initial,
                        'unit' => $transaksi_tes->unit,
                        'volume' => $transaksi_tes->volume,
                        'normal_notes' => null,
                        'general_code' => null,
                        'sequence' => $transaksi_tes->sequence,
                        'normal_value' => $normal_value,
                        'result_status_label' => 'Normal',
                        'draw_by' => $transaksi_tes->draw_by,
                        'draw_by_name' => null,
                        'created_at' => $transaksi_tes->created_at,
                        'updated_at' => $transaksi_tes->updated_at,
                    ]);
            }
        }
    }
}
