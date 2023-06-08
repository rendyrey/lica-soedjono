<!-- Horizontal form modal -->
<div id="print-test-modal" class="modal fade" tabindex="-1">
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
                            <input type="hidden" id="analytic_transaction_id">
                            <select class="form-select form-select-sm form-select-solid select-two" id="verificator-test-pemeriksaan" data-control="select2" data-placeholder="Select Checker">
                                <option value="">Please Select</option>
                                @foreach ($verificators as $verificator)
                                <option value="{{ $verificator->user_id }}">{{ $verificator->name }}</option>
                                @endforeach
                            </select>
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
                    <div class="row">
                        <div class="col-6">
                            <button type="button" class="form-control btn btn-light-primary" type="button" id="print-test-btn" data-transaction-id="" disabled>Print Tes</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="form-control btn btn-light-success" type="button" id="go-to-post-analytics-btn" data-transaction-id="" disabled>Finish Transaction</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->