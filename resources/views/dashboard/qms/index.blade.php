@extends('dashboard.main_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-5 mx-5" id="kt_docs_content_container">
        <div class="row">
            <div class="d-flex flex-wrap flex-stack">
                <h1 class="fw-bolder my-2">
                    Patient Queue
                </h1>
                <a href="{{ url('qms/display') }}" class="btn btn-primary"><i class="bi bi-display"></i> Display QMS</a>
            </div>
        </div>
        <div class="row">

            <!--PRE ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-4 pt-4">
                            Yet to start
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-warning"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack">
                            <!--begin::Badge-->
                            <div class="badge badge-info">Pre Analytics</div>
                            <!--end::Badge-->
                        </div>
                        <!--end::Header-->
                        <!-- date range & search datatable -->
                        <div class="d-flex justify-content-between pt-5">
                            <div class="col-md-6">
                                <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker-pre-analytics" />
                            </div>
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" data-kt-docs-table-filter="search-pre-analytics" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Patient" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!-- end::date range & search datatable -->
                        <div class="pt-5"></div>
                        <!--begin::Datatable-->
                        <table class="table gy-1 align-middle table-striped px-0 pre-analytics-datatable-ajax">
                            <thead>
                                <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-start min-w-80px">Date</th>
                                    <th>Transaction ID</th>
                                    <th class="text-start">Lab No</th>
                                    <th class="text-start">Name</th>
                                    <th class="min-w-40px text-lowercase"><i class="bi bi-info-circle"></i></th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold"></tbody>
                        </table>
                        <!--end::Datatable-->
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--END PRE ANALYTICS-->

            <!--ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-4 pt-4">
                            On Progress
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-primary"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack">
                            <!--begin::Badge-->
                            <div class="badge badge-info">Analytics</div>
                            <!--end::Badge-->
                        </div>
                        <!--end::Header-->
                        <!-- date range & search datatable -->
                        <div class="d-flex justify-content-between pt-5">
                            <div class="col-md-6">
                                <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker-analytics" />
                            </div>
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" data-kt-docs-table-filter="search-analytics" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Patient" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!-- end::date range & search datatable -->
                        <!--begin::Datatable-->
                        <div class="pt-5"></div>
                        <table class="table gy-1 align-middle table-striped px-0 analytics-datatable-ajax">
                            <thead>
                                <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-start min-w-80px">Date</th>
                                    <th>Transaction ID</th>
                                    <th class="text-start">Lab No</th>
                                    <th class="text-start">Name</th>
                                    <th class="min-w-40px text-lowercase"><i class="bi bi-info-circle"></i></th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold"></tbody>
                        </table>
                        <!--end::Datatable-->
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--END ANALYTICS-->

            <!--POST ANALYTICS-->
            <div class="col-md-4 col-lg-12 col-xl-4">
                <!--begin::Col header-->
                <div class="mb-9">
                    <div class="d-flex flex-stack">
                        <div class="fw-bolder fs-4 pt-4">
                            Finish
                        </div>
                    </div>
                    <div class="h-3px w-100 bg-success"></div>
                </div>
                <!--end::Col header-->
                <!--begin::Card-->
                <div class="card mb-6 mb-xl-9">
                    <!--begin::Card body-->
                    <div class="card-body">
                        <!--begin::Header-->
                        <div class="d-flex flex-stack">
                            <!--begin::Badge-->
                            <div class="badge badge-info">Post Analytics</div>
                            <!--end::Badge-->
                        </div>
                        <!--end::Header-->
                        <!-- date range & search datatable -->
                        <div class="d-flex justify-content-between pt-5">
                            <div class="col-md-6">
                                <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker-post-analytics" />
                            </div>
                            <!--begin::Search-->
                            <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                <input type="text" data-kt-docs-table-filter="search-post-analytics" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Patient" />
                            </div>
                            <!--end::Search-->
                        </div>
                        <!-- end::date range & search datatable -->
                        <!--begin::Datatable-->
                        <div class="pt-5"></div>
                        <table class="table gy-1 align-middle table-striped px-0 post-analytics-datatable-ajax">
                            <thead>
                                <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="text-start min-w-80px">Date</th>
                                    <th class="text-start">Lab No</th>
                                    <th class="text-start">Name</th>
                                    <th class="min-w-40px text-lowercase"><i class="bi bi-info-circle"></i></th>
                                </tr>
                            </thead>
                            <tbody class="fw-bold"></tbody>
                        </table>
                        <!--end::Datatable-->
                    </div>
                    <!--end::Card body-->
                </div>
            </div>
            <!--END POST ANALYTICS-->

        </div>

    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/qms/index.js')}}"></script>
@endsection