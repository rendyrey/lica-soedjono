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
    <h3>LAPORAN TURN AROUND TIME BY GROUP</h3>
    <table>

        <tr>
            <td>Nama Institusi</td>
            <td> : </td>
            <td> Laboratorium Klinik RST Soedjono Magelang</td>
        </tr>
        <tr>
            <td width="15%">Periode Tanggal</td>
            <td width="1%"> : </td>
            <td width="84%"> {{ $startDate }} - {{ $endDate }} </td>
        </tr>
    </table>

    <br>

    @foreach($groupData as $group_data)
    <h4>{{ $group_data->group_name }}</h4>
  
    <table id="tb_result" style="border: 1px solid black; margin: 5px; border-collapse: collapse;">
        <thead>
            <tr>
                <th class="border-bottom" style="text-align: center;">No</th>
                <th class="border-bottom" style="text-align: center;">Tanggal</th>
                <th class="border-bottom" style="text-align: center;">No Lab</th>
                <th class="border-bottom" style="text-align: center;">Pendaftaran</th>
                <th class="border-bottom" style="text-align: center;">Analitik</th>
                <th class="border-bottom" style="text-align: center;">Validasi</th>
                <th class="border-bottom" style="text-align: center;">Cetak</th>
                <th class="border-bottom" style="text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $index = 1;
            $temp_totals = 0;
            $count_tat_dibawah_target = 0;
            $count_tat_diatas_target = 0;
            $tat_in_seconds = 0;
            $target_tat_in_seconds = 140 * 60;
            foreach ($tatData as $data) {
                if($data->group_id == $group_data->group_id){

                $checkin_time = \Carbon\Carbon::parse($data->checkin_time);
                $analytic = \Carbon\Carbon::parse($data->analytic_time);
                $validate = \Carbon\Carbon::parse($data->validate_time);
                $post = \Carbon\Carbon::parse($data->post_time);

                // analytic time
                $analytics = strtotime($analytic) - strtotime($checkin_time);
                $analytic_time = gmdate('H:i:s', $analytics);
                // validate time
                $validates = strtotime($validate) - strtotime($checkin_time);
                $validate_time = gmdate('H:i:s', $validates);
                // post time
                $posts = strtotime($post) - strtotime($checkin_time);
                $post_time = gmdate('H:i:s', $posts);

                // anal val
                $anal_val = $analytics + $validates;
                $total_raw = $posts + $anal_val;

                $total = gmdate('H:i:s', $total_raw);

                $tat_in_seconds = $total_raw;

                if ($tat_in_seconds <= $target_tat_in_seconds) {
                    $count_tat_dibawah_target++;
                } else {
                    $count_tat_diatas_target++;
                }

            ?>
                <tr>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $index }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y', strtotime($data->created_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->no_lab }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y H:i:s', strtotime($checkin_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">+ {{ $analytic_time }} </td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">+ {{ $validate_time }} </td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">+ {{ $post_time }} </td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"> {{ $total }} </td>
                </tr>
            <?php
                $index++;
                $temp_totals += $total_raw;
            }
            }
            ?>

        </tbody>
    </table>

    @php
    $average = $temp_totals / ($index - 1);
    $average_time = gmdate('H:i:s', $average);

    $sum = $temp_totals;
    $sum_time = gmdate('H:i:s', $sum);

    $jam = floor($sum / (60 * 60));
    $menit = $sum - ( $jam * (60 * 60) );
    $detik = $sum % 60;

    @endphp

    <table>
        <tr>
            <td width="20%" style="font-weight:bold">Rata-rata Total</td>
            <td width="2%" style="font-weight:bold">:</td>
            <td width="20" style="font-weight:bold">{{ $average_time }}</td>
            <td width="20%" style="font-weight:bold">
                <= 140 Menit </td>
            <td width="2%" style="font-weight:bold">:</td>
            <td width="20%" style="font-weight:bold">{{ $count_tat_dibawah_target }}</td>
        </tr>
        <tr>
            <td width="20%" style="font-weight:bold">Jumlah Kumulatif</td>
            <td width="2%" style="font-weight:bold">:</td>
            <td width="20" style="font-weight:bold">{{{ $jam }}} Jam {{ floor( $menit / 60 ) }} Menit {{ $detik }} Detik </td>
            <td style="font-weight:bold"> > 140 Menit </td>
            <td style="font-weight:bold">:</td>
            <td style="font-weight:bold">{{ $count_tat_diatas_target }}</td>
        </tr>
    </table>

    <br>
    
    @endforeach
</body>

</html>