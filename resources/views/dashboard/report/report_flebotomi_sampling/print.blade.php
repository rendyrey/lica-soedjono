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
        <h3>LAPORAN JUMLAH PASIEN BERDASARKAN PENERIMAAN SAMPLE DAN FLEBOTOMI</h3>
        <table>
            <tr>
                <td width="15%">Nama Institusi</td>
                <td width="1%"> : </td>
                <td width="84%"> Laboratorium Klinik RST Soedjono Magelang</td>
            </tr>
            <tr>
                <td width="15%">Periode Tanggal</td>
                <td width="1%"> : </td>
                <td width="84%"> {{ $startDate }} - {{ $endDate }} </td>
            </tr>
            <tr>
                <td>Spesimen</td>
                <td> : </td>
                <td> {{ $specimen }} </td>
            </tr>
        </table>

        <br>

        <table id="tb_result" style="border: 1px solid black; margin: 5px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="border-bottom" style="text-align: center;">No</th>
                    <th class="border-bottom" style="text-align: center;">Tanggal</th>
                    <th class="border-bottom" style="text-align: center;">Pasien</th>
                    <th class="border-bottom" style="text-align: center;">Rekam Medik</th>
                    <th class="border-bottom" style="text-align: center;">Umur</th>
                    <th class="border-bottom" style="text-align: center;">Jenis Kelamin</th>
                    <th class="border-bottom" style="text-align: center;">Nama Dokter</th>
                    <th class="border-bottom" style="text-align: center;">Nama Ruangan</th>
                    <th class="border-bottom" style="text-align: center;">Sampling Oleh</th>
                    <th class="border-bottom" style="text-align: center;">Waktu Draw</th>
                </tr>
            </thead>
            <tbody>
                @php
                $index = 1;
                @endphp
                @foreach($samplingData as $data)
                <tr>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $index }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y', strtotime($data->created_time)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_medrec }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">
                        <?php
                        $birthDate = new DateTime($data->patient_birthdate);
                        $today = new DateTime("today");
                        $y = $today->diff($birthDate)->y;
                        $m = $today->diff($birthDate)->m;
                        $d = $today->diff($birthDate)->d;
                        echo $y . 'Thn/' . $m . 'Bln/' . $d . 'Hr';
                        ?>
                    </td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">
                        <?php
                        if ($data->patient_gender == 'M') {
                            echo 'Laki-laki';
                        } else {
                            echo 'Perempuan';
                        }
                        ?>
                    </td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->doctor_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->room_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->draw_by_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y H:i:s', strtotime($data->draw_time)) }}</td>
                </tr>
                @php
                $index++;
                @endphp
                @endforeach
            </tbody>
        </table>
</body>

</html>