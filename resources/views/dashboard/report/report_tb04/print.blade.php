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
        <h3>LAPORAN JUMLAH PASIEN SEKSI PENUNJANG LABORATORIUM</h3>
        <table>
            <tr>
                <td width="15%">Periode Tanggal</td>
                <td width="1%"> : </td>
                <td width="84%"> {{ $startDate }} - {{ $endDate }} </td>
            </tr>
        </table>

        <br>

        <table id="tb_result" style="border: 1px solid black; margin: 5px; border-collapse: collapse;">
            <thead>
                <tr class="text-center text-gray-600 fw-bolder fs-7 text-uppercase gs-0" style="border: 1px solid;">
                    <th rowspan="3">No.</th>
                    <th rowspan="3">No. Identity</th>
                    <th rowspan="3">Medrec</th>
                    <th rowspan="3">Patient Name</th>
                    <th rowspan="3">NIK</th>
                    <th rowspan="3">Age</th>
                    <th rowspan="3">Gender</th>
                    <th rowspan="3">Address</th>
                    <th rowspan="3">Facility</th>
                    <th rowspan="3">Diagnosis</th>
                    <th rowspan="3">Follow Up</th>
                    <th colspan="8">Micro Test</th>
                    <th rowspan="3">Office Sign</th>
                    <th rowspan="3">TB SO</th>
                    <th rowspan="3">TB RO</th>
                    <th rowspan="3">Note</th>
                    <th rowspan="3">Validation</th>
                </tr>
                <tr class="text-center text-gray-600 fw-bolder fs-7 text-uppercase gs-0" style="border: 1px solid;">
                    <th rowspan="2">Type of Test Sample</th>
                    <th rowspan="2">Test Date Accepted</th>
                    <th rowspan="2">Result Date Reported</th>
                    <th colspan="2">Micro Test Result</th>
                    <th rowspan="2">Test Date Accepted</th>
                    <th rowspan="2">Result Date Reported</th>
                    <th rowspan="2">Result Xpert (TCM)</th>
                </tr>
                <tr class="text-center text-gray-600 fw-bolder fs-7 text-uppercase gs-0" style="border: 1px solid;">
                    <th>1</th>
                    <th>2</th>
                </tr>
            </thead>
            <tbody>
                @php
                $index = 1;
                @endphp
                @foreach ($tb04Data as $data)
                <tr>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $index }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_medrec }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_name }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_nik }}</td>
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
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;">{{ $data->patient_address }}</td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                    <td style="text-align: center; border: 1px solid black; border-collapse: collapse;"></td>
                </tr>
                @php
                $index++;
                @endphp
                @endforeach
            </tbody>
        </table>
</body>

</html>