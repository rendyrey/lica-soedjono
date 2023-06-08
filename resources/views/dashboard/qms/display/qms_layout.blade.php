<!DOCTYPE html>
<!--
Author: Keenthemes
Product Name: Metronic - Bootstrap 5 HTML, VueJS, React, Angular & Laravel Admin Dashboard Theme
Purchase: https://1.envato.market/EA4JP
Website: http://www.keenthemes.com
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Dribbble: www.dribbble.com/keenthemes
Like: www.facebook.com/keenthemes
License: For each use you must have a valid license purchased only from above link in order to legally use the theme for your project.
-->
<html lang="en">
<!--begin::Head-->

<head>
    <base href=" {{ url('/') }}">
    <title>LICA - {{ $title }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="shortcut icon" href="{{ url('metronic_assets/media/logos/favicon-lica2.png') }}" />
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700" />
    <!--end::Fonts-->
    <!--begin::Page Vendor Stylesheets(used by this page)-->
    @yield('styles')
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ asset('metronic_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('metronic_assets/css/style.qms.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-custom.css?ver=1.0.2')}}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <style>
        #bottom2 {
            border-radius: 0px 15px 0px 0px;
            /* border: 2px solid #73AD21; */
            background-color: #F5F8FA;
            width: 25vw;
            height: 90px;
            position: absolute;
            text-align: center;
            bottom: 0;
            left: 0;
            z-index: 97;
            color: #000000;
            font-weight: bold;
            padding-top: 8px;
        }
    </style>

</head>
<!--end::Head-->
<!--begin::Body-->
<div id="cover-spin"></div>

<body id="kt_body" class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled" style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
    <!--begin::Main-->
    <!--begin::Root-->
    <div class="d-flex flex-column flex-root">
        <!--begin::Page-->
        <div class="page d-flex flex-row flex-column-fluid">
            <!--begin::Wrapper-->
            <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
                <!-- <center class="pt-1">
                    <img src="{{asset('images/logo-pindad.png')}}" style="width: 100px; height: 100px">
                    <h1>ANTRIAN LABORATORIUM KLINIK</h1>
                    <h1>RUMAH SAKIT UMUM PINDAD BANDUNG</h1>
                </center> -->

                <!--begin::Header-->
                <div style="background-color: #F5F8FA" height="20%" class="header align-items-stretch">
                    <!--begin::Container-->
                    <div class="container-xxl d-flex align-items-stretch justify-content-between">
                        <!-- <center>
                            <img src="{{asset('images/logo-pindad.png')}}" style="width: 100px; height: 100px">
                            <h1>ANTRIAN LABORATORIUM KLINIK</h1>
                            <h1>RUMAH SAKIT UMUM PINDAD BANDUNG</h1>
                        </center> -->
                        <table style="width: 100%;">
                            <tbody>
                                <tr>
                                    <td style="font-size: 20px; text-align: right;" width="30%">
                                        <img src="{{asset('images/logo_rsud_bnw.png')}}" style="width: 85px; height: 85px;">
                                    </td>
                                    <td style="font-size: 24px; text-align:center; font-weight: bold;" width="45%">
                                        ANTRIAN PEMERIKSAAN LABORATORIUM
                                        <br>
                                        RUMAH SAKIT UMUM DAERAH INDRAMAYU
                                    </td>
                                    <td style="font-size: 20p;" width="25%">

                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Header-->


                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Toolbar-->

                    <!--end::Toolbar-->
                    @yield('content')
                </div>
                <!--end::Content-->
                <div id="bottom2">
                    <?php
                    $day = date("D");
                    switch ($day) {
                        case 'Sun':
                            $hari = "Minggu";
                            break;

                        case 'Mon':
                            $hari = "Senin";
                            break;

                        case 'Tue':
                            $hari = "Selasa";
                            break;

                        case 'Wed':
                            $hari = "Rabu";
                            break;

                        case 'Thu':
                            $hari = "Kamis";
                            break;

                        case 'Fri':
                            $hari = "Jumat";
                            break;

                        case 'Sat':
                            $hari = "Sabtu";
                            break;

                        default:
                            $hari = "Tidak di ketahui";
                            break;
                    }
                    $date = date("d/m/Y");
                    ?>
                    <?= $hari . ', ' . $date; ?>
                    <p style="font-size:40px;" id="time">00:00</p>
                </div>
                <!--begin::Footer-->
                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <!--begin::Container-->

                    <marquee>
                        <h5 style="color: #000000">LABORATORIUM RUMAH SAKIT UMUM DAERAH INDRAMAYU</h5>
                    </marquee>

                    <!--end::Container-->
                </div>
                <!--end::Footer-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Page-->
    </div>
    <!--end::Root-->
    <!--begin::Javascript-->
    <script>
        var hostUrl = "{{ url('metronic_assets') }}";
    </script>
    <!--begin::Global Javascript Bundle(used by all pages)-->
    <script src="{{asset('metronic_assets/plugins/global/plugins.bundle.js')}}"></script>
    <script src="{{asset('metronic_assets/js/scripts.bundle.js')}}"></script>
    <script src="{{ asset('js/jquery.printPage.js') }}"></script>
    <!--end::Global Javascript Bundle-->
    <!--begin::Page Vendors Javascript(used by this page)-->

    <script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <!--end::Page Vendors Javascript-->
    <!--begin::Page Custom Javascript(used by this page)-->
    <script>
        var base = "{{ url('/') }}/";
    </script>

    @yield('scripts')
    <!--end::Page Custom Javascript-->
    <script>
        // setInterval(ScrollDiv, 50)
    </script>
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>