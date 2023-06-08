@extends('dashboard.viewer.layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
<style>
    .fixTableHead {
        overflow-y: auto;
        height: 300px;
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
                                    Post Analytics
                                </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class="d-flex justify-content-between mb-5">
                                    <div class="col-lg-4">
                                        <input class="form-control form-control-solid form-control-sm" placeholder="Pick date range" id="daterange-picker" />
                                    </div>
                                    <div class="col-lg-4 px-2">
                                        <div class="">
                                            {{ Form::select('group_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-group','required'=>true, 'data-control' => 'select2', 'data-placeholder' => 'Select Group','id'=>'group_id']) }}
                                        </div>
                                    </div>
                                    <!--begin::Search-->
                                    <div class="col-lg-4 px-2">
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
                                <table class="table gy-1 align-middle table-striped px-0 post-analytics-datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start min-w-80px" width="10%">Date</th>
                                            <th class="text-start" width="15%">Lab No</th>
                                            <th class="text-start" width="15%">Medrec</th>
                                            <th class="text-start" width="30%">Name</th>
                                            <th class="text-start" width="20%">Room</th>
                                            <th class="text-lowercase text-left" style="width:5%"><i class="bi bi-info-circle"></i></th>
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
                        </div>
                        <div class="separator mb-2" style="border: 1px solid grey;"></div>
                        <!--begin::Section-->
                        <div class=" row">
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-10px">Name</th>
                                            <td class="name-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">NIK</th>
                                            <td class="nik-detail py-1 text-gray-600">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">Gender</th>
                                            <td class="gender-detail py-1 text-gray-600">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">First Printed</th>
                                            <td class="first-printed-detail py-1 text-gray-600">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1">Medical Record</th>
                                            <td class="medrec-detail py-1 text-gray-600">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">Patient type</th>
                                            <td class="type-detail py-1 text-gray-600">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">Room</th>
                                            <td class="room-detail py-1 text-gray-600">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1">Physician</th>
                                            <td class="doctor-detail py-1 text-gray-600">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1">Note</th>
                                            <td class="note-detail py-1 text-gray-600">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="col-12 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th style="width:25%" class="py-1">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        Memo Result
                                                    </div>
                                                    <div class="col-md-4 form-check form-check-sm form-check-custom form-check-solid">
                                                        <input id="is_memo_print" name="is_memo_print" value="1" class="form-check-input" type="checkbox">
                                            </th>
                                </div>
                            </div>
                            <td class="memo-result py-1 text-gray-600">-</th>
                                </tr>
                                </table>
                        </div>
                    </div>

                    <div class="separator" style="border: 1px solid grey;"></div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                @php
                                $icon = '<i class="bi bi-pencil test-data-action d-none" style="cursor:pointer" data-transaction-id="" data-text="" id="memo-result-btn"></i>';
                                @endphp
                                <p>Parameter Data &nbsp;&nbsp; {!! $icon !!}</p>
                                <div>
                                    <!-- <button class="btn btn-light-primary btn-sm mb-1 test-data-action btnPrintHasil" href="{{url('/printHasilTest/1')}}" type="button" id="btnPrintHasil" data-transaction-id="">Print Hasil Test</button> -->
                                </div>
                            </div>
                            <div class="fixTableHead">
                                <table class="table table-striped transaction-test-table" style="overflow-y:scroll; height: 220px;">
                                    {{-- <table class="table table-striped transaction-test-table w-100" style="display:block;height:400px;overflow-y:scroll"> --}}
                                    <thead>
                                        <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                            <td class="px-0 text-center">Test</td>
                                            <td class="px-0 text-center">Result</td>
                                            <td class="px-0 text-center">Norm</td>
                                            <td class="px-0 text-center"><i class="bi bi-info-circle"></i></td>
                                            <td class="px-0 text-center">Reported to</td>
                                            <td class="px-0 text-center">Report by</td>
                                        </tr>
                                    </thead>
                                    <tbody id="transaction-test-table-body">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <!--end::Container-->

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
<script>
    document.write("<script type='text/javascript' src='{{asset('js/viewer/index.js?v')}}" + Date.now() + "'><\/script>");
</script>
<script src="{{asset('js/viewer/datatable.js')}}"></script>
@endsection