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
            <div class="col-lg-6">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                                <h1 class="anchor fw-bolder mb-5">
                                    Pre Analytics
                                </h1>
                                <div>
                                    <button type="button" id="refresh-pre-datatable" class="btn btn-light-success" onclick="refreshTable()">
                                        Reload
                                    </button>
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal" data-bs-target="#add-patient-modal">
                                        Add Patient
                                    </button>
                                </div>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class='row mb-5'>
                                    {{-- <div class="col-lg-6">
                                    <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker"/>
                                  </div> --}}
                                </div>
                                <div class="d-flex justify-content-between mb-5">
                                    <div class="col-lg-6">
                                        <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker" />
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
                                        <input type="text" data-kt-docs-table-filter="search-pre-analytics" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Pre-Analytics" />
                                    </div>
                                    <!--end::Search-->
                                    <!--begin::Group actions-->
                                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                                        <div class="fw-bolder me-5">
                                            <span class="me-2" data-kt-docs-table-select="selected_count"></span>Selected
                                        </div>
                                        <button type="button" class="btn btn-danger" data-kt-docs-table-select="delete_selected">Selection Action</button>
                                    </div>
                                    <!--end::Group actions-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Datatable-->
                                <table class="table gy-1 align-middle table-striped px-0 pre-analytics-datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-80px">Date</th>
                                            <th>Transaction ID</th>
                                            <th class="text-start">Lab No</th>
                                            <th class="text-start">Medrec</th>
                                            <th class="text-start">Name</th>
                                            <th class="text-start">Room</th>
                                            <th class="min-w-40px text-lowercase"><i class="bi bi-info-circle"></i></th>
                                            <th class="text-end min-w-50px">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold"></tbody>
                                </table>
                                <!--end::Datatable-->
                            </div>
                            <!--end::CRUD-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Card Body-->
                </div>
                <!--end::Card-->
            </div>

            <div class="col-lg-6">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 py-lg-6 px-lg-6 text-gray-700">
                        <div class="d-flex justify-content-between mb-4">
                            <h1>Patient Details</h1>
                            <button class="btn btn-light-primary btn-sm patient-details-btn d-none" id="edit-patient-details-btn" data-transaction-id="">Edit patient details</button>
                        </div>
                        <div class="separator mb-2" style="border: 1px solid grey;"></div>
                        <!--begin::Section-->
                        <div class="row">
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-100px">Name</th>
                                            <td class="name-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Gender</th>
                                            <td class="gender-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Email</th>
                                            <td class="email-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Phone</th>
                                            <td class="phone-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-100px">Age</th>
                                            <td class="age-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Insurance</th>
                                            <td class="insurance-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Patient type</th>
                                            <td class="type-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Room</th>
                                            <td class="room-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-100px">Medical Record</th>
                                            <td class="medrec-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Physician</th>
                                            <td class="doctor-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="separator mb-4" style="border: 1px solid grey;"></div>
                        <div class="row">
                            <div class="col-6 d-flex justify-content-between">
                                <h4 class="text-dark">Patient test</h4>
                                <button class="btn btn-light-primary btn-sm patient-details-btn d-none" data-transaction-id="" id="edit-test-btn" data-room-class="">Edit test</button>
                            </div>
                            <div class="col-6 d-flex justify-content-between">
                                <h4 class="text-dark">Specimen</h4>
                                <div>
                                    <button class="btn btn-light-primary btn-sm patient-details-btn d-none" id="check-in-btn" data-auto-nolab="false" disabled="disabled" data-transaction-id="" data-has-checked-in="false">Check in</button>
                                    <button class="btn btn-light-info btn-sm draw-btn patient-details-btn d-none" id="draw-all-btn" value="">Draw all</button>
                                    <button class="btn btn-light-info btn-sm draw-btn patient-details-btn d-none" id="undraw-all-btn" value="" data-auto-undraw="">Undraw all</button>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-6">
                                <table class="table table-striped transaction-test-table">
                                    <thead>
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0">Test name</td>
                                            <td class="px-0">Analyzer</td>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-6">
                                <table class="table table-striped transaction-specimen-table">
                                    <thead>
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0">Specimen</td>
                                            <td class="px-0">Vol.</td>
                                            <td class="px-0">Draw</td>
                                            <td class="px-0">Print</td>
                                        </tr>
                                    </thead>
                                </table>

                                <label for="" class="mt-4 form-label">Note</label>
                                <textarea class="form-control mb-4" data-kt-autosize="true" id="transaction-note" data-transaction-id=""></textarea>
                                <div class="d-flex justify-content-between">
                                    <!-- <button class="btn btn-light-primary btn-sm">Print nota</button> -->
                                    <button class="btn btn-light-primary btn-sm" id="btn-print-barcode" type="button">Print barcode</button>
                                    <button class="btn btn-light-success btn-sm" id="go-to-analytics-btn">Go to analytics</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@include('dashboard.pre_analytics.add-patient-modal')
@include('dashboard.pre_analytics.edit-test-modal')
@include('dashboard.pre_analytics.edit-patient-details-modal');
@include('dashboard.pre_analytics.undraw-modal');
@include('dashboard.pre_analytics.checkin-modal');
@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script>
    document.write("<script type='text/javascript' src='{{asset('js/pre_analytics/index.js?v')}}" + Date.now() + "'><\/script>");
</script>
<script src="{{asset('js/pre_analytics/edit-test.js')}}"></script>
@endsection