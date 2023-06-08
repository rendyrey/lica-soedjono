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
        <h2>LAPORAN QUALITY CONTROL LEVEL 2</h2>
        <table>
            <tr>
                <td width="20%">Periode Pemeriksaan</td>
                <td width="1%"> : </td>
                <td width="34%"> {{ $qc->month }} {{ $qc->year }} </td>

                <td width="15%">No. Lot</td>
                <td width="1%"> : </td>
                <td width="29%"> {{ $qc_reference->no_lot }} </td>
            </tr>
            <tr>
                <td>Periode Tanggal</td>
                <td> : </td>
                <td> {{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }} </td>

                <td>Nilai bawah</td>
                <td> : </td>
                <td> {{ $qc_reference->low_value }} </td>
            </tr>
            <tr>
                <td>Nama Alat</td>
                <td> : </td>
                <td> {{ $qc->analyzer_name }} </td>

                <td>Nilai Atas</td>
                <td> : </td>
                <td> {{ $qc_reference->high_value }} </td>
            </tr>
            <tr>
                <td>Nama Pemeriksaan</td>
                <td> : </td>
                <td style="text-transform: uppercase;"> {{ $qc->test_name }} </td>

                <td>Nilai Target</td>
                <td> : </td>
                <td> {{ $qc_reference->target_value }} </td>
            </tr>
            <tr>
                <td>Nama Kontrol</td>
                <td> : </td>
                <td> {{ $qc_reference->control_name }} </td>

                <td>Nilai Deviasi</td>
                <td> : </td>
                <td> {{ $qc_reference->deviation }} </td>
            </tr>
        </table>

        <br>

        <table id="tb_result" style="border: 1px solid black; border-collapse: collapse;">
            <thead>
                <tr>
                    <th class="border-bottom" style="text-align: center; width:5%;">Tanggal</th>
                    <th class="border-bottom" style="text-align: center; width:15%;">Nilai QC</th>
                    <th class="border-bottom" style="text-align: center; width:15%;">Posisi QC</th>
                    <th class="border-bottom" style="text-align: center; width:15%;">QC</th>
                    <th class="border-bottom" style="text-align: center; width:35%;">ATLM</th>
                    <th class="border-bottom" style="text-align: center; width:15%;">Rekomendasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($qc_data as $data)
                <tr>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ date('d/m/Y', strtotime($data->date)) }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->data }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->position }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->qc }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->atlm }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->recommendation }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
</body>

</html>