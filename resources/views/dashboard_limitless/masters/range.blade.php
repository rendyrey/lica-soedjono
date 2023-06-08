@extends('dashboard.main_layout')

@section('content')
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <!-- card header -->
            <div class="card-header header-elements-sm-inline">
                <h4 class="card-title">Master Test</h4>
            </div>
            <!-- /card header -->

            <!-- card body -->
            {{-- <div class="card-body">
            </div> --}}
            <!-- /card body -->
            
            <table class="table datatable-ajax datatable-responsive">
                <thead>
                    <th>No</th>
                    <th>Test Name</th>
                </thead>
            </table>


        </div>
    </div>
    <div class="col-lg-6 range-table d-none">
        <div class="card">
            <div class="card-header header-elements-sm-inline">
                <h4 class="card-title">Ref. Ranges</h4>
            </div>
            <table class="table datatable-range datatable-responsive">
                <thead>
                    <th>No</th>
                    <th>Age (Days)</th>
                    <th>Male Ref</th>
                    <th>M Crit Below</th>
                    <th>M Crit Above</th>
                    <th>Female Ref</th>
                    <th>F Crit Below</th>
                    <th>F Crit Above</th>
                    <th>Normal Male</th>
                    <th>Normal Female</th>
                    <th>Action</th>
                    <th></th>
                </thead>
            </table>
        </div>
    </div>
    <div class="col-lg-3 new-range d-none" id="master-new">
        <div class="card">
             <!-- card header -->
             <div class="card-header header-elements-sm-inline">
                <h5 class="card-title">Add new Ref. Range</h5>
                <div class="header-elements">
                    <div class="list-icons">
                        <a class="list-icons-item" data-action="collapse"></a>
                    </div>
                </div>
            </div>
            <!-- /card header -->
            <!-- card body -->
            <div class="card-body">
                {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-range-create']) !!}
                {{ Form::hidden('test_id', '', ['class' => 'test-id', 'id' => 'test-id']) }}
                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Age <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-6">
                                {{ Form::text('min_age', null, ['class' => 'form-control', 'id' => 'first-input', 'placeholder' => 'Min Age']) }}
                            </div>
                            <div class="col-6">
                                {{ Form::text('max_age', null, ['class' => 'form-control', 'placeholder' => 'Max Age']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Male Ref. <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-6">
                                {{ Form::text('min_male_ref', null, ['class' => 'form-control', 'placeholder' => 'Min Male Ref']) }}
                            </div>
                            <div class="col-6">
                                {{ Form::text('max_male_ref', null, ['class' => 'form-control', 'placeholder' => 'Max Male Ref']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Male Crit. <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-6">
                                {{ Form::text('min_crit_male', null, ['class' => 'form-control', 'placeholder' => 'Below']) }}
                            </div>
                            <div class="col-6">
                                {{ Form::text('max_crit_male', null, ['class' => 'form-control', 'placeholder' => 'Above']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Female Ref. <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-6">
                                {{ Form::text('min_female_ref', null, ['class' => 'form-control', 'placeholder' => 'Min Female Ref']) }}
                            </div>
                            <div class="col-6">
                                {{ Form::text('max_female_ref', null, ['class' => 'form-control', 'placeholder' => 'Max Female Ref']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Female Crit. <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        <div class="row">
                            <div class="col-6">
                                {{ Form::text('min_crit_female', null, ['class' => 'form-control', 'placeholder' => 'Below']) }}
                            </div>
                            <div class="col-6">
                                {{ Form::text('max_crit_female', null, ['class' => 'form-control', 'placeholder' => 'Above']) }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Normal Male<span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('normal_male', null, ['class' => 'form-control', 'placeholder' => 'Normal Male']) }}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Normal Female<span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::text('normal_female', null, ['class' => 'form-control', 'placeholder' => 'Normal Female']) }}
                    </div>
                </div>

                {{-- <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Normal Male <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::textarea('normal_male', '', ['class' => 'editor-full-male', 'id' => 'editor-full-male'])}}
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-form-label col-lg-3">
                        Normal Female <span class="text-danger">*</span>
                    </label>
                    <div class="col-lg-9">
                        {{ Form::textarea('normal_female', '', ['class' => 'editor-full-female', 'id' => 'editor-full-female'])}}
                    </div>
                </div> --}}

                <div class="row">
                    {{ Form::button('Add ' . $masterData, ['class' => 'form-control btn-success', 'id' => 'submit-btn','type' => 'submit']) }}
                </div>
                
                {!! Form::close() !!}
            </div>
            <!-- /card body -->
        </div>
    </div>
    
</diV>

<!-- Horizontal form modal -->
<div id="modal_form_horizontal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Horizontal form</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-range-edit', 'method' => 'put']) !!}
            {{ Form::hidden('id') }}
            {{ Form::hidden('test_id', '', ['class' => 'test-id'])}}
            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Age <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-6">
                            {{ Form::text('min_age', null, ['class' => 'form-control', 'id' => 'first-input', 'placeholder' => 'Min Age']) }}
                        </div>
                        <div class="col-6">
                            {{ Form::text('max_age', null, ['class' => 'form-control', 'placeholder' => 'Max Age']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Male Ref. <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-6">
                            {{ Form::text('min_male_ref', null, ['class' => 'form-control', 'placeholder' => 'Min Male Ref']) }}
                        </div>
                        <div class="col-6">
                            {{ Form::text('max_male_ref', null, ['class' => 'form-control', 'placeholder' => 'Max Male Ref']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Male Crit. <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-6">
                            {{ Form::text('min_crit_male', null, ['class' => 'form-control', 'placeholder' => 'Below']) }}
                        </div>
                        <div class="col-6">
                            {{ Form::text('max_crit_male', null, ['class' => 'form-control', 'placeholder' => 'Above']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Female Ref. <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-6">
                            {{ Form::text('min_female_ref', null, ['class' => 'form-control', 'placeholder' => 'Min Female Ref']) }}
                        </div>
                        <div class="col-6">
                            {{ Form::text('max_female_ref', null, ['class' => 'form-control', 'placeholder' => 'Max Female Ref']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Female Crit. <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-6">
                            {{ Form::text('min_crit_female', null, ['class' => 'form-control', 'placeholder' => 'Below']) }}
                        </div>
                        <div class="col-6">
                            {{ Form::text('max_crit_female', null, ['class' => 'form-control', 'placeholder' => 'Above']) }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Normal Male<span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::text('normal_male', null, ['class' => 'form-control', 'placeholder' => 'Normal Male']) }}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Normal Female<span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::text('normal_female', null, ['class' => 'form-control', 'placeholder' => 'Normal Female']) }}
                </div>
            </div>

            {{-- <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Normal Male <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::textarea('normal_male', '', ['class' => 'editor-full-male', 'id' => 'editor-full-male'])}}
                </div>
            </div>

            <div class="form-group row">
                <label class="col-form-label col-lg-3">
                    Normal Female <span class="text-danger">*</span>
                </label>
                <div class="col-lg-9">
                    {{ Form::textarea('normal_female', '', ['class' => 'editor-full-female', 'id' => 'editor-full-female'])}}
                </div>
            </div> --}}
            <div class="row">
                {{ Form::button('Update ' . $masterData, ['class' => 'form-control btn-success', 'id' => 'submit-btn','type' => 'submit']) }}
            </div>
            
            {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->
@endsection

@section('additional-script')
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/datatables.min.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/extensions/responsive.min.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/tables/datatables/extensions/select.min.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
{{-- <script src="{{asset('js/vue.js')}}"></script> --}}

<!-- picker date -->
<script src="{{asset('limitless_assets/js/plugins/pickers/pickadate/picker.js')}}"></script>
<script src="{{asset('limitless_assets/js/plugins/pickers/pickadate/picker.date.js')}}"></script>
<!-- /picker date -->

<!-- uniform (for radios button) -->
<script src="{{asset('limitless_assets/js/plugins/forms/styling/uniform.min.js')}}"></script>
<!-- /uniform -->

<!-- Form validation -->
<script src="{{asset('limitless_assets/js/plugins/forms/validation/validate.min.js')}}"></script>
<!-- /Form validation -->

<!-- JGROWL (like toast) -->
<script src="{{asset('limitless_assets/js/plugins/notifications/jgrowl.min.js')}}"></script>
<!-- /JGROWL -->

<!-- sweetAlert -->
<script src="{{asset('limitless_assets/js/plugins/notifications/sweet_alert.min.js')}}"></script>
<!-- /sweetAlert -->

<!-- CKEditor -->
<script src="{{asset('limitless_assets/js/plugins/editors/ckeditor/ckeditor.js')}}"></script>
<!-- /CKEditor -->

<!-- select2 -->
<script src="{{asset('limitless_assets/js/plugins/forms/selects/select2.min.js')}}"></script>
<!-- /select2 -->

<script src="{{asset('js/master/master-'.$masterData.'-page.js')}}"></script>
<script src="{{asset('js/master/global.js')}}"></script>
@endsection