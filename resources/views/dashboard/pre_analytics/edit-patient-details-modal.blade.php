<!-- Horizontal form modal -->
<div id="edit-patient-details-modal" class="modal fade" tabindex="-1">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Edit Patient Details</h5>
              <!--begin::Close-->
              <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                  X
              </div>
              <!--end::Close-->
          </div>
          <div class="modal-body">
          {!! Form::open(['class'=>'form form-horizontal form-validate-jquery', 'id' => 'form-edit-patient-details', 'method' => 'put']) !!}
          {{ Form::hidden('id') }}
          <div class="edit-patient-details-form">
            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Insurance</label></div>
              <div class="col-md-9">
                {{ Form::select('insurance_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-insurance-edit req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select insurance']) }}
              </div>
            </div>

            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Type</label></div>
              <div class="col-md-9">
                {{ Form::select('type', array_replace(Helper::roomType(),['' => '']), null, ['class' => 'form-select form-select-sm form-select-solid select-two req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select type', 'data-hide-search' => 'true', 'id' => 'select-type-edit']) }}
              </div>
            </div>

            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Room</label></div>
              <div class="col-md-9">
                {{ Form::select('room_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-room-edit req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select room', 'id' => 'select-room-edit', 'disabled']) }}
              </div>
            </div>

            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Physican/Doctor</label></div>
              <div class="col-md-9">
                {{ Form::select('doctor_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-doctor-edit req-input', 'data-control' => 'select2', 'data-placeholder' => 'Select doctor']) }}
              </div>
            </div>

            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Cito</label></div>
              <div class="col-md-9">
                <div class="form-check form-check-custom form-check-solid">
                  {{ Form::checkbox('cito', true, false, ['class' => 'form-check-input', 'id' => 'cito-edit']) }}
                  <label class="form-check-label" for="cito-edit">
                    CITO
                  </label>
                </div>
              </div>
            </div>

            <div class="fv-row row mb-4">
              <div class="col-md-3"><label class="form-label fs-7">Diagnosis</label></div>
              <div class="col-md-9">
                {{ Form::textarea('diagnosis', null, ['class' => 'form-control form-control-solid', 'data-kt-autosize' => 'true', 'rows' => 3]) }}
              </div>
            </div>
            <!-- End Input -->
          </div>
          <div class="mb-2 mt-8">
              {{ Form::submit('Update patient details', ['class' => 'form-control btn btn-light-success']) }}
          </div>
          
          {!! Form::close() !!}
          </div>
      </div>
  </div>
</div>
<!-- /horizontal form modal -->