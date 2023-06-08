<!-- Horizontal form modal -->
<div id="print-hasil-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sign Result</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    X
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">

                <div class="edit-patient-details-form">
                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Validator</label></div>
                        <div class="col-md-9">
                            <input type="text" class="form-control form-control-solid form-control-sm" id="validator-hasil-pemeriksaan" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>

                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Checker</label></div>
                        <div class="col-md-9">
                            <input type="text" class="form-control form-control-solid form-control-sm" id="verificator-hasil-pemeriksaan" value="" readonly>
                        </div>
                    </div>

                    <div class="fv-row row mb-4">
                        <div class="col-md-3"><label class="form-label fs-7">Language Option</label></div>
                        <div class="col-md-9">
                            <form class="form">
                                <div class="form-group row">
                                    <div class="col-9 col-form-label">
                                        <div class="radio-inline">
                                            <label class="radio radio-primary">
                                                <input type="radio" name="radios22" value="Bahasa Indonesia" checked="checked" />
                                                <span></span>
                                                Bahasa Indonesia
                                            </label>
                                            <label class="radio radio-primary">
                                                <input type="radio" name="radios22" value="English" />
                                                <span></span>
                                                English
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- End Input -->
                </div>
                <div class="mb-2 mt-8">
                    <button type="button" class="form-control btn btn-light-primary btnPrintHasil" type="button" id="btnPrintHasil" data-transaction-id="">Print Hasil Tes</button>
                    <!-- <button class="btn btn-light-primary btn-sm mb-1 test-data-action btnPrintHasil" href="{{url('/printHasilTest/1')}}" type="button" id="btnPrintHasil" data-transaction-id="">Print Hasil Test</button> -->
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->