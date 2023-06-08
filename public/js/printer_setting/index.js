var baseUrl = function(url) {
  return base + url;
}

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    width: {
        required: true
    },
    height: {
        required: true
    },
    printer_client_target : {
        required: true
    },
    printer_client_name : {
        required: true
    }
};

// Form Validation Component
var FormValidation = function() {
    //
    // Setup module components
    //

    // Validation config
    var _componentValidation = function() {
        if (!$().validate) {
            console.warn('Warning - validate.min.js is not loaded.');
            return;
        }

        // Initialize
        $('#form-create').validate({
            ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
            errorClass: 'fv-plugins-message-container invalid-feedback',
            successClass: 'validation-valid-label',
            validClass: 'validation-valid-label',
            highlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            unhighlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            // success: function(label) {
            //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
            // },

            // Different components require proper error label placement
            errorPlacement: function(error, element) {

                // Unstyled checkboxes, radios
                if (element.parents().hasClass('form-check')) {
                    error.appendTo( element.parents('.form-check').parent() );
                }

                // Input with icons and Select2
                else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo( element.parent() );
                }

                // Input group, styled file input
                else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                    error.appendTo( element.parent().parent() );
                }

                // Other elements
                else {
                    error.insertAfter(element);
                }
            },
            rules: rulesFormValidation,
            messages: {
                custom: {
                    required: 'This is a custom error message'
                }
            },
            // submitHandler: function(form, event) {
            // }
        });

        $('#form-edit').validate({
            ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
            errorClass: 'fv-plugins-message-container invalid-feedback',
            successClass: 'validation-valid-label',
            validClass: 'validation-valid-label',
            highlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            unhighlight: function(element, errorClass) {
                $(element).removeClass(errorClass);
            },
            // success: function(label) {
            //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
            // },

            // Different components require proper error label placement
            errorPlacement: function(error, element) {

                // Unstyled checkboxes, radios
                if (element.parents().hasClass('form-check')) {
                    error.appendTo( element.parents('.form-check').parent() );
                }

                // Input with icons and Select2
                else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                    error.appendTo( element.parent() );
                }

                // Input group, styled file input
                else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                    error.appendTo( element.parent().parent() );
                }

                // Other elements
                else {
                    error.insertAfter(element);
                }
            },
            rules: rulesFormValidation,
            messages: {
                custom: {
                    required: 'This is a custom error message'
                }
            },
            // submitHandler: function(form, event) {
            // }
        });

        // Reset form
        // $('#reset').on('click', function() {
        //     validatorCreate.resetForm();
        // });

        $("#form-create").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckeditor != 'undefined') {
                ckeditor.updateSourceElement();
            }
            if ($(this).valid()) {
                createData();
            }
        });

        $("#form-edit").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckeditor_edit != 'undefined') {
                ckeditor_edit.updateSourceElement();
            }
            if ($(this).valid()) {
                // it is needed because CKEditor need double submit to work if using jquery post
                // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
                
                updateData();
            }
            // editData(e);
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentValidation();
        }
    }
}();

var createData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create").serialize();
    let theForm = $("#form-create");
    $.ajax({
        url: baseUrl('printer-setting/create'),
        method: 'POST',
        data: formData,
        success: function(res) {
            toastr.success(res.message, "Create Success!");
            
            Datatable.refreshTable();

            $("#submit-btn").prop('disabled', false); // re-enable submit button
            theForm.trigger('reset'); // reset form after successfully create data
            $(".select-two").val(null).trigger("change"); // unselect all the select form
            $("#form-create input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            ckeditor.setData(''); //remove value ckeditor

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
            $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

var editData = function (id) {
    $.ajax({
        url: baseUrl('master/'+masterData+'/edit/'+id),
        method: 'GET',
        success: function(res){
            setValueModalEditForm(res);
        },
        error: function(res) {

        }
    })
}

var updateData = function () {
    let theForm = $("#form-edit");
    let formData = $("#form-edit").serialize();
    $.ajax({
        url: baseUrl('printer-setting/update'),
        data: formData,
        method: 'PUT',
        success: function(res) {
            $("#modal_form_horizontal").modal('hide');
            theForm.trigger('reset');
            Datatable.refreshTable();
            toastr.success(res.message, "Update Success!");
        },
        error: function(request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    });
}

var deleteData = function (id) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You will not be able to recover this data!',
        // type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            confirmButton: "btn btn-danger",
            cancelButton: 'btn btn-secondary'
        }
    }).then(function(isConfirm){
        if(isConfirm.value) {
            $.ajax({
                url: baseUrl('printer-setting/delete/'+id),
                method: 'DELETE',
                success: function(res) {
                    Datatable.refreshTable();
                    toastr.success(res.message, "Delete Success!");
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}
document.addEventListener('DOMContentLoaded', function () {
    Datatable.init();
    FormValidation.init();

});