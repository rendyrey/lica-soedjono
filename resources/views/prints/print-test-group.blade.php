<!DOCTYPE html>
<html>

<head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
    <title></title>
    <style type="text/css">
        body {
            font-family: "Arial", sans-serif;
            /*font-family: 'Courier New', monospace;*/
            font-size: 12px;
            /*margin-left: 2em; */
            /*margin-top: -5em;*/
        }

        #header {
            /* position: fixed;
            /* width: 100%;
            height: 100%; */
            /* padding: 10px; */
            margin-top: 0px
        }

        p {
            margin: 0;
            /*display: table-cell;*/
        }

        /* #content {
            margin-top: 150px;
            display: inline;
        }
        .spacer
        {
            width: 100%;
            height: 95px;
        } */
        table {
            width: 100%;
            margin: 0 auto;
        }

        tr th {
            background: #eee;

        }

        .border-bottom {
            border-bottom: 1px solid black;
        }

        tr,
        td {
            /* border: 1px solid black; */
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        caption {
            text-align: left;
        }

        img {
            margin-top: 10px;
            margin-left: 40px;
            height: 80px;
            width: 80px;
        }

        .content-width {
            padding-right: 45px;
        }

        .footer-width {
            padding-right: 100px;
        }

        .footer-bottom {
            padding-bottom: 125px;
        }

        #header {
            /*border-style: solid;
            border-width: 1px;*/
            margin-bottom: 10px;
            margin-top: -15px
        }
    </style>
</head>

