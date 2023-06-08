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
    <link href="{{ asset('metronic_assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app-custom.css?ver=1.0.2')}}" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <style>
        #cover-spin {
            position: fixed;
            width: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: none;
        }

        @-webkit-keyframes spin {
            from {
                -webkit-transform: rotate(0deg);
            }

            to {
                -webkit-transform: rotate(360deg);
            }
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #cover-spin::after {
            content: '';
            display: block;
            position: absolute;
            left: 48%;
            top: 40%;
            width: 40px;
            height: 40px;
            border-style: solid;
            border-color: #ffb582;
            border-top-color: transparent;
            border-width: 4px;
            border-radius: 50%;
            -webkit-animation: spin .8s linear infinite;
            animation: spin .8s linear infinite;
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
                <!--begin::Header-->
                <div id="kt_header" style="" class="header align-items-stretch">
                    <!--begin::Container-->
                    <div class="container-xxl d-flex align-items-stretch justify-content-between">
                        <!--begin::Aside mobile toggle-->
                        <!--end::Aside mobile toggle-->
                        <!--begin::Logo-->
                        <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0 me-lg-15">
                            <a href="{{ url('/') }}">
                                <!-- <img alt="Logo" src="{{ url('metronic_assets/media/logos/logo-1.svg') }}" class="h-20px h-lg-30px" /> -->
                                <img alt="Logo" src="{{ url('metronic_assets/media/logos/logo-lica2.png') }}" style="height: 480x; width: 80px" />
                            </a>
                        </div>
                        <!--end::Logo-->
                        <!--begin::Wrapper-->
                        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                            <!--begin::Navbar-->
                            <div class="d-flex align-items-stretch" id="kt_header_nav">
                                <!--begin::Menu wrapper-->
                                <div class="header-menu align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_header_menu_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_body', lg: '#kt_header_nav'}">
                                    <!--begin::Menu-->
                                    <div class="menu menu-lg-rounded menu-column menu-lg-row menu-state-bg menu-title-gray-700 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-400 fw-bold my-5 my-lg-0 align-items-stretch" id="#kt_header_menu" data-kt-menu="true">

                                        <!-- Empty Navbar -->

                                    </div>
                                    <!--end::Menu-->
                                </div>
                                <!--end::Menu wrapper-->
                            </div>
                            <!--end::Navbar-->
                            <!--begin::Toolbar wrapper-->
                            <div class="d-flex align-items-stretch flex-shrink-0">
                                <!--begin::User menu-->
                                <div class="d-flex align-items-center ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                                    <!--begin::Menu wrapper-->
                                    <div class="cursor-pointer symbol symbol-30px symbol-md-40px" data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        {{ Auth::user()->name }}
                                    </div>
                                    <!--begin::User account menu-->
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-primary fw-bold py-4 fs-6 w-275px" data-kt-menu="true">
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <!--begin::Username-->
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bolder d-flex align-items-center fs-5">{{ Auth::user()->name }}
                                                    </div>
                                                    <a href="#" class="fw-bold text-muted text-hover-primary fs-7">{{ Auth::user()->role }}</a>
                                                </div>
                                                <!--end::Username-->
                                            </div>
                                        </div>
                                        <!--end::Menu item-->
                                        <!--begin::Menu separator-->
                                        <div class="separator my-2"></div>
                                        <!--end::Menu separator-->
                                        <!--begin::Menu item-->
                                        @if(Auth::user()->username == 'admin')
                                        <div class="menu-item px-5">
                                            <a href="{{ url('user-management') }}" class="menu-link px-5">User Management</a>
                                        </div>
                                        @endif
                                        <!--end::Menu item-->
                                        <!--begin::Menu item-->
                                        <div class="menu-item px-5">
                                            <a href="#" class="menu-link px-5" onclick="event.preventDefault();
													document.getElementById('logout-form').submit();">
                                                {{ __('Logout') }}
                                            </a>
                                        </div>
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                        <!--end::Menu item-->
                                    </div>
                                    <!--end::User account menu-->
                                    <!--end::Menu wrapper-->
                                </div>
                                <!--end::User menu-->
                                <!--begin::Header menu toggle-->
                                <div class="d-flex align-items-center d-lg-none ms-2 me-n3" title="Show header menu">
                                    <div class="btn btn-icon btn-active-light-primary w-30px h-30px w-md-40px h-md-40px" id="kt_header_menu_mobile_toggle">
                                        <!--begin::Svg Icon | path: icons/duotune/text/txt001.svg-->
                                        <span class="svg-icon svg-icon-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path d="M13 11H3C2.4 11 2 10.6 2 10V9C2 8.4 2.4 8 3 8H13C13.6 8 14 8.4 14 9V10C14 10.6 13.6 11 13 11ZM22 5V4C22 3.4 21.6 3 21 3H3C2.4 3 2 3.4 2 4V5C2 5.6 2.4 6 3 6H21C21.6 6 22 5.6 22 5Z" fill="black" />
                                                <path opacity="0.3" d="M21 16H3C2.4 16 2 15.6 2 15V14C2 13.4 2.4 13 3 13H21C21.6 13 22 13.4 22 14V15C22 15.6 21.6 16 21 16ZM14 20V19C14 18.4 13.6 18 13 18H3C2.4 18 2 18.4 2 19V20C2 20.6 2.4 21 3 21H13C13.6 21 14 20.6 14 20Z" fill="black" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                    </div>
                                </div>
                                <!--end::Header menu toggle-->
                            </div>
                            <!--end::Toolbar wrapper-->
                        </div>
                        <!--end::Wrapper-->
                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Header-->
                <!--begin::Content-->
                <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                    <!--begin::Toolbar-->
                    <br>
                    <br>
                    <br>
                    <!--end::Toolbar-->
                    @yield('content')
                </div>
                <!--end::Content-->
                <!--begin::Footer-->
                <div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
                    <!--begin::Container-->
                    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between">
                        <!--begin::Copyright-->
                        <div class="text-dark order-2 order-md-1">
                            <span class="text-muted fw-bold me-1">2022©</span>
                            <a href="https://keenthemes.com" target="_blank" class="text-gray-800 text-hover-primary">LICA</a>
                        </div>
                        <!--end::Copyright-->
                        <!--begin::Menu-->

                        <!--end::Menu-->
                    </div>
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
    <script src="{{asset('js/main-layout.js')}}"></script>
    <!--end::Javascript-->
</body>
<!--end::Body-->

</html>