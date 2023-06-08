<div class="modal fade" id="test-description-editor">
  <div class="modal-dialog modal-md">
      <div class="modal-content">
          <div class="modal-body text-center">
            <h2>Test Result</h2>
            <div id="test-description"></div>
            <form id="test-description-report-form">
              {{ Form::textarea('result_description', '', ['class' => 'form-control result_description', 'id' => 'result_description'])}}                    
              <div class="mb-2 mt-8 text-right" style="text-align: right;">
                {{ Form::button('Cancel', ['class' => 'btn btn-light-danger', 'id' => 'cancel-modal-description-btn']) }}
                {{ Form::button('Submit', ['class' => 'btn btn-light-success', 'id' => 'submit-modal-description-btn']) }}
              </div>
            </form>
          </div>
      </div>
  </div>
</div>
<script src="{{asset('metronic_assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js')}}"></script>
<script>
  
</script>