<body>
<div id="header">
        <table>
            <tr>
                <td width="15%">
                    <div>
                        <img src="{{asset('images/logo-diponegoro.png')}}" style="margin-left: 0px; width: 85%; height: 85%">
                    </div>
                </td>
                <td width="67%" style="margin-top:-15px;">
                    <p style="font-size: 28px;text-align: center;"><b>LABORATORIUM KLINIK</b></p>
                    <p style="font-size: 30px;text-align: center;"><b>RUMKIT TK II dr. SOEDJONO</b></p>
                    <p style="font-size: 24px;text-align: center;"><b>MAGELANG</b></p>
                    <font size="3">
                        <p align="center" style="font-size: 16px;">Jl. Urip Sumoharjo 48 Telp. (0293) 363061</p>
                        <p align="center" style="font-size: 16px;">Fax. (0293) 363366 Magelang</p>
                    </font>
                </td>
                <td width="18%">
                    <div>
                        <img src="{{asset('images/logo-hesti-wira-sakti.png')}}" style="margin-left: 0px; width: 100%; height: 100%">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <center>
        <h3 style="margin-bottom: -5px">HASIL PEMERIKSAAN LABORATORIUM</h3>
    </center>
    <hr style="border: 1px solid black">
    <table>
        <tr>
            <td style="text-align: right;">Cetakan ke : {{$transaction->print}}</td>
        </tr>
    </table>
    <!-- <hr style="border: 1px solid black"> -->
    <table>
        <tbody>
            <tr>
                <td style="text-align: left; width: 15%;">Nama Pasien</td>
                <td style="text-align: left; width: 40%;">: <b> {{ $transaction->patient_name }} </b> </td>
                <td style="text-align: left;">No. RM/ No Lab</td>
                <td style="text-align: left;">: <b>{{$transaction->patient_medrec}} / {{$transaction->no_lab}}</b></td>
            </tr>
            <tr>
                <td style="text-align: left;">Tgl Lahir (Umur)</td>
                <td style="text-align: left;">: <?= date('d/m/Y',  strtotime($transaction->patient_birthdate)); ?> ({{$age}})</td>
                <td style="text-align: left; width: 15%;">Tanggal </b></td>
                <td style="text-align: left; width: 30%;">: <?= date('d/m/Y', strtotime($transaction->checkin_time)); ?></td>
            </tr>
            <tr>
                <td style="text-align: left;">Jenis Kelamin </td>
                @if ($transaction->patient_gender == 'M')
                <td style="text-align: left;">: L</td>
                @elseif ($transaction->patient_gender == 'F')
                <td style="text-align: left;">: P</td>
                @endif
                <td style="text-align: left;">Dokter Pengirim </td>
                <td style="text-align: left;">: {{ $transaction->doctor_name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Alamat Pasien</td>
                <td style="text-align: left;">: {{ $transaction->patient_address }}</td>
                <td style="text-align: left;">Asal Ruangan / Kelas </td>
                <td style="text-align: left;">: {{ $transaction->room_name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Diagnosis Kerja </td>
                <td style="text-align: left;">: {{ $transaction->note }} </td>
                <td style="text-align: left;">Status Klaim </td>
                <td style="text-align: left;">: {{ $transaction->insurance_name }} </td>
            </tr>
            <tr>
                <td style="text-align: left;">Penanggungjawab</td>
                <td style="text-align: left;">: dr. Inge Kusumaningdiyah,Sp.PK </td>
            </tr>
        </tbody>
    </table>
    <hr style="border: 1px solid black">
    <table id="tb_result" width="100%" style="border-bottom: 1px solid black">
        <thead>
            <tr>
                <th class="border-bottom" id="content" style="text-align: left; padding-left:15px; width:25%;">JENIS PEMERIKSAAN</th>
                <th class="border-bottom" id="content" style="width:5%;"></th>
                <th class="border-bottom" id="content" style="text-align: left; width:15%;">HASIL</th>
                <th class="border-bottom" id="content" style="text-align: left; width:20%;">NILAI RUJUKAN</th>
                <th class="border-bottom" id="content" style="text-align: left; width:10%;">SATUAN</th>
                <th class="border-bottom" id="content" style="text-align: left; width:15%;">KETERANGAN</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $group_test = '';
            $sub_group = '';
            $package_name = '';
            $new_page = 0;
            $row = 0;
            $page = 1; ?>
            @foreach ($tests as $test)
            <?php $hitung;
            ?>
            @if((($group_test != '') && ($group_test != $test->group_name) && (count($groups[$test->group_name]) + $row > 30)) || ($row > 50))
            <?php $row = 0; ?>
        </tbody>
    </table>
    <hr>
    <table>
        <tbody>
            <tr>
                <!--<td style="text-align: right;"> Hal.  {{ $page }}</td>-->
            </tr>
        </tbody>
    </table>
    <?php $page++; ?>
    <div id="header" class="page-break">
        <table>
            <tr>
                <td width="15%">
                    <div>
                        <img src="{{asset('images/logo-diponegoro.png')}}" style="margin-left: 0px; width: 85%; height: 85%">
                    </div>
                </td>
                <td width="67%" style="margin-top:-15px;">
                    <p style="font-size: 28px;text-align: center;"><b>LABORATORIUM KLINIK</b></p>
                    <p style="font-size: 30px;text-align: center;"><b>RUMKIT TK II dr. SOEDJONO</b></p>
                    <p style="font-size: 24px;text-align: center;"><b>MAGELANG</b></p>
                    <font size="3">
                        <p align="center" style="font-size: 16px;">Jl. Urip Sumoharjo 48 Telp. (0293) 363061</p>
                        <p align="center" style="font-size: 16px;">Fax. (0293) 363366 Magelang</p>
                    </font>
                </td>
                <td width="18%">
                    <div>
                        <img src="{{asset('images/logo-hesti-wira-sakti.png')}}" style="margin-left: 0px; width: 100%; height: 100%">
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <center>
        <h3 style="margin-bottom: -5px">HASIL PEMERIKSAAN LABORATORIUM</h3>
    </center>
    <hr style="border: 1px solid black">
    <table>
        <tr>
            <td style="text-align: right;">Cetakan ke : {{$transaction->print}}</td>
        </tr>
    </table>
    <!-- <hr style="border: 1px solid black"> -->
    <table>
        <tbody>
            <tr>
                <td style="text-align: left; width: 15%;">Nama Pasien</td>
                <td style="text-align: left; width: 40%;">: <b> {{ $transaction->patient_name }} </b> </td>
                <td style="text-align: left;">No. RM/ No Lab</td>
                <td style="text-align: left;">: <b>{{$transaction->patient_medrec}} / {{$transaction->no_lab}}</b></td>
            </tr>
            <tr>
                <td style="text-align: left;">Tgl Lahir (Umur)</td>
                <td style="text-align: left;">: <?= date('d/m/Y',  strtotime($transaction->patient_birthdate)); ?> ({{$age}})</td>
                <td style="text-align: left; width: 15%;">Tanggal </b></td>
                <td style="text-align: left; width: 30%;">: <?= date('d/m/Y', strtotime($transaction->checkin_time)); ?></td>
            </tr>
            <tr>
                <td style="text-align: left;">Jenis Kelamin </td>
                @if ($transaction->patient_gender == 'M')
                <td style="text-align: left;">: L</td>
                @elseif ($transaction->patient_gender == 'F')
                <td style="text-align: left;">: P</td>
                @endif
                <td style="text-align: left;">Dokter Pengirim </td>
                <td style="text-align: left;">: {{ $transaction->doctor_name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Alamat Pasien</td>
                <td style="text-align: left;">: {{ $transaction->patient_address }}</td>
                <td style="text-align: left;">Asal Ruangan / Kelas </td>
                <td style="text-align: left;">: {{ $transaction->room_name }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">Diagnosis Kerja </td>
                <td style="text-align: left;">: {{ $transaction->note }} </td>
                <td style="text-align: left;">Status Klaim </td>
                <td style="text-align: left;">: {{ $transaction->insurance_name }} </td>
            </tr>
            <tr>
                <td style="text-align: left;">Penanggungjawab</td>
                <td style="text-align: left;">: dr. Inge Kusumaningdiyah,Sp.PK </td>
            </tr>
        </tbody>
    </table>
    <hr style="border: 1px solid black">
    <table id="tb_result" width="100%" style="border-bottom: 1px solid black;">
        <tr>
            <th class="border-bottom" id="content" style="text-align: left; padding-left:15px; width:25%;">JENIS PEMERIKSAAN</th>
            <th class="border-bottom" id="content" style="width:5%;"></th>
            <th class="border-bottom" id="content" style="text-align: left; width:15%;">HASIL</th>
            <th class="border-bottom" id="content" style="text-align: left; width:20%;">NILAI RUJUKAN</th>
            <th class="border-bottom" id="content" style="text-align: left; width:10%;">SATUAN</th>
            <th class="border-bottom" id="content" style="text-align: left; width:15%;">KETERANGAN</th>
        </tr>
        </thead>
        <?php $new_page = 1; ?>
        @endif
        @if (($group_test == '')||($group_test != $test->group_name))
        <?php $group_test = $test->group_name; ?>

        <tr id="content">
            <td style="padding-left:15px; padding-bottom: 5px;"><span style="font-size:10px; font-style: italic; font-weight: bold">{{ $test->group_name }}</span></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $row++; ?>
        @endif

        @if($test->print_package_name == 1)
        @if (($package_name == '')||($package_name != $test->package_name))
        <?php $package_name = $test->package_name; ?>

        <tr id="content">
            <td style="padding-left:15px; padding-bottom: 5px;"><span style="font-size:10px; font-weight: bold">{{ $test->package_name }}</span></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $row++; ?>

        @endif
        @endif

        @if (($sub_group == '')||($sub_group != $test->sub_group))
        @if ($test->sub_group != '')
        <tr id="content">
            <td style="padding-left:25px; padding-bottom: 3px;"><span style="font-size:10px;font-weight: bold">{{ $test->sub_group }}</span></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php $row++; ?>

        @endif
        <?php $sub_group = $test->sub_group; ?>

        @endif

        <tr id="content">

            <!-- td test name -->
            @if ($sub_group == '')
            <td id="content" valign="top" style="padding-left:35px; padding-bottom: 3px;">{{ $test->test_name }}</td>
            @else
            <td id="content" valign="top" style="padding-left:35px; padding-bottom: 3px;">{{ $test->test_name }}</td>
            @endif
            <!-- end td test name -->

            <!-- td flagging  -->
            @if ($test->result_status_label == 'Critical')
            <td id="content" style="text-align: right; padding-bottom: 3px;">**</td>
            @elseif ($test->result_status_label == 'Abnormal')
            <td id="content" style="text-align: right; padding-bottom: 3px;">*</td>
            @elseif ($test->result_status_label == 'Normal')
            <td id="content" style="text-align: right; padding-bottom: 3px;"></td>
            @else
            <td id="content" style="text-align: right; padding-bottom: 3px;"></td>
            @endif
            <!-- end td flagging -->

            <?php
            $result = $test->result;
        
            if(is_string($test->result)){
                // $result = str_replace(" ; ","<br>",$test->result ); 
                $result = $test->result;
            }
            ?>
            
            <!-- td result -->
            @if (strlen($test->result) > 15)
            @if ($test->normal_value == '' && $test->test_unit == '')
            <td colspan="4" id="content" style="text-align: left; padding-bottom: 3px;">{!! $result !!}</td>
            @else
            <td id="content" style="text-align: right; padding-bottom: 3px;">{!! $result !!}</td>
            @endif
            <?php $row = $row + 4; ?>
            @else
            <td id="content" style="text-align: left; padding-bottom: 3px;">{!! $result !!}</td>
            @endif
            <!-- end td result -->

            <td id="content" style="text-align: left;">{!! $test->normal_value !!}</td>
            <td id="content" style="text-align: left;">{{ $test->test_unit }}</td>

            <td id="content" style="text-align: left;">
            <?php
                if($test->result_status_label == 'Critical'){
                    echo 'Hasil kritis dilaporkan oleh ' . $test->report_by . ' kepada ' . $test->report_to . ' (' . date('H:i', strtotime($test->report_time)) . ')' . '<br>';
                    echo '<b>' . 'Keterangan : ' . '</b>' . $test->memo_test;
                }else{
                    echo $test->memo_test;
                }
            
            ?>
            </td>

        </tr>

        <?php $row++; ?>
        @endforeach
        </tbody>
    </table>

    <br>
    <table>
        <tbody>
            @if($transaction->is_print_memo != '')
            <tr>
                <td style="text-align: left;">Keterangan :</td>
            </tr>
            <tr>
                <td style="text-align: left;">
                    <?php
                    if ($transaction->memo_result == 'hivpos') {
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/hivpos.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    } else if($transaction->memo_result == 'covid19'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    } else if($transaction->memo_result == 'covid19AGD1'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19AGD1.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'covid19gen'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19gen.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'covid19-nonreact'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19-nonreact.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'covid19-react'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19-react.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'negatif'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/negatif.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'positif'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/positif.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'covid19neg'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19neg.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    }else if($transaction->memo_result == 'covid19pos'){
                        $keterangan = [];
                        if (($open = fopen(storage_path() . "/csv/covid19pos.csv", "r")) !== FALSE) {
                            while (($data = fgetcsv($open, ",")) !== FALSE) {
                                $keterangan[] = $data;
                            }
                            fclose($open);
                        }
                        foreach ($keterangan as $value) {
                            echo $value[0] . "<br>";
                        }
                    } else {
                        echo $transaction->memo_result;
                    }
                    ?>
                </td>
            </tr>
            @endif

        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <td style="text-align: left; font-size: 10px; width: 19%;"> Jam Pengambilan Sample </td>
                <td style="text-align: left; font-size: 10px; width: 20%;"> : <?= date('d/m/Y H:i', strtotime($group_draw_time)); ?></td>
                <td style="text-align: left; width: 25%;"> </td>
                <td style="text-align: left; width: 25%;"> </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 10px; width: 19%;"> Jam Cetak Hasil </td>
                <td style="text-align: left; font-size: 10px; width: 20%;"> : <?= date('d/m/Y H:i', strtotime($print_time)); ?></td>
                <td style="text-align: left; width: 25%;"> </td>
                <td style="text-align: left; width: 25%;"> </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 10px; width: 19%;"> Printed by </td>
                <td style="text-align: left; font-size: 10px; width: 20%;"> : {{ Auth::user()->name }}</td>
                <td style="text-align: left; width: 25%;"> </td>
                <td style="text-align: left; width: 25%;"> </td>
            </tr>
            <tr>
                <td style="text-align: left; font-size: 12px; width: 25%;"> <i>( * ) Abnormal, ( ** ) Nilai Kritis</i> </td>
                <td style="text-align: left; font-size: 12px; width: 25%;"> </td>
                <td style="text-align: left; width: 25%;"> </td>
                <td style="text-align: left; width: 25%;"> </td>
            </tr>
        </tbody>
    </table>

    <table style="position: fixed;">
        <tbody>
            <tr>
                <td width="80%" style="text-align: right;"> </td>
                <td width="20%" style="text-align: center;">Magelang, <?= date('d/m/Y', strtotime($print_time)); ?> </td>
            </tr>
            <tr>
                <td width="80%" style="text-align: right;"> </td>
                <td width="20%" style="text-align: center;">Validator,<br><br><br><br></td>
            </tr>
            <tr>
                <td width="80%" style="text-align: right;"> </td>
                <td width="20%" style="text-align: center;">{{ Auth::user()->name }}</td>
            </tr>

        </tbody>
    </table>

</body>

</html>