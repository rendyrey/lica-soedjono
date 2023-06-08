<!-- Horizontal form modal -->
<div id="edit-qc-data-3-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit QC Data (Level 3)</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    X
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">
                <!-- {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-edit-level-1', 'method' => 'put']) !!} -->
                <div class="edit-patient-details-form">
                    <input type="hidden" id="qc_data_id_edit_3" name="qc_data_id_edit_3" class="form-control form-control-solid form-control-sm">
                    <input type="hidden" id="qc_id_edit_3" name="qc_id_edit_3" class="form-control form-control-solid form-control-sm">

                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Date</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::number('day_edit', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <!-- <input type="text" id="day_edit" name="day_edit" class="form-control form-control-solid form-control-sm"> -->
                            <input class="form-control form-control-solid form-control-sm daterange-picker" placeholder="Pick date range" id="date_edit_3" name="date_edit_3" />
                        </div>
                    </div>
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">QC Data</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::text('qc_data_edit', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <input type="text" id="qc_data_edit_3" name="qc_data_edit_3" class="form-control form-control-solid form-control-sm" onfocusout="QCDataonFocusOutEdit3()">
                        </div>
                    </div>
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Position (SD)</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::text('position_edit', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <input type="text" id="position_edit_3" name="position_edit_3" class="form-control form-control-solid form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">QC</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::number('qc_edit', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <input type="number" id="qc_edit_3" name="qc_edit_3" class="form-control form-control-solid form-control-sm">
                        </div>
                    </div>
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Medical Laboratory Technologist</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::text('atlm', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <input type="text" id="atlm_edit_3" name="atlm_edit_3" value="{{ Auth::user()->name }}" class="form-control form-control-solid form-control-sm" readonly>
                        </div>
                    </div>
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Recommendation</label></div>
                        <div class="col-md-9">
                            <!-- {{ Form::text('recommendation_edit', null, ['class' => 'form-control form-control-solid form-control-sm']) }} -->
                            <input type="text" id="recommendation_edit_3" name="recommendation_edit_3" class="form-control form-control-solid form-control-sm">
                        </div>
                    </div>

                    <!-- End Input -->
                </div>
                <div class="mb-2 mt-8">
                    <button type="button" id="button_update_data_3" class="form-control btn btn-light-success" data-qc-id-1="" data-qc-id-2="" data-qc-id-3=""> Update QC Data </button>
                    <!-- {{ Form::submit('Edit QC Data', ['class' => 'form-control btn btn-light-success']) }} -->
                </div>
                <!-- {!! Form::close() !!} -->
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->