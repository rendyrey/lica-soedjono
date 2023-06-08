<!-- Horizontal form modal -->
<div id="modal_form_edit" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update data User</h5>
                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    X
                </div>
                <!--end::Close-->
            </div>
            <div class="modal-body">
                <form class="form form-horizontal form-validate-jquery" id="form-edit" method="PUT">
                    <div class="mb-4">
                        <small><label class="form-label fs-6 required">Name</label></small>
                        <input type="hidden" id="id" name="id" class="form-control form-control-solid form-control-sm">
                        <input type="text" id="edit_name" name="edit_name" class="form-control form-control-solid form-control-sm">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-6 required">Username</label>
                        <input type="text" id="edit_username" name="edit_username" class="form-control form-control-solid form-control-sm">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-6 required">Password</label>
                        <input type="text" id="edit_password" name="edit_password" class="form-control form-control-solid form-control-sm">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fs-6 required">Role</label>
                        <select id="edit_role" class="form-select form-select-sm form-select-solid select-two" data-control="select2" data-placeholder="Select Role">
                            <option></option>
                            <option value="Admin">Admin</option>
                            <option value="Analis">Analyst</option>
                            <option value="Dokter">Doctor</option>
                            <option value="Viewer">Viewer</option>
                        </select>
                    </div>
                    <div class="mb-2 mt-8">
                        <button type="button" id="button_update_data" class="form-control btn btn-light-success">Update Data</button>
                    </div>

                    <!-- {{ Form::select('group_id', [], null, ['class' => 'form-select form-select-sm form-select-solid select-two select-group','required'=>true, 'data-control' => 'select2', 'data-placeholder' => 'Select Group','id'=>'group_id']) }} -->
                    <!-- {!! Form::close() !!} -->
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /horizontal form modal -->