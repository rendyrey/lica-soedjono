@extends('dashboard.main_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
<link href="{{asset('metronic_assets/css/style.highcharts.css')}}" rel="stylesheet" type="text/css" />

@endsection
@section('content')
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-5 mx-5" id="kt_docs_content_container">
        <!-- Content 1 -->
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
                                    Quality Control
                                </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::Content1-->
                            <div class="py-5">

                                <h4>Filter</h4>
                                <hr>

                                <!-- FILTER ROW -->
                                <div class="row pt-3">
                                    <div class="col-md-3">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Month</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="month" name="month" data-placeholder="Select Month">
                                                    <option value="">Select Month</option>
                                                    <option value="Januari">Januari</option>
                                                    <option value="Februari">Februari</option>
                                                    <option value="Maret">Maret</option>
                                                    <option value="April">April</option>
                                                    <option value="Mei">Mei</option>
                                                    <option value="Juni">Juni</option>
                                                    <option value="Juli">Juli</option>
                                                    <option value="Agustus">Agustus</option>
                                                    <option value="September">September</option>
                                                    <option value="Oktober">Oktober</option>
                                                    <option value="November">November</option>
                                                    <option value="Desember">Desember</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Year</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="year" name="year" data-placeholder="Select Year">
                                                    <option value="">Select Year</option>
                                                    <option value="2018">2018</option>
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Analyzer</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="analyzer" name="analyzer" data-placeholder="Select Analyzer">
                                                    <option value="">Select Analyzer</option>
                                                    @foreach($analyzer_data as $data)
                                                    <option value="{{ $data->analyzer_id }}"> {{ $data->analyzer }} </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Test Name</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="test" name="test" data-placeholder="Select Test">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-11">
                                    </div>
                                    <div class="col-md-1">
                                        <button type="submit" class="btn btn-md btn-primary" style="float: right" onclick="CheckQC()">Search</button>
                                    </div>
                                </div>
                                <!-- END FILTER ROW -->

                                <h4>QC Level Form</h4>
                                <hr>

                                <!-- LOT NO. ROW -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Lot No.</label>
                                            <div class="col-md-9">
                                                <input type="text" id="no_lot" name="no_lot" class="form-control form-control-solid form-control-sm" required="true">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END LOT NO. ROW -->

                                <!-- LEVEL LABEL ROW -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right; font-weight: bold">Level 1</label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="level[]" value="1" class="form-control form-control-solid form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right; font-weight: bold">Level 2</label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="level[]" value="2" class="form-control form-control-solid form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right; font-weight: bold">Level 3</label>
                                            <div class="col-md-9">
                                                <input type="hidden" name="level[]" value="3" class="form-control form-control-solid form-control-sm">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END LEVEL LABEL ROW -->

                                <!-- STANDARD DEVIATION (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Standard Deviation</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="standard_deviation1" name="standard_deviation1" data-placeholder="Select Standard Deviation">
                                                    <option value="">Select Standard Deviation</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Standard Deviation</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="standard_deviation2" name="standard_deviation2" data-placeholder="Select Standard Deviation">
                                                    <option value="">Select Standard Deviation</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Standard Deviation</label>
                                            <div class="col-md-9">
                                                <select class="form-select form-select-sm form-select-solid select-two" data-control="select2" id="standard_deviation3" name="standard_deviation3" data-placeholder="Select Standard Deviation">
                                                    <option value="">Select Standard Deviation</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END STANDARD DEVIATION (Level 1, 2, 3) -->

                                <!-- CONTROL NAME (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Control Name</label>
                                            <div class="col-md-9">
                                                <input type="text" id="control_name1" name="control_name1" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Control Name</label>
                                            <div class="col-md-9">
                                                <input type="text" id="control_name2" name="control_name2" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Control Name</label>
                                            <div class="col-md-9">
                                                <input type="text" id="control_name3" name="control_name3" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END CONTROL NAME (Level 1, 2, 3) -->

                                <!-- LOW VALUE (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Low Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="low_value1" name="low_value1" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Low Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="low_value2" name="low_value2" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Low Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="low_value3" name="low_value3" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END LOW VALUE (Level 1, 2, 3) -->

                                <!-- HIGH VALUE (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">High Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="high_value1" name="high_value1" onfocusout="onFocusOut1()" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">High Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="high_value2" name="high_value2" onfocusout="onFocusOut2()" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">High Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="high_value3" name="high_value3" onfocusout="onFocusOut3()" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END HIGH VALUE (Level 1, 2, 3) -->

                                <!-- TARGET VALUE (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Target Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="target_value1" name="target_value1" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Target Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="target_value2" name="target_value2" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">Target Value</label>
                                            <div class="col-md-9">
                                                <input type="text" id="target_value3" name="target_value3" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END TARGET VALUE (Level 1, 2, 3) -->

                                <!-- DSP POINT (Level 1, 2, 3) -->
                                <div class="row pt-3">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">1 SD</label>
                                            <div class="col-md-9">
                                                <input type="text" id="deviation1" name="deviation1" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">1 SD</label>
                                            <div class="col-md-9">
                                                <input type="text" id="deviation2" name="deviation2" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right">1 SD</label>
                                            <div class="col-md-9">
                                                <input type="text" id="deviation3" name="deviation3" class="form-control form-control-solid form-control-sm" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END DSP POINT (Level 1, 2, 3) -->

                                <!-- BUTTON ADD (Level 1, 2, 3) -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right"></label>
                                            <div class="col-md-9">
                                                <button type="button" id="add-reference-1" onclick="addReference1()" class="btn btn-sm btn-primary" style="float: right" disabled>Add Reference</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right"></label>
                                            <div class="col-md-9">
                                                <button type="button" id="add-reference-2" onclick="addReference2()" class="btn btn-sm btn-primary" style="float: right" disabled>Add Reference</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="fv-row row mb-4">
                                            <label class="form-label fs-7 col-md-3" style="text-align: right"></label>
                                            <div class="col-md-9">
                                                <button type="button" id="add-reference-3" onclick="addReference3()" class="btn btn-sm btn-primary" style="float: right" disabled>Add Reference</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- END BUTTON ADD (Level 1, 2, 3) -->


                                <div class="row">
                                    <!-- QC TABLE LEFT -->
                                    <div class="col-md-6">

                                        <br>
                                        <h4>Monthly QC Data</h4>

                                        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Level 1</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Level 2</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3">Level 3</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                                                <div class="row">
                                                    <div class="d-flex justify-content-between mb-5">
                                                        <div class="col-lg-6">
                                                            <button type="button" id="add-qc-data-1" class="btn btn-sm btn-light" disabled>Add Data</button>
                                                            <button type="button" id="print-qc-data-1" class="btn btn-sm btn-light" data-qc-id="" disabled>Print Report</button>
                                                            <button type="button" id="export-qc-data-1" class="btn btn-sm btn-light" disabled>Export Excel</button>
                                                        </div>
                                                        <!--begin::Search-->
                                                        <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                                            <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-table-1" data-qc-id-1="" data-qc-id-2="" data-qc-id-3="" disabled />
                                                        </div>
                                                        <!--end::Search-->
                                                    </div>
                                                    <!-- Tabel Level 1 -->
                                                    <table class="table gy-1 align-middle table-striped px-0 fs-6 gy-5 datatable-level-1">
                                                        <thead>
                                                            <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                                                <th class="text-start" width="12%">No.</th>
                                                                <th class="text-start" width="12%">Date</th>
                                                                <th class="text-start" width="12%">QC Data</th>
                                                                <th class="text-start" width="12%">Position (SD)</th>
                                                                <th class="text-start" width="12%">QC</th>
                                                                <th class="text-start" width="16%">Medical Laboratory Technologist</th>
                                                                <th class="text-start" width="12%">Recommendation</th>
                                                                <th class="text-start" width="12%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-bold"></tbody>
                                                    </table>
                                                    <!-- End Tabel Level 1 -->
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                                                <div class="row">
                                                    <div class="d-flex justify-content-between mb-5">
                                                        <div class="col-lg-6">
                                                            <button type="button" id="add-qc-data-2" class="btn btn-sm btn-light" disabled>Add Data</button>
                                                            <button type="button" id="print-qc-data-2" class="btn btn-sm btn-light" data-qc-id="" disabled>Print Report</button>
                                                            <button type="button" id="export-qc-data-2" class="btn btn-sm btn-light" disabled>Export Excel</button>
                                                        </div>
                                                        <!--begin::Search-->
                                                        <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                                            <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-table-2" data-qc-id-1="" data-qc-id-2="" data-qc-id-3="" disabled />
                                                        </div>
                                                        <!--end::Search-->
                                                    </div>
                                                    <table class="table gy-1 align-middle table-striped px-0 fs-6 gy-5 datatable-level-2">
                                                        <thead>
                                                            <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                                                <th class="text-start" width="12%">Date</th>
                                                                <th class="text-start" width="12%">QC Data</th>
                                                                <th class="text-start" width="12%">Position (SD)</th>
                                                                <th class="text-start" width="12%">QC</th>
                                                                <th class="text-start" width="16%">Medical Laboratory Technologist</th>
                                                                <th class="text-start" width="12%">Recommendation</th>
                                                                <th class="text-start" width="12%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-bold"></tbody>
                                                    </table>
                                                    <!-- End Tabel Level 2 -->
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                                                <div class="row">
                                                    <div class="d-flex justify-content-between mb-5">
                                                        <div class="col-lg-6">
                                                            <button type="button" id="add-qc-data-3" class="btn btn-sm btn-light" disabled>Add Data</button>
                                                            <button type="button" id="print-qc-data-3" class="btn btn-sm btn-light" data-qc-id="" disabled>Print Report</button>
                                                            <button type="button" id="export-qc-data-3" class="btn btn-sm btn-light" disabled>Export Excel</button>
                                                        </div>
                                                        <!--begin::Search-->
                                                        <div class="d-flex align-items-center position-relative my-1 mb-2 mb-md-0">
                                                            <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-table-3" data-qc-id-1="" data-qc-id-2="" data-qc-id-3="" disabled />
                                                        </div>
                                                        <!--end::Search-->
                                                    </div>
                                                    <!-- Tabel Level 3 -->
                                                    <table class="table gy-1 align-middle table-striped px-0 fs-6 gy-5 datatable-level-3">
                                                        <thead>
                                                            <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                                                <th class="text-start" width="12%">Date</th>
                                                                <th class="text-start" width="12%">QC Data</th>
                                                                <th class="text-start" width="12%">Position (SD)</th>
                                                                <th class="text-start" width="12%">QC</th>
                                                                <th class="text-start" width="16%">Medical Laboratory Technologist</th>
                                                                <th class="text-start" width="12%">Recommendation</th>
                                                                <th class="text-start" width="12%">Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="fw-bold"></tbody>
                                                    </table>
                                                    <!-- End Tabel Level 3 -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END QC TABLE LEFT -->

                                    <!-- QC GRAPH RIGHT -->
                                    <div class="col-md-6">

                                        <br>
                                        <h4>QC Data Trend</h4>

                                        <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                            <li class="nav-item">
                                                <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_graph_1">All Level</a>
                                            </li>
                                        </ul>

                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade show active" id="kt_tab_graph_1" role="tabpanel">
                                                <!-- <canvas id="graph1" width="600" height="400"></canvas> -->
                                                <figure class="highcharts-figure">
                                                    <div id="graph1"></div>
                                                </figure>
                                            </div>
                                            <div class="tab-pane fade" id="kt_tab_graph_2" role="tabpanel">
                                                <!-- <canvas id="graph1" width="600" height="400"></canvas> -->
                                                <figure class="highcharts-figure">
                                                    <div id="graph2"></div>
                                                </figure>
                                            </div>
                                            <div class="tab-pane fade" id="kt_tab_graph_3" role="tabpanel">

                                            </div>
                                        </div>

                                    </div>
                                    <!-- END QC GRAPH RIGHT -->
                                </div>



                                <br>

                            </div>
                            <!--end::Content1-->
                        </div>
                        <!--end::Section-->
                    </div>
                    <!--end::Card Body-->
                </div>
                <!--end::Card-->
            </div>
        </div>
        <!-- End Content 1 -->

    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@include('dashboard.qc.create-qc-data-1-modal')
@include('dashboard.qc.create-qc-data-2-modal')
@include('dashboard.qc.create-qc-data-3-modal')

@include('dashboard.qc.edit-qc-data-1-modal')
@include('dashboard.qc.edit-qc-data-2-modal')
@include('dashboard.qc.edit-qc-data-3-modal')
@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/qc/index.js')}}"></script>
<script src="{{asset('js/qc/chart.js')}}"></script>
<script src="{{asset('js/qc/highcharts.js')}}"></script>
<script src="{{asset('js/qc/series-label.js')}}"></script>
<script src="{{asset('js/qc/exporting.js')}}"></script>
<script src="{{asset('js/qc/export-data.js')}}"></script>
<script src="{{asset('js/qc/accessibility.js')}}"></script>

<!-- export csv -->
<script src="{{asset('js/js_export/export_excel.js')}}"></script>

@endsection