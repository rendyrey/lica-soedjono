<div class="modal fade" id="critical-modal">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
          <div class="modal-body text-center">
            <i class="bi bi-exclamation-triangle-fill h1" style="font-size:50px"></i>
            <h2>Patient has critical value</h2>
            <p>Please Input Report</p>

            <p>Critical Test:</p>
            <div id="critical-tests"></div>
            <form id="critical-tests-report-form">
              {{Form::hidden('transaction_test_ids', null, [])}}
              {{Form::hidden('transaction_id', null, [])}}
              <div class="mb-4 mt-4">
                <label class="form-label fs-6">Report To</label>
                {{ Form::text('report_to', null, ['class' => 'form-control form-control-solid form-control-sm', 'required' => 'required']) }}
              </div>
              <div class="mb-4">
                <label class="form-label fs-6">Report By</label>
                {{ Form::text('report_by', Auth::user()->name, ['class' => 'form-control form-control-solid form-control-sm', 'id' => 'first-input', 'readonly' => true]) }}
              </div>
              <div class="mb-2 mt-8 text-right" style="text-align: right;">
                {{ Form::button('Cancel', ['class' => 'btn btn-light-danger', 'id' => 'cancel-modal-btn']) }}
                {{ Form::submit('Report', ['class' => 'btn btn-light-success', 'id' => 'report-modal-btn']) }}
              </div>
            </form>
          </div>
      </div>
  </div>
</div>