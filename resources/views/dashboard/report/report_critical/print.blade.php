<!DOCTYPE html>
<html>

<head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> -->
    <title></title>
    <style type="text/css">
        body {
            font-family: "Arial", sans-serif;
            /*font-family: 'Courier New', monospace;*/
            font-size: 13px;
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
            border: 1px solid;

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
            margin-top: 0px
        }
    </style>
</head>

<body>

    <div>
        <h3>LAPORAN NILAI KRITIS LABORATORIUM</h3>
        <table>
            <tr>
                <td width="15%">Nama Institusi</td>
                <td width="1%"> : </td>
                <td width="84%"> Laboratorium Klinik RST Soedjono Magelang</td>
            </tr>
            <tr>
                <td width="15%">Periode Pemeriksaan</td>
                <td width="1%"> : </td>
                <td width="84%"> {{ $startDate }} - {{ $endDate }} </td>
            </tr>
        </table>

        <br>

        <table id="tb_result" style="border: 1px solid black; margin: 5px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="border-bottom" style="text-align: center;">No</th>
                    <th class="border-bottom" style="text-align: center;">Tanggal</th>
                    <th class="border-bottom" style="text-align: center;">No Lab</th>
                    <th class="border-bottom" style="text-align: center;">No MR</th>
                    <th class="border-bottom" style="text-align: center;">Nama Pasien</th>
                    <th class="border-bottom" style="text-align: center;">Asal Ruangan</th>
                    <th class="border-bottom" style="text-align: center;">Pemeriksaan</th>
                    <th class="border-bottom" style="text-align: center;">Hasil</th>
                    <th class="border-bottom" style="text-align: center;">Nilai Normal</th>
                    <th class="border-bottom" style="text-align: center;">Jam Val</th>
                    <th class="border-bottom" style="text-align: center;">Jam Lapor</th>
                    <th class="border-bottom" style="text-align: center;">TAT Lapor</th>
                </tr>
            </thead>
            <tbody>
                @php
                $index = 1;
                $total = 0;
                $count_tat_dibawah_target = 0;
                $count_tat_diatas_target = 0;
                $tat_in_seconds = 0;
                $target_tat_in_seconds = 30*60;
                @endphp

                @foreach($criticalData as $data)

                @php
                $validate_time = \Carbon\Carbon::parse($data->validate_time);
                $report_time = \Carbon\Carbon::parse($data->report_time);
                $tat = $validate_time->diffInSeconds($report_time);
                $tat_time = gmdate('H:i:s', $tat);

                $tat_in_seconds = $tat;
                @endphp

                <?php
                if ($tat_in_seconds <= $target_tat_in_seconds) {
                    $count_tat_dibawah_target++;
                } else {
                    $count_tat_diatas_target++;
                }
                ?>

                <tr>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $index }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y', strtotime($data->input_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->no_lab }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_medrec }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->room_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->test_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->global_result }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{!! $data->normal_value !!}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y H:i:s', strtotime($data->validate_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y H:i:s', strtotime($data->report_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $tat_time }} </td>
                </tr>
                @php
                $index++;
                $total += $tat;
                @endphp
                @endforeach
            </tbody>
        </table>
        @php
        $average = $total / ($index - 1);
       
        $jam = floor($average / (60 * 60));
        $menit = $average - ( $jam * (60 * 60) );
        $detik = $average % 60;

        $percentage_dibawah_target = $count_tat_dibawah_target / ($index - 1);
        $percentage_dibawah_target = $percentage_dibawah_target * 100;

        $percentage_diatas_target = $count_tat_diatas_target / ($index - 1);
        $percentage_diatas_target = $percentage_diatas_target * 100;
        @endphp

        <table>
            <tr>
                <td width="20%" style="font-weight:bold">Rata-rata TAT Lapor</td>
                <td width="2%" style="font-weight:bold">:</td>
                <td width="20" style="font-weight:bold">{{{ $jam }}} Jam {{ floor( $menit / 60 ) }} Menit {{ $detik }} Detik</td>
            </tr>
            <tr>
                <td width="20%" style="font-weight:bold">Target <= 30 Menit</td>
                <td width="2%" style="font-weight:bold">:</td>
                <td width="20" style="font-weight:bold">{{ $count_tat_dibawah_target }} = {{ round($percentage_dibawah_target) }}%</td>
            </tr>
            <tr>
                <td width="20%" style="font-weight:bold">Target > 30 Menit</td>
                <td width="2%" style="font-weight:bold">:</td>
                <td width="20" style="font-weight:bold">{{ $count_tat_diatas_target }} = {{ round($percentage_diatas_target) }}%</td>
            </tr>
        </table>
</body>

</html>