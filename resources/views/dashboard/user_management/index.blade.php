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
            <div class="col-lg-8">
                <!--begin::Card-->
                <div class="card card-docs mb-2">
                    <!--begin::Card Body-->
                    <div class="card-body fs-6 py-15 px-5 py-lg-8 px-lg-8 text-gray-700">
                        <!--begin::Section-->
                        <div class="p-0">
                            <!--begin::Heading-->
                            <div class="d-flex justify-content-between">
                                <h1 class="anchor fw-bolder mb-5">
                                    User List
                                </h1>
                            </div>
                            <!--end::Heading-->
                            <!--begin::CRUD-->
                            <div class="py-5">
                                <!--begin::Wrapper-->
                                <div class="d-flex justify-content-between mb-5">
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
                                        <input type="text" data-kt-docs-table-filter="search-user-management" class="form-control form-control-sm form-control-solid w-250px ps-15" placeholder="Search Users" />
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
                                <table class="table gy-1 align-middle table-striped px-0 user-management-datatable-ajax">
                                    <thead>
                                        <tr class="text-start">
                                            <th class="text-start">No.</th>
                                            <th class="text-start">Name</th>
                                            <th class="text-start">Username</th>
                                            <th class="text-start">Password</th>
                                            <th class="text-start">Role</th>
                                            <th class="text-start">Created At</th>
                                            <th class="text-start">Updated At</th>
                                            <th class="text-start">Action</th>
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
                                Add new User
                            </h2>
                            <form class="form form-horizontal form-validate-jquery" id="form-create" method="POST">
                                <div class="mb-4">
                                    <small><label class="form-label fs-6 required">Name</label></small>
                                    <!-- <input type="hidden" id="id" name="id" class="form-control form-control-solid form-control-sm"> -->
                                    <input type="text" id="name" name="name" class="form-control form-control-solid form-control-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fs-6 required">Username</label>
                                    <input type="text" id="username" name="username" class="form-control form-control-solid form-control-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fs-6 required">Password</label>
                                    <input type="text" id="password" name="password" class="form-control form-control-solid form-control-sm">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fs-6 required">Role</label>
                                    <select id="role" class="form-select form-select-sm form-select-solid select-two" data-control="select2" data-placeholder="Select Role">
                                        <option></option>
                                        <option value="Admin">Admin</option>
                                        <option value="Analis">Analyst</option>
                                        <option value="Dokter">Doctor</option>
                                        <option value="Viewer">Viewer</option>
                                    </select>
                                </div>
                                <div class="mb-2 mt-8">
                                    <button type="button" id="button_add_data" class="form-control btn btn-light-success">Add Data</button>
                                </div>

                                <!-- {!! Form::close() !!} -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--end::Container-->


    </div>
    <!--end::Content-->

    @include('dashboard.user_management.edit-user-modal')

    @endsection

    @section('scripts')

    <!-- Form validation -->
    <script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
    <!-- /Form validation -->

    <script src="{{asset('metronic_assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
    <script src="{{asset('js/user_management/index.js')}}"></script>
    @endsection