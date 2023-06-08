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
            <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                                <h1 class="anchor fw-bolder mb-5">
                                    Analytics
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
                                    <div class="d-flex align-items-center position-relative mb-md-0">
                                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="black" />
                                            </svg>
                                        </span>
                                        <!--end::Svg Icon-->
                                        <input type="text" data-kt-docs-table-filter="search-analytics" class="form-control form-control-sm form-control-solid ps-15" placeholder="Search Patient" />
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
                                <table class="table gy-1 align-middle table-striped px-0 analytics-datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-600 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="text-start">Lab No</th>
                                            <th>Medrec</th>
                                            <th class="text-start">Room</th>
                                            <th class="text-start">Name</th>
                                            <th class="text-end min-w-50px"><i class="bi bi-info-circle"></i></th>
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

            <div class="col-lg-8">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 py-lg-6 px-lg-6 text-gray-700">
                        <div class="d-flex justify-content-between mb-4">
                            <h1>Patient Details</h1>
                            <button class="btn btn-light-primary btn-sm patient-details-btn d-none" id="edit-patient-details-btn" data-transaction-id="">Edit patient details</button>
                        </div>
                        <div class="separator mb-4" style="border: 1px solid grey;"></div>
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
                                    </table>
                                </div>
                            </div>
                            <div class="col-4 px-0">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-100px">Physician</th>
                                            <td class="doctor-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                        <tr>
                                            <th class="py-1 w-100px">Note</th>
                                            <td class="note-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="separator mb-4" style="border: 1px solid grey;"></div>

                            <div class="row mt-2">
                                <div class="col-7">
                                    <div class="d-flex flex-row justify-content-between">
                                        <div>
                                            <span>Parameter Data &nbsp;&nbsp;
                                                <span class="note-notif ">
                                                    <i class="bi bi-pencil test-data-action d-none" style="cursor:pointer" data-transaction-id="" data-text="" id="memo-result-btn" data-toggle="tooltip" data-placement="top" title=""></i>
                                                    <span class="badge hidden" id="note-notif">&nbsp;</span>
                                                </span>
                                            </span>
                                            <button class="btn btn-light-primary btn-sm mb-1 ms-5 test-data-action d-none" id="verify-all-btn" data-transaction-id="">Ver All</button>
                                            <button class="btn btn-light-danger btn-sm mb-1 test-data-action d-none" id="unverify-all-btn" data-transaction-id="">Unver All</button>
                                            {{-- <br> --}}
                                            <button class="btn btn-light-primary btn-sm test-data-action d-none" id="validate-all-btn" data-transaction-id="">Val All</button>
                                            <button class="btn btn-light-danger btn-sm mb-1 test-data-action d-none" id="unvalidate-all-btn" data-transaction-id="">Unval All</button>
                                        </div>
                                    </div>
                                    <p class="mt-2">Test List</p>
                                    <table class="table table-striped transaction-test-table">
                                        {{-- <table class="table table-striped transaction-test-table w-100" style="display:block;height:400px;overflow-y:scroll"> --}}
                                        <thead>
                                            {{-- <thead style="position:sticky;top:0;z-index:1;background:#fff;width:100%"> --}}
                                            <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                                <td class="px-0" width="20%">Test</td>
                                                <td class="px-0" width="25%">Result</td>
                                                <td class="px-0" width="20%">Norm</td>
                                                <td class="px-0" width="5%"><i class="bi bi-info-circle"></i></td>
                                                <td class="px-0" width="8%">Verf</td>
                                                <td class="px-0" width="8%">Val</td>
                                                <td class="px-0" width="7%">Del</td>
                                                <td class="px-0" width="7%">DP</td>
                                            </tr>
                                        </thead>
                                        <tbody id="transaction-test-table-body">

                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-5">
                                    <div class="d-flex flex-row">
                                        <?php if ($PRINTTESTPERALLGROUP) { ?>
                                            <a class="btn btn-light-info btn-sm test-data-action d-none mx-5 mb-1" id="confirm-print-all-test" data-transaction-id="">Print Test</a>
                                            <a class="btn btn-light-info btn-sm test-data-action d-none mx-5 mb-1 hidden" id="print-all-test" data-transaction-id="">Print Test</a>
                                        <?php } ?>
                                        <button class="btn btn-light-success btn-sm test-data-action d-none mb-1" id="go-to-post-analytics-btn" data-transaction-id="">Finish Transaction</button>
                                        <!-- <button class="btn btn-light-success btn-sm test-data-action d-none mb-1" id="finish-transaction-btn" data-transaction-id="">Finish Transaction</button> -->
                                    </div>
                                    <p class="mt-2">Patient History</p>
                                    <table class="table table-striped transaction-test-history-table" id="datatable_history">
                                        <thead>
                                            <tr class="px-0 text-uppercase text-gray-600 fw-bolder fs-7">
                                                <td class="px-0">Test</td>
                                                <td class="px-0">Result</td>
                                                <td class="px-0">Test date</td>
                                            </tr>
                                        </thead>
                                        <tbody id="transaction-test-history-table-body">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-5">
                                    <table class="table">
                                        <tr>
                                            <th class="py-1 w-150px">Total Diffcount</th>
                                            <td class="diff-count-detail py-1 text-gray-600 w-auto">-</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
    <div class="modal fade" id="modal_duplo" tabindex="-1" role="dialog" aria-labelledby="modal_duploLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal_duploLabel">Duplo Test</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4">
                            <label class="form-label" id="duplo_test_name">Test Name</label>
                            <input type="hidden" name="duplo_test_id" id="duplo_test_id">
                            <input type="hidden" name="duplo_trans_id" id="duplo_trans_id">
                            <input type="hidden" name="duplo_transaction_test_id" id="duplo_transaction_test_id">
                        </div>
                        <div class="col-8">
                            <select id="select-duplo-analyzer" data-control="select2" data-placeholder="Select analyzer" class="select form-select form-select-sm form-select-solid my-0 me-4"> </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal" id="btn-close-modal-duplo">Close</button>
                    <button type="button" class="btn btn-primary font-weight-bold" id="btn-save-modal-duplo">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Container-->


</div>
<!--end::Content-->

@include('dashboard.analytics.critical-modal')
@include('dashboard.analytics.test-description-editor')
@include('dashboard.analytics.print-test-modal')

@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/analytics/datatable.js')}}"></script>
<script>
    document.write("<script type='text/javascript' src='{{asset('js/analytics/index.js?v')}}" + Date.now() + "'><\/script>");
</script>
@endsection