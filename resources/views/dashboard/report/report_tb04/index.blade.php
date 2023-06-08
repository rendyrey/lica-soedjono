@extends('dashboard.main_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<style>
    .fixTableHead {
        overflow-y: auto;
        height: 700px;
    }

    .fixTableHead thead th {
        position: sticky;
        top: 0;
    }

    .table-fixed {
        border-collapse: collapse;
        width: 100%;
    }
</style>
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-5 mx-5" id="kt_docs_content_container">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                                <h1 class="anchor fw-bolder mb-5">
                                    TB04 Report
                                </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class="d-flex justify-content-between mb-5">
                                    <div class="col-lg-3">
                                        <button type="button" id="print-report" href="{{url('report/tb04-print')}}" class="btn btn-sm btn-light btnPrint">Print Report</button>
                                        <button type="button" id="export-report" class="btn btn-sm btn-light">Export Excel</button>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            {{ Form::select('group_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-group','required'=>true, 'data-control' => 'select2', 'data-placeholder' => 'Select Type','id'=>'group_id']) }}
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
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
                                        <input type="text" data-kt-docs-table-filter="search-datatable-patient" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search" />
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
                                <div class="fixTableHead">
                                    <table class="table gy-1 align-middle table-striped px-0 datatable-ajax" style="border: 1px solid; overflow-x:scroll; width: 100%">
                                        <!-- <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start">No.</th>
                                            <th class="text-start">Date</th>
                                            <th class="text-start">No. Identity</th>
                                            <th class="text-start">Medrec</th>
                                            <th class="text-start">Patient Name</th>
                                            <th class="text-start">NIK</th>
                                            <th class="text-start">Age</th>
                                            <th class="text-start">Gender</th>
                                            <th class="text-start">Address</th>
                                            <th class="text-start">Facility Name</th>
                                            <th class="text-start">Diagnosis</th>
                                            <th class="text-start">Follow Up</th>
                                        </tr>
                                    </thead> -->
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
                                        <tbody class="fw-bold"></tbody>
                                    </table>
                                </div>
                                <!--end::Datatable-->
                                <!-- <div class="d-flex justify-content-between">
                                    <p>&nbsp</p>
                                    <div>
                                        <button class="btn btn-light-primary btnPrint" href="{{url('report/test-print')}}" type="button" id="print_pdf">Print PDF</button>
                                    </div>
                                </div> -->
                            </div>
                            <!--end::CRUD-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Card Body-->
                </div>
                <!--end::Card-->
            </div>
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
<script src="{{asset('js/report/report_tb04/index.js')}}"></script>
<script src="{{asset('js/report/report_tb04/datatable.js')}}"></script>
@endsection