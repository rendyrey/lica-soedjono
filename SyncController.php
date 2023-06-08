<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
Use Exception;

class SyncController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public 
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // return view('home')->with('menus');
        // $users = DB::connection('mysql')->table('users')->get();
        // print_r($users);

        $test = DB::connection('sqlsrv')->table('TA_METODE_NILAI')
                           ->select(\DB::raw("*"))
                           ->get();
        echo "<pre>";
        print_r($test);
        echo "Oke";
        $find = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
            ->select('TA_TRS_TDK_UMUM5.*','TA_JENIS_PEMERIKSAAN.FS_NM_JENIS_PEMERIKSA')
            ->leftJoin('TA_JENIS_PEMERIKSAAN', 'TA_JENIS_PEMERIKSAAN.FS_KD_JENIS_PEMERIKSAAN', '=', 'TA_TRS_TDK_UMUM5.FS_KD_JENIS_PEMERIKSAAN')
            // ->whereIn('FS_KD_TRS', $arr_kode)
            ->get();
            echo "<pre>";
        print_r($find);
        echo "Oke";
        // echo "123123";
        // return view('test');

    }

    public function sync_data_from_server(){

        //get all patient record on sql server databasea
        $patient_record_sqlserver = DB::connection('sqlsrv')->select(DB::raw("
            select TOP 1000
                mr.FS_NM_PASIEN as nama_pasien,
                mr.FD_TGL_LAHIR as tgl_lahir,
                case
                    when mr.Fs_JNS_KELAMIN = 1 then 'P'
                    else 'L' end as jk,
                    mr.FS_ALM_PASIEN as alamat,
                    mr.FS_TLP_PASIEN as no_telp,
                    mr.FS_MR as no_rkm_medis,
                    tr.FS_KD_REG,
                    ttu.FS_KD_TRS,
                    ttu.FD_TGL_TRS as tgl_permintaan,
                    ttu.FS_JAM_TRS as jam_permintaan,
                    ttu.FS_KD_LAYANAN_LAB_ASAL as kode_ruangan,
                    tl.FS_NM_LAYANAN as ruangan,
                    ttu.FS_KD_DOKTER_PERUJUK as kode_dokter,
                    tp.FS_NM_PEG as dokter,
                    ttj.FS_KD_TIPE_JAMINAN as kode_pembayaran,
                    ttj.FS_NM_TIPE_JAMINAN as pembayaran,
                    ttu.FS_NO_LAB
                from
                    TC_MR mr
                join TA_REGISTRASI tr on
                    tr.FS_MR = mr.FS_MR
                join TA_TRS_TDK_UMUM ttu on
                    ttu.FS_KD_REG = tr.FS_KD_REG
                join TA_LAYANAN tl on
                    tl.FS_KD_LAYANAN = ttu.FS_KD_LAYANAN_LAB_ASAL
                join TD_PEG tp on
                    tp.FS_KD_PEG = ttu.FS_KD_DOKTER_PERUJUK
                join TA_TRS_TDK_UMUM2 ttu2 on ttu.FS_KD_TRS = ttu2.FS_KD_TRS
                join TA_TIPE_JAMINAN ttj on ttj.FS_KD_TIPE_JAMINAN = ttu2.FS_KD_TIPE_JAMINAN
                where
                    ttu.STATUS_LIS = 0
                group by
                    mr.FS_NM_PASIEN,
                    mr.FD_TGL_LAHIR,
                    mr.Fs_JNS_KELAMIN,
                    mr.FS_ALM_PASIEN,
                    mr.FS_TLP_PASIEN,
                    mr.FS_MR,
                    tr.FS_KD_REG,
                    ttu.FS_KD_TRS,
                    ttu.FD_TGL_TRS,
                    ttu.FS_JAM_TRS,
                    ttu.FS_KD_LAYANAN_LAB_ASAL,
                    tl.FS_NM_LAYANAN,
                    ttu.FS_KD_DOKTER_PERUJUK,
                    tp.FS_NM_PEG,
                    ttj.FS_KD_TIPE_JAMINAN,
                    ttj.FS_NM_TIPE_JAMINAN,
                    ttu.FS_NO_LAB"));

        //insert log get data from server
        DB::table('log_integration')
        ->insert([
            'no_order' => "sync",
            'return_result' => json_encode($patient_record_sqlserver),
            'timestamp' => Carbon::now(),
            'status' => "returndatasyncfromserver",
            'notes' => "GET DATA FROM SERVER"

        ]);
        // echo "<pre>";
        // print_r($patient_transaction);
        // print_r($patient_record_sqlserver);
        // die();

        //generate patient data demografi and transaction for payload
        $patient_list_insert = $this->generate_patient_data($patient_record_sqlserver);

        //generate patient transaction test data for payload
        $patient_transaction_list_insert = $this->generate_patient_transaction($patient_record_sqlserver);
        // print_r($patient_list_insert);
        // print_r($patient_transaction_list_insert);
        // echo "oke2";
        $status_insert =true;
        if($patient_list_insert){
            foreach ($patient_list_insert as $key => $value) {
                foreach ($patient_transaction_list_insert as $k2 => $trs) {
                    if($value['transaksi']['no_order'] == $trs['kd_trs']){
                        array_push($value['tes'],$trs['data']);
                    }
                }
                 //insert log get data from server
                DB::table('log_integration')
                ->insert([
                    'no_order' =>$value['transaksi']['no_order']  ,
                    'return_result' => json_encode($value),
                    'timestamp' => Carbon::now(),
                    'status' => "payloadinsertpatient",
                    'notes' => "PREPARING DATA FOR INSERT"
                ]);
                // print_r($value);
               $insert = $this->insertPatient($value);
               // $insert = true;
                if(!$insert){
                    $status_insert = false;
                }
                // code...
            }
        }
        if($status_insert){

            return response()->json([
                'error' => [
                    'message' => 'Sukses',
                    'status_code' => 200
                ]
            ]);
        }else{
            return response()->json([
                'error' => [
                    'message' => 'gagal',
                    'status_code' => 400
                ]
            ]);
        }

    }

    private function generate_patient_data($patient_record_sqlserver){
        $patient_list= [];
        // //loop record patient available
        foreach ($patient_record_sqlserver as $k2 => $p2) {
            if(substr(strtolower($p2->kode_ruangan),0,1) == "b"){
                $jenis = "Rawat Inap";
                $kode_jenis = 1;
            }else if(substr(strtolower($p2->kode_ruangan),0,1) == "p"){
                $jenis = "Rawat Jalan";
                $kode_jenis = 2;
            }else{
                $kode_jenis = "";
                $jenis = "";
            }
            $new_data = array(
                "demografi"=> array(
                    "no_rkm_medis"=> substr($p2->no_rkm_medis, -6),
                    "nama_pasien"=>$p2->nama_pasien ,
                    "tgl_lahir"=>$p2->tgl_lahir ,
                    "jk"=>$p2->jk ,
                    "alamat"=>$p2->alamat ,
                    "no_telp"=>$p2->no_telp
                ),
                "transaksi"=>array(
                    "no_order" => $p2->FS_KD_TRS,
                    "no_lab" => '20' . $p2->FS_NO_LAB,
                    "draw" => 1,
                    "tgl_permintaan" => $p2->tgl_permintaan,
                    "jam_permintaan" => $p2->jam_permintaan,
                    "kode_pembayaran" => $p2->kode_pembayaran,
                    "pembayaran" => $p2->pembayaran,
                    "kode_ruangan" => $p2->kode_ruangan,
                    "kelas" => 1,
                    "ruangan" => $p2->ruangan,
                    "kode_jenis" => $kode_jenis,
                    "jenis" => $jenis,
                    "kode_dokter" => $p2->kode_dokter,
                    "dokter" => $p2->dokter,
                ),
                "tes"=>[],
            );
            $patient_list[] = $new_data;
        }

        return $patient_list;

    }

    private function generate_patient_transaction($patient_record_sqlserver){
        $patient_transaction_list= [];
        $arr_kode = [];
        //get all trans_kode
        foreach ($patient_record_sqlserver as $key => $value) {
            if(!in_array($value->FS_KD_TRS, $arr_kode) ){
                array_push($arr_kode, $value->FS_KD_TRS);
            }
        }

        if($arr_kode){

            //versi reference tdk umum 5
            // $find = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
            // ->select('TA_TRS_TDK_UMUM5.*','TA_JENIS_PEMERIKSAAN.FS_NM_JENIS_PEMERIKSAAN')
            // ->leftJoin('TA_JENIS_PEMERIKSAAN', 'TA_JENIS_PEMERIKSAAN.FS_KD_JENIS_PEMERIKSAAN', '=', 'TA_TRS_TDK_UMUM5.FS_KD_JENIS_PEMERIKSAAN')
            // ->whereIn('FS_KD_TRS', $arr_kode)
            // ->get();

            // if($find){
            //     foreach ($find as $key => $value) {
            //         // code...
            //         $data = array(
            //             "kd_trs"=>$value->FS_KD_TRS,
            //             "data"=>array(
            //                 "kode_jenis_tes"=>$value->FS_KD_JENIS_PEMERIKSAAN,
            //                 "nama_tes"=>$value->FS_NM_JENIS_PEMERIKSAAN,
            //                 "cito"=>1,
            //             ),
            //         );
            //         array_push($patient_transaction_list, $data);
            //     }
            // }

            //versi reference TA_TR_LAB_BRIDGE 
            $find = DB::connection('sqlsrv')->table('TA_TRS_LAB_BRIDGE')
            ->select('FS_KD_TRS' ,'FS_KD_TARIF2')
            ->whereIn('FS_KD_TRS', $arr_kode)
            ->groupBy('FS_KD_TRS' ,'FS_KD_TARIF2')
            ->get();

            if($find){
                $arr_kd_trs = [];
                foreach ($find as $key => $value) {
                    $data = array(
                        "kd_trs"=>$value->FS_KD_TRS,
                        "data"=>array(
                            "kode_jenis_tes"=>$value->FS_KD_TARIF2,
                            "nama_tes"=>"Test Paket",
                            "cito"=>1,
                        ),
                    );
                    array_push($patient_transaction_list, $data);
                }
            }
        }
        return $patient_transaction_list;
    }

    private function insertPatient($req){
        $demografi = $req['demografi'];
        $transaksi = $req['transaksi'];
        $tests = $req['tes'];
        DB::beginTransaction();

        $query = DB::table('master_patients')->where('medrec', $demografi['no_rkm_medis']);
        $patient_data = $query->first();
        if (empty($patient_data)) {
            DB::table('master_patients')
                ->insert([
                    'name' => $demografi['nama_pasien'],
                    'email' => '',
                    'phone' => $demografi['no_telp'],
                    'medrec' => $demografi['no_rkm_medis'],
                    'gender' => $demografi['jk'],
                    'birth' => $demografi['tgl_lahir'],
                    'address' => $demografi['alamat']
                ]);
            $patient_id = DB::getPdo()->lastInsertId();
        } else {
            $patient_id = $patient_data->id;
        }

        //insert transaction
        $query = DB::table('master_room')->where('general_code', $transaksi['kode_ruangan'])->where('kelas', $transaksi['kelas'])->where('jenis', $transaksi['kode_jenis']);
        $room_data = $query->first();
        if (empty($room_data)) {
            DB::rollback();
            DB::table('log_integration')
            ->insert([
                'no_order' => $transaksi['no_order'],
                'return_result' => json_encode($req),
                'timestamp' => Carbon::now(),
                'status' => "insertpatientfailed",
                'notes' => "Ruangan belum terdaftar pada data master LICA"
            ]);
            return false;
        }
        $query = DB::table('master_dokter')->where('general_code', $transaksi['kode_dokter']);
        $doctor_data = $query->first();
        if (empty($doctor_data)) {
            DB::rollback();
            DB::table('log_integration')
            ->insert([
                'no_order' => $transaksi['no_order'],
                'return_result' => json_encode($req),
                'timestamp' => Carbon::now(),
                'status' => "insertpatientfailed",
                'notes' => "Dokter belum terdaftar pada data master LICA"
            ]);
            return false;
        }
        $query = DB::table('master_insurance')->where('general_code', $transaksi['kode_pembayaran']);
        $insurance_data = $query->first();
        if (empty($insurance_data)) {
            DB::rollback();
            DB::table('log_integration')
            ->insert([
                'no_order' => $transaksi['no_order']." ".$transaksi['kode_pembayaran'],
                'return_result' => json_encode($req),
                'timestamp' => Carbon::now(),
                'status' => "insertpatientfailed",
                'notes' => "Jenis Pembayaran/Asuransi belum terdaftar pada data master LICA"
            ]);
            return false;
        }
        $query_check_transaction_exist = DB::table('transactions')->where('no_order', $transaksi['no_order']);
        $check_transaction_exist = $query_check_transaction_exist->first();

        if(!$check_transaction_exist){
            DB::table('transactions')
            ->insert([
                'master_patient_id' => $patient_id,
                'master_room_id' => $room_data->id,
                'master_doctor_id' => $doctor_data->id,
                'master_insurance_id' => $insurance_data->id,
                'type' => $transaksi['kode_jenis'],
                'no_order' => $transaksi['no_order'],
                'no_lab' => $transaksi['no_lab'],
                'draw' => $transaksi['draw'],
                'created_time' => $transaksi['tgl_permintaan']." ".$transaksi['jam_permintaan']
            ]);
            $transaction_id = DB::getPdo()->lastInsertId();
            if (empty($tests)) {
                // DB::rollback();
                // DB::table('log_integration')
                // ->insert([
                //     'no_order' => $transaksi['no_order'],
                //     'return_result' => json_encode($req),
                //     'timestamp' => Carbon::now(),
                //     'status' => "insertpatientfailed",
                //     'notes' => "Data tes pasien kosong"
                // ]);
                // return false;
            }

            //in this version reference change to fs_kd_trf
            foreach ($tests as $test => $value) {
                $query = DB::table('master_packages')->where('general_code', $value['kode_jenis_tes']);
                $test_data = $query->first();

                if (!empty($test_data)) {
                    $query = DB::table('master_package_tests')->where('master_package_id', $test_data->id);
                    $master_package_tests = $query->get();

                    $price_id = DB::table('master_prices')->select('id')->where('single_package_id', $test_data->id)->where('type', 2)->first();
                    // $query_price = DB::table('master_prices')->select('id')->where('single_package_id', $test_data->id)->where('type', 2);
                    // $price_id = $query->first();
                    
                    if (empty($price_id)) {
                        // DB::rollback();
                        DB::table('log_integration')
                        ->insert([
                            'no_order' => $transaksi['no_order'],
                            'return_result' => json_encode($value),
                            'timestamp' => Carbon::now(),
                            'status' => "insertpatientfailed",
                            'notes' => "Data tes tidak terdaftar di LICA"
                        ]);
                        // return false;
                    }else{
                        
                        DB::table('transaction_nota')
                            ->insert([
                                'transaction_id' => $transaction_id,
                                'master_price_id' => $price_id->id,
                                'master_test_id' => 0,
                                'master_package_id' => $test_data->id
                            ]);

                        foreach ($master_package_tests as $master_package_test) {

                            DB::table('transaction_tests')
                                ->insert([
                                    'transaction_id' => $transaction_id,
                                    'master_test_id' => $master_package_test->master_test_id,
                                    'master_price_id' => $price_id->id,
                                    'draw' => 1,
                                    'draw_time' => Carbon::now()->toDateTimeString(),
                                    'fs_kd_trf' => $value['kode_jenis_tes']
                                ]);
                        }
                    }
                } else {
                    //old version before change reference to fs_kd_trf
                    // $query = DB::table('general_code_test')->select('master_tests.*')->leftJoin('master_tests', 'master_tests.id', '=', 'general_code_test.id_master_test')->where('general_code_test.general_code', $value['kode_jenis_tes']);
                    $query = DB::table('master_tests')->select('master_tests.*')->where('master_tests.fs_kd_trf', $value['kode_jenis_tes']);
                    $test_data = $query->first();
                    if (empty($test_data)) {
                        // DB::rollback();
                        // DB::table('log_integration')
                        // ->insert([
                        //     'no_order' => $transaksi['no_order'],
                        //     'return_result' => json_encode($req),
                        //     'timestamp' => Carbon::now(),
                        //     'status' => "insertpatientfailed",
                        //     'notes' => "Data tes " . $value['nama_tes'] . " tidak terdaftar di LICA"
                        // ]);
                        // return false;
                    }else{

                        $price_id = DB::table('master_prices')->select('id')->where('single_package_id', $test_data->id)->first();
                        if (empty($price_id)) {
                            //DB::rollback();

                        DB::table('log_integration')
                                ->insert([
                                    'no_order' => $transaksi['no_order'],
                                    'return_result' =>json_encode($value),
                                    'status' => "synccreatetransactiontesterror",
                                    'notes' => "Data price tidak terdaftar di LICA"
                                ]);
                            //return false;
                        }else{
                            DB::table('transaction_tests')
                            ->insert([
                                'transaction_id' => $transaction_id,
                                'master_test_id' => $test_data->id,
                                'master_price_id' => $price_id->id,
                                'fs_kd_trf' => $value['kode_jenis_tes']
                            ]);

                            DB::table('transaction_nota')
                            ->insert([
                                'transaction_id' => $transaction_id,
                                'master_price_id' => $price_id->id,
                                'master_test_id' => $test_data->id,
                                'master_package_id' => 0
                            ]);
                        }
                    }

                }
            }

            DB::table('log_integration')
                ->insert([
                    'no_order' => $transaksi['no_order'],
                    'return_result' => 'Data Added',
                    'status' => "insertpatientsuccess",
                ]);
            $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM')
                    ->where('FS_KD_TRS', $transaksi['no_order'])
                    ->update(
                        [
                            'STATUS_LIS' => 1,
                        ]
                    );
            DB::commit();
        }
        
        return true;
    }

    public function sync_data_into_server(Request $request){
        $transaction_id = $request->input('id'); 
        // echo $transaction_id;
        $transaction_test = DB::connection('mysql')->select(DB::raw("
        SELECT tr.no_order,tr.analytic_time, tr.created_time, tt.*,gct.general_code,gct.fs_kd_trf, mr.result AS result_master, tt.fs_kd_trf as fs_kd_trf_return,tr.post_time  FROM transactions tr
        JOIN transaction_tests tt ON tt.transaction_id = tr.id
        JOIN master_tests gct ON gct.id = master_test_id
        LEFT JOIN master_results mr ON mr.id = tt.result_label
                WHERE tr.id =  ".$transaction_id."
        "));
        // echo "<pre>";
        // print_r($transaction_test);
        // foreach($transaction_test as $row){
        //     echo 'general code : ' . $row->general_code . '<br>';
        //     echo 'result : ' . $row->result_number . '<hr>';
        // }
        // die;

        $status_insert = 1;
        if($transaction_test){
            // echo "loop";
            $isupdateparent = false;
            foreach ($transaction_test as $key => $value) {
                try
                {
                    //versi insert
                    if($value->result_number){
                        $hasil = $value->result_number;
                    }else if($value->result_label){
                        $hasil = $value->result_master;
                    }else if($value->result_text){
                        $hasil = $value->result_text;
                    }else if($value->result_number == 0){
                        $hasil = 0;
                    }else{
                        $hasil = "";
                    }

                    $query = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                    ->where('FS_KD_TRS', $value->no_order)
                    ->where('FS_KD_JENIS_PEMERIKSAAN', $value->general_code);
                    $check_exist = $query->first();
                    
                    if($isupdateparent == false){
                        
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM')
                        ->where('FS_KD_TRS', $value->no_order)
                        ->update(
                        [
                            'FB_VERIFIKASI' => 1,
                        ]
                        );
                        $isupdateparent = true;
                    }
                    // $check_exist =false;
                    if($check_exist){
                        $data_insert = array(
                            'FS_KD_TARIF' => ($value->fs_kd_trf_return) ? $value->fs_kd_trf_return : "",
                            'FS_HASIL' => $hasil,
                            'FB_VERIFIKASI_JENIS' => 1,

                        );
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                        ->where('FS_KD_TRS', $value->no_order)
                        ->where('FS_KD_JENIS_PEMERIKSAAN', $value->general_code)
                        ->update($data_insert);
                    }else{
                        $data_insert = [
                                'FS_KD_TRS' => $value->no_order,
                                'FS_KD_TARIF' => ($value->fs_kd_trf_return) ? $value->fs_kd_trf_return : "",
                                'FS_KD_JENIS_PEMERIKSAAN' => $value->general_code,
                                'FS_HASIL' => $hasil,
                                'FS_KETERANGAN' => "",
                                'FD_TGL_TEST' => date('Y-m-d', strtotime($value->analytic_time)),
                                'FS_JAM_TEST' => date('H:i:s', strtotime($value->analytic_time)),
                                'FB_VERIFIKASI_JENIS' => 1,
                                'CRTTGL' => date('Y-m-d', strtotime($value->created_time)),
                                'CRTJAM' => date('H:i:s', strtotime($value->created_time)),
                                'CRTIPA' => "",
                                'CRTVER' => "",
                                'CRTUSR' => "",
                                'UPDTGL' => date('Y-m-d'),
                                'UPDJAM' => date('H:i:s'),
                                'UPDIPA' => "",
                                'UPDVER' => "",
                                'UPDUSR' => "",
                                'FS_NM_PEMERIKSAAN' => "",
                                'FS_FLAG_HASIL' => "",
                                'FS_SATUAN' => "",
                                'FS_NILAI_RUJUKAN' => "",
                                'FS_METODE' => "",
                                'FS_VALIDATOR' => "",
                                'FS_KET_NORMAL' => "",
                                'FS_BINTANG' => "",
                                'FD_TGL_NOTA' => "",
                                'FS_JAM_NOTA' => "",
                                'FD_TGL_RUN_TES' => "",
                                'FS_JAM_RUN_TES' => "",
                                'FD_TGL_FINISH' => "",
                                'FS_JAM_FINISH' => "",
                                'FD_TGL_VERIFIKASI' => "",
                                'FS_JAM_VERIFIKASI' => "",
                                'FD_TGL_VALIDASI' => "",
                                'FS_JAM_VALIDASI' => "",
                                'FD_TGL_PERAWAT' => "",
                                'FS_JAM_PERAWAT' => "",
                                'FD_TGL_BACA_KRITIS' => "",
                                'FS_JAM_BACA_KRITIS' => "",
                                'FS_ABNORMAL_KRITIS' => "",
                                'FS_KET_ABNORMAL_KRITIS' => "",
                            ];
                        if($value->post_time){
                            $data_insert['FD_TGL_FINISH'] = date('Y-m-d', strtotime($value->post_time));
                            $data_insert['FS_JAM_FINISH'] = date('H:i:s', strtotime($value->post_time));
                        }
                        if($value->verify_time){
                            $data_insert['FD_TGL_VERIFIKASI'] = date('Y-m-d', strtotime($value->verify_time));
                            $data_insert['FS_JAM_VERIFIKASI'] = date('H:i:s', strtotime($value->verify_time));
                        }
                        if($value->validate_time){
                            $data_insert['FD_TGL_VALIDASI'] = date('Y-m-d', strtotime($value->validate_time));
                            $data_insert['FS_JAM_VALIDASI'] = date('H:i:s', strtotime($value->validate_time));
                        }
                        if($value->report_time){
                            $data_insert['FD_TGL_BACA_KRITIS'] = date('Y-m-d', strtotime($value->report_time));
                            $data_insert['FS_JAM_BACA_KRITIS'] = date('H:i:s', strtotime($value->report_time));
                        }
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                        ->insert($data_insert);
                    }
                    DB::table('log_integration')
                    ->insert([
                        'no_order' => $value->no_order,
                        'return_result' =>json_encode($data_insert),
                        'status' => "syncupdatesuccess",
                        'notes' => $value->fs_kd_trf_return,
                        'timestamp' => Carbon::now(),
                    ]);
                }
                catch(Exception $e)
                {
                    $status_insert =0;
                    DB::table('log_integration')
                        ->insert([
                            'no_order' => $value->no_order,
                            'return_result' =>json_encode($value),
                            'notes' =>$e->getMessage(),
                            'status' => "syncpdatefailed",
                            'timestamp' => Carbon::now(),
                        ]);
                }
            }
        }else{
            DB::table('log_integration')
            ->insert([
                'no_order' => $value->no_order,
                'return_result' =>" SELECT tr.no_order,tr.analytic_time, tr.created_time, tt.*,gct.general_code,gct.fs_kd_trf, mr.result AS result_master, tt.fs_kd_trf as fs_kd_trf_return FROM transactions tr
                JOIN transaction_tests tt ON tt.transaction_id = tr.id
                JOIN master_tests gct ON gct.id = master_test_id
                LEFT JOIN master_results mr ON mr.id = tt.result_label
                WHERE tr.id =  ".$transaction_id."",
                'status' => "syncupdatefailed",
                'notes' => "No data selected"
            ]);
            $status_insert = 0;
        }
        if($status_insert){

            return response()->json([
                'error' => [
                    'message' => 'Sukses',
                    'status_code' => 200
                ]
            ]);
        }else{
            return response()->json([
                'error' => [
                    'message' => 'gagal',
                    'status_code' => 400
                ]
            ]);
        }
    }

    // tambahan jaka
    public function sync_send_validated_data(Request $request)
    {
        $transaction_id = $request->input('id'); 
        // echo $transaction_id;
        $transaction_test = DB::connection('mysql')->select(DB::raw("
        SELECT tr.no_order,tr.analytic_time, tr.created_time, tt.*,gct.general_code,gct.fs_kd_trf, mr.result AS result_master, tt.fs_kd_trf as fs_kd_trf_return,tr.post_time  FROM transactions tr
        JOIN transaction_tests tt ON tt.transaction_id = tr.id
        JOIN master_tests gct ON gct.id = master_test_id
        LEFT JOIN master_results mr ON mr.id = tt.result_label
                WHERE tr.id =  ".$transaction_id."
                AND tt.validate = 1
        "));
        // echo "<pre>";
        // print_r($transaction_test);

        $status_insert = 1;
        if($transaction_test){
            // echo "loop";
            $isupdateparent = false;
            foreach ($transaction_test as $key => $value) {
                try
                {
                    //versi insert
                    if($value->result_number){
                        $hasil = $value->result_number;
                    }else if($value->result_label){
                        $hasil = $value->result_master;
                    }else if($value->result_text){
                        $hasil = $value->result_text;
                    }else if($value->result_number == 0){
                        $hasil = 0;
                    }else{
                        $hasil = "";
                    }

                    $query = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                    ->where('FS_KD_TRS', $value->no_order)
                    ->where('FS_KD_JENIS_PEMERIKSAAN', $value->general_code);
                    $check_exist = $query->first();
                    
                    if($isupdateparent == false){
                        
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM')
                        ->where('FS_KD_TRS', $value->no_order)
                        ->update(
                        [
                            'FB_VERIFIKASI' => 1,
                        ]
                        );
                        $isupdateparent = true;
                    }
                    // $check_exist =false;
                    if($check_exist){
                        $data_insert = array(
                            'FS_KD_TARIF' => ($value->fs_kd_trf_return) ? $value->fs_kd_trf_return : "",
                            'FS_HASIL' => $hasil,
                            'FB_VERIFIKASI_JENIS' => 1,

                        );
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                        ->where('FS_KD_TRS', $value->no_order)
                        ->where('FS_KD_JENIS_PEMERIKSAAN', $value->general_code)
                        ->update($data_insert);
                    }else{
                        $data_insert = [
                                'FS_KD_TRS' => $value->no_order,
                                'FS_KD_TARIF' => ($value->fs_kd_trf_return) ? $value->fs_kd_trf_return : "",
                                'FS_KD_JENIS_PEMERIKSAAN' => $value->general_code,
                                'FS_HASIL' => $hasil,
                                'FS_KETERANGAN' => "",
                                'FD_TGL_TEST' => date('Y-m-d', strtotime($value->analytic_time)),
                                'FS_JAM_TEST' => date('H:i:s', strtotime($value->analytic_time)),
                                'FB_VERIFIKASI_JENIS' => 1,
                                'CRTTGL' => date('Y-m-d', strtotime($value->created_time)),
                                'CRTJAM' => date('H:i:s', strtotime($value->created_time)),
                                'CRTIPA' => "",
                                'CRTVER' => "",
                                'CRTUSR' => "",
                                'UPDTGL' => date('Y-m-d'),
                                'UPDJAM' => date('H:i:s'),
                                'UPDIPA' => "",
                                'UPDVER' => "",
                                'UPDUSR' => "",
                                'FS_NM_PEMERIKSAAN' => "",
                                'FS_FLAG_HASIL' => "",
                                'FS_SATUAN' => "",
                                'FS_NILAI_RUJUKAN' => "",
                                'FS_METODE' => "",
                                'FS_VALIDATOR' => "",
                                'FS_KET_NORMAL' => "",
                                'FS_BINTANG' => "",
                                'FD_TGL_NOTA' => "",
                                'FS_JAM_NOTA' => "",
                                'FD_TGL_RUN_TES' => "",
                                'FS_JAM_RUN_TES' => "",
                                'FD_TGL_FINISH' => "",
                                'FS_JAM_FINISH' => "",
                                'FD_TGL_VERIFIKASI' => "",
                                'FS_JAM_VERIFIKASI' => "",
                                'FD_TGL_VALIDASI' => "",
                                'FS_JAM_VALIDASI' => "",
                                'FD_TGL_PERAWAT' => "",
                                'FS_JAM_PERAWAT' => "",
                                'FD_TGL_BACA_KRITIS' => "",
                                'FS_JAM_BACA_KRITIS' => "",
                                'FS_ABNORMAL_KRITIS' => "",
                                'FS_KET_ABNORMAL_KRITIS' => "",
                            ];
                        if($value->post_time){
                            $data_insert['FD_TGL_FINISH'] = date('Y-m-d', strtotime($value->post_time));
                            $data_insert['FS_JAM_FINISH'] = date('H:i:s', strtotime($value->post_time));
                        }
                        if($value->verify_time){
                            $data_insert['FD_TGL_VERIFIKASI'] = date('Y-m-d', strtotime($value->verify_time));
                            $data_insert['FS_JAM_VERIFIKASI'] = date('H:i:s', strtotime($value->verify_time));
                        }
                        if($value->validate_time){
                            $data_insert['FD_TGL_VALIDASI'] = date('Y-m-d', strtotime($value->validate_time));
                            $data_insert['FS_JAM_VALIDASI'] = date('H:i:s', strtotime($value->validate_time));
                        }
                        if($value->report_time){
                            $data_insert['FD_TGL_BACA_KRITIS'] = date('Y-m-d', strtotime($value->report_time));
                            $data_insert['FS_JAM_BACA_KRITIS'] = date('H:i:s', strtotime($value->report_time));
                        }
                        $update = DB::connection('sqlsrv')->table('TA_TRS_TDK_UMUM5')
                        ->insert($data_insert);
                    }
                    DB::table('log_integration')
                    ->insert([
                        'no_order' => $value->no_order,
                        'return_result' =>json_encode($data_insert),
                        'status' => "syncupdatesuccess",
                        'notes' => $value->fs_kd_trf_return,
                        'timestamp' => Carbon::now(),
                    ]);
                }
                catch(Exception $e)
                {
                    $status_insert =0;
                    DB::table('log_integration')
                        ->insert([
                            'no_order' => $value->no_order,
                            'return_result' =>json_encode($value),
                            'notes' =>$e->getMessage(),
                            'status' => "syncpdatefailed",
                            'timestamp' => Carbon::now(),
                        ]);
                }
            }
        }
        else{
            DB::table('log_integration')
            ->insert([
                'no_order' => $value->no_order,
                'return_result' =>" SELECT tr.no_order,tr.analytic_time, tr.created_time, tt.*,gct.general_code,gct.fs_kd_trf, mr.result AS result_master, tt.fs_kd_trf as fs_kd_trf_return FROM transactions tr
                JOIN transaction_tests tt ON tt.transaction_id = tr.id
                JOIN master_tests gct ON gct.id = master_test_id
                LEFT JOIN master_results mr ON mr.id = tt.result_label
                WHERE tr.id =  ".$transaction_id."",
                'status' => "syncupdatefailed",
                'notes' => "No data selected"
            ]);
            $status_insert = 0;
        }
        if($status_insert){

            return response()->json([
                'error' => [
                    'message' => 'Sukses',
                    'status_code' => 200
                ]
            ]);
        }else{
            return response()->json([
                'error' => [
                    'message' => 'gagal',
                    'status_code' => 400
                ]
            ]);
        }
    }
}

