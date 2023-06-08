<!-- Horizontal form modal -->
<div id="checkin-modal" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Checkin Options</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    X
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">

                <div class="edit-patient-details-form">
                    <div class="fv-row row mb-4">
                        <div class="col-md-2 mt-5"><label class="form-label fs-7">Cito</label></div>
                        <div class="col-md-3 mt-5">

                            <!-- {{ Form::checkbox('cito', true, false, ['class' => 'form-check-input', 'id' => 'cito']) }} -->
                            <input type="checkbox" class="form-check-input" id="citoCheckin" data-transaction-id="">
                            <label class="form-check-label" for="cito">
                                CITO
                            </label>
                            
                        </div>
                    <!-- </div> -->
                    <!-- <div class="fv-row row mb-1"> -->
                        <div class="col-md-6 hidden" id="no-lab-checkin-options">
                            <input type="text" id="no-lab-checkin" class="swal2-input" placeholder="No. Lab" minLength="3" maxLength="3" style="z-index: 999">
                        </div>
                    <!-- </div> -->

                    <!-- End Input -->
                </div>
                <div class="mb-2 mt-8">
                    <button type="button" class="form-control btn btn-light-primary" type="button" id="btn-final-checkin" data-transaction-id="" data-auto-nolab="false">Checkin</button>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->