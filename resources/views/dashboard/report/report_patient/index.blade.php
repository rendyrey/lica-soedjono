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
                                    Patient Report
                                </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class="d-flex justify-content-between mb-5">
                                    <div class="col-lg-3">
                                        <button type="button" id="print-report" href="{{url('report/patient-print')}}" class="btn btn-sm btn-light btnPrint">Print Report</button>
                                        <button type="button" id="export-report" class="btn btn-sm btn-light" onclick="tablesToExcel(['datatables'], ['Patient Report'], 'Patient-Report.xls', 'Excel')">Export Excel</button>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="">
                                            {{ Form::select('type_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-type','required'=>true, 'data-control' => 'select2', 'data-placeholder' => 'Select Type','id'=>'type_id']) }}
                                            <!-- <select id="type_id" name="type" required="true" class="form-select form-select-sm form-select-solid select-two select-group" data-control="select2" data-placeholder="Select Type">
                                                <option value=""></option>
                                                <option value="rawat_inap">Rawat Inap</option>
                                                <option value="rawat_jalan">Rawat Jalan</option>
                                                <option value="igd">IGD</option>
                                            </select> -->
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
                                <table class="table gy-1 align-middle table-striped px-0 datatable-ajax" id="datatables">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start" width="5%">No.</th>
                                            <th class="text-start" width="10%">Date</th>
                                            <th class="text-start" width="20%">Patient Name</th>
                                            <th class="text-start" width="10%">Medrec</th>
                                            <th class="text-start" width="10%">Age</th>
                                            <th class="text-start" width="10%">Gender</th>
                                            <th class="text-start" width="10%">Type</th>
                                            <th class="text-start" width="15%">Room Name</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-bold"></tbody>
                                </table>
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
<script src="{{asset('js/report/report_patient/index.js')}}"></script>
<script src="{{asset('js/report/report_patient/datatable.js')}}"></script>
<script src="{{asset('js/js_export/export_excel.js')}}"></script>
@endsection