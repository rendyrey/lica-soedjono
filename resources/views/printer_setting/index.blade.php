@extends('dashboard.main_layout')

@section('styles')
<link href="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.css')}}" rel="stylesheet" type="text/css"/>
@endsection
@section('content')
<!--begin::Content-->
<div class="docs-content d-flex flex-column flex-column-fluid" id="kt_docs_content">
    <!--begin::Container-->
    <div class="px-5 mx-5" id="kt_docs_content_container">
        <div class="row">
            <div class="col-lg-8">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <h1 class="anchor fw-bolder mb-5">
                            Printer Setting</h1>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class="d-flex flex-stack flex-wrap mb-5">
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
                                        <input type="text" data-kt-docs-table-filter="search-datatable" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search" />
                                    </div>
                                    <!--end::Search-->
                                    <!--begin::Toolbar-->
                                    {{-- <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                                        <!--begin::Add customer-->
                                        <button type="button" class="btn btn-sm btn btn-light-primary btn-hover-rise" data-bs-toggle="tooltip" title="Add new printer">
                                        Add new printer</button>
                                        <!--end::Add customer-->
                                    </div> --}}
                                    <!--end::Toolbar-->
                                    <!--begin::Group actions-->
                                    <div class="d-flex justify-content-end align-items-center d-none" data-kt-docs-table-toolbar="selected">
                                        <div class="fw-bolder me-5">
                                        <span class="me-2" data-kt-docs-table-select="selected_count"></span>Selected</div>
                                        <button type="button" class="btn btn-danger" data-kt-docs-table-select="delete_selected">Selection Action</button>
                                    </div>
                                    <!--end::Group actions-->
                                </div>
                                <!--end::Wrapper-->
                                <!--begin::Datatable-->
                                <table class="table gy-1 align-middle table-striped px-0 datatable-ajax">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                                            <th class="min-w-300px">Name</th>
                                            <th>Connection Target</th>
                                            <th>Printer Name</th>
                                            <th>Width</th>
                                            <th>Length</th>
                                            <th>Status</th>
                                            <th class="text-end min-w-100px">Actions</th>
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

            <div class="col-lg-4">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-2">
                            <!--begin::Heading-->
                            <h2 class="anchor fw-bolder mb-5">
                            Add new printer</h2>
                            {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-create']) !!}
                            <div class="mb-4">
                                <label class="form-label fs-6 required">Name/Label</label>
                                {{ Form::text('name', null, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'name','placeholder' => 'Printer Room 1']) }}
                            </div>
                            <div class="mb-4">
                                <label class="form-label fs-6 required">Connection Target</label>
                                {{ Form::text('printer_client_target', null, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'printer_client_target','placeholder' => 'USER-PC/192.168.100.10']) }}
                            </div>
                            <div class="mb-4">
                                <label class="form-label fs-6 required">Printer Name</label>
                                {{ Form::text('printer_client_name', null, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'printer_client_name','placeholder' => 'zd230-1']) }}
                            </div>
                            <div class="mb-4">
                                <div id="class_price">
                                <!--begin::Form group-->
                                <div class="form-group">
                                    <div data-repeater-list="class_price">
                                        <div data-repeater-item>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <label class="form-label required">width</label>
                                                    {{ Form::number('width', null, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'width','placeholder' => '50mm']) }}

                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">Height</label>
                                                    {{ Form::number('height', null, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'height','placeholder' => '30mm']) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Form group-->
                              </div>
                            </div>
                            <div class="mb-2 mt-8">
                                {{ Form::submit('Add Printer' , ['class' => 'form-control btn btn-light-success']) }}
                            </div>
                            {!! Form::close() !!}
                            <!--end::Input group-->
                        </div>
                    </div>
                </div>

            </div>
        </div>
        
    </div>
    <!--end::Container-->


</div>
<!--end::Content-->



<!-- Horizontal form modal -->
<div id="modal_form_horizontal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update data printer</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    X
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">
            {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-edit', 'method' => 'put']) !!}
            {{ Form::hidden('id') }}
            <div class="mb-4">
                <label class="form-label fs-6 required">Doctor Name</label>
                {{ Form::text('name', null, ['class' => 'form-control form-control-solid form-control-sm']) }}
            </div>
            <div class="mb-8">
                <label class="form-label fs-6">General Code</label>
                {{ Form::text('general_code', null, ['class' => 'form-control form-control-solid form-control-sm']) }}
            </div>
            <div class="mb-2 mt-8">
                {{ Form::submit('Update printer', ['class' => 'form-control btn btn-light-success']) }}
            </div>
            
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->
@endsection

@section('scripts')

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<script src="{{asset('js/printer_setting/index.js')}}"></script>
<script src="{{asset('js/printer_setting/datatable.js')}}"></script>

@endsection