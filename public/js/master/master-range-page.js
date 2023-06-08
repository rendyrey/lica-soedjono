"use strict";
var masterData = 'range'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship

// required for the datatable columns
var responsiveButtonIndexColumn = 10;

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='test_id']").val(data.test_id);
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='min_age']").val(data.min_age);
    $("#modal_form_horizontal input[name='max_age']").val(data.max_age);
    $("#modal_form_horizontal input[name='min_crit_male']").val(data.min_crit_male);
    $("#modal_form_horizontal input[name='max_crit_male']").val(data.max_crit_male);
    $("#modal_form_horizontal input[name='min_crit_female']").val(data.min_crit_female);
    $("#modal_form_horizontal input[name='max_crit_female']").val(data.max_crit_female);
    $("#modal_form_horizontal input[name='min_male_ref']").val(data.min_male_ref);
    $("#modal_form_horizontal input[name='max_male_ref']").val(data.max_male_ref);
    $("#modal_form_horizontal input[name='min_female_ref']").val(data.min_female_ref);
    $("#modal_form_horizontal input[name='max_female_ref']").val(data.max_female_ref);
    // $("#modal_form_horizontal input[name='normal_male']").val(data.normal_male);
    // $("#modal_form_horizontal input[name='normal_female']").val(data.normal_female);
    // debugger;
    if (data.normal_male != null && data.normal_male != '') {
        ckEditorNormalMaleEdit.setData(data.normal_male);
    }
    if (data.normal_female != null && data.normal_female) {
        ckEditorNormalFemaleEdit.setData(data.normal_female);
    }
}

// required for the form validation rules
var rulesFormValidationCreate = {
    min_age: {
        required: true,
        number: true
    },
    max_age: {
        required: true,
        number: true,
        greaterThan: ['#first-input','#max-age']
    },
    min_male_ref: {
        required: true,
        number: true,
    },
    max_male_ref: {
        required: true,
        number: true,
        greaterThan: ['#min-male-ref', '#max-male-ref']
    },
    min_crit_male: {
        number: true
    },
    max_crit_male: {
        number: true
    },
    min_female_ref: {
        required: true,
        number: true
    },
    max_female_ref: {
        required: true,
        number: true,
        greaterThan: ['#min-female-ref', '#max-female-ref']
    },
    min_crit_female: {
        number: true
    },
    max_crit_female: {
        number: true
    }
};
// required for the form validation rules
var rulesFormValidationEdit = {
    min_age: {
        required: true,
        number: true
    },
    max_age: {
        required: true,
        number: true,
        greaterThan: ['#first-input-edit','#max-age-edit']
    },
    min_male_ref: {
        required: true,
        number: true
    },
    max_male_ref: {
        required: true,
        number: true,
        greaterThan: ['#min-male-ref-edit', '#max-male-ref-edit']
    },
    min_crit_male: {
        number: true
    },
    max_crit_male: {
        number: true
    },
    min_female_ref: {
        required: true,
        number: true
    },
    max_female_ref: {
        required: true,
        number: true,
        greaterThan: ['#min-female-ref-edit', '#max-female-ref-edit']
    },
    min_crit_female: {
        number: true
    },
    max_crit_female: {
        number: true
    }
};

var messageFormValidation = {
  min_age: {
    required: 'Min Age is required',
    number: 'Min Age must be number'
  },
  max_age: {
    required: 'Max Age is required',
    number: 'Max Age must be number'
  }
}

// Datatable Component
var DatatableRefRange = function () {
  // Shared variables
  var table;
  var dt;
  var rangeTable;
  var filterPayment;

  // Private functions
  var initDatatable = function () {
      dt = $(".datatable-test-range").DataTable({
          select: {
            style: 'single'
          },
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: true,
          order: [],
          stateSave: false,
          ajax: {
              url: baseUrl('master/ref-range/datatable')
          },
          columns: [
            { data: 'name' }
          ]
      });

      table = dt.$;

      rangeTable = $(".datatable-range").DataTable({
        responsive: true,
        language: {
            emptyTable: "Please click one of the test data, if you did, then the data is empty"
        },
        searchDelay: 500,
        processing: true,
        serverSide: true,
        // order: [[1, 'desc']],
        ajax: {
            url: baseUrl('master/range/0')
        },
        columns: [
            { data: null, render: function(data, type, row){
                    return row.min_age + ' - ' + row.max_age;
                }, searchable: false, orderable: false, 
            },
            { data: null, render: function(data, type, row){
                    return row.min_male_ref + ' - ' + row.max_male_ref;
                }, searchable: false, orderable: false
            },
            { data: 'min_crit_male' },
            { data: 'max_crit_male' },
            { data: null, render: function(data, type, row){
                    return row.min_female_ref + ' - ' + row.max_female_ref;
                }, searchable: false, orderable: false
            },
            { data: 'min_crit_female' },
            { data: 'max_crit_female' },
            { data: 'normal_male' },
            { data: 'normal_female' }
        ],
        columnDefs: [
          {
              responsivePriority: 1,
              targets: 9,
              data: null,
              orderable: false,
              className: 'text-end',
              searchable: false,
              render: function (data, type, row) {
                  return `
                      <a href="#" class="btn btn-light-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                      <i class="fas fa-ellipsis-v"></i>
                      </a>
                      <!--begin::Menu-->
                      <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                          <!--begin::Menu item-->
                          <div class="menu-item px-3">
                              <button class="btn btn-light-primary form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="editRangeData(`+row.id+`)">
                              <i class="fas fa-edit"></i>Edit
                              </button>
                          </div>
                          <!--end::Menu item-->

                          <!--begin::Menu item-->
                          <div class="menu-item px-3">
                              <button class="btn btn-light-danger form-control btn-sm my-1 px-3" data-kt-docs-table-filter="delete_row" onClick="deleteRangeData(`+row.id+`)">
                              <i class="fas fa-trash"></i> Delete
                              </button>
                          </div>
                          <!--end::Menu item-->
                      </div>
                      <!--end::Menu-->
                  `;
              },
          },
      ],
      });

      // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
      dt.on('draw', function () {
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });

      rangeTable.on('draw', function () {
        initToggleToolbar();
        toggleToolbars();
        KTMenu.createInstances();
      });

      dt.on('select', function(e, data, type, indexes) {
        if (type == 'row') {
            const id = data.rows().data()[indexes].id
            rangeTable.ajax.url(baseUrl('master/range/'+id)).load();
            $(".test-id").val(id);
            $(".range-table").removeClass('d-none');
            $(".new-range").removeClass('d-none');
        }
      });
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
      filterSearch.addEventListener('keyup', function (e) {
          dt.search(e.target.value).draw();
      });
  }

  // Init toggle toolbar
  var initToggleToolbar = function () {
      // Toggle selected action toolbar
      // Select all checkboxes
      const container = document.querySelector('.datatable-range');
      const checkboxes = container.querySelectorAll('[type="checkbox"]');
      
      // Toggle delete selected toolbar
      checkboxes.forEach(c => {
          // Checkbox on click event
          c.addEventListener('click', function () {
              setTimeout(function () {
                  // toggleToolbars();
              }, 50);
          });
      });
  }
  

  // Toggle toolbars
  var toggleToolbars = function () {
      // Define variables
      const container = document.querySelector('.datatable-range');
      const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
      const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

      // Select refreshed checkbox DOM elements
      const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

      // Detect checkboxes state & count
      let checkedState = false;
      let count = 0;

      // Count checked boxes
      allCheckboxes.forEach(c => {
          if (c.checked) {
              checkedState = true;
              count++;
          }
      });

      // Toggle toolbars
      if (checkedState) {
          selectedCount.innerHTML = count;
          toolbarSelected.classList.remove('d-none');
      } else {
          toolbarSelected.classList.add('d-none');
      }
  }

  // Public methods
  return {
      init: function () {
          initDatatable();
          handleSearchDatatable();
      },
      refreshTable: function() {
          dt.ajax.reload();
      },
      refreshRangeTable: function() {
        rangeTable.ajax.reload();
      }
  }
}();

var FormRangeValidation = function() {
    // Validation config
    var _componentValidation = function() {
        if (!$().validate) {
            console.warn('Warning - validate.min.js is not loaded.');
            return;
        }

        // Initialize
        $('#form-range-create').validate({
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
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
            rules: rulesFormValidationCreate,
            messages: messageFormValidation
            // submitHandler: function(form, event) {
            // }
        });

        $('#form-range-edit').validate({
            ignore: 'input[type=hidden], .select2-search__field', // ignore hidden fields
            errorClass: 'validation-invalid-label',
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
            rules: rulesFormValidationEdit,
            messages: {
                custom: {
                    required: 'This is a custom error message'
                }
            },
            // submitHandler: function(form, event) {
            // }
        });

        $("#form-range-create").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckEditorNormalMaleCreate != 'undefined') {
              ckEditorNormalMaleCreate.updateSourceElement();
            }
            if (typeof ckEditorNormalFemaleCreate != 'undefined') {
              ckEditorNormalFemaleCreate.updateSourceElement();
            }

            if ($(this).valid()) {
                createRangeData();
            }
        });

        $("#form-range-edit").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckEditorNormalMaleEdit != 'undefined') {
                ckEditorNormalMaleEdit.updateSourceElement();
            }
            if (typeof ckEditorNormalFemaleEdit != 'undefined') {
                ckEditorNormalFemaleEdit.updateSourceElement();
            }
            if ($(this).valid()) {
                // it is needed because CKEditor need double submit to work if using jquery post
                // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
                updateRangeData();
            }
            // editData(e);
        });
    };
    
    return {
        init: function() {
            _componentValidation();
        }
    }
}();

var createRangeData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-range-create").serialize();
    let theForm = $("#form-range-create");
    $.ajax({
        url: baseUrl('master/'+masterData+'/create'),
        method: 'POST',
        data: formData,
        success: function(res) {
            toastr.success(res.message, "Create Success!");
            DatatableRefRange.refreshRangeTable();

            $("#submit-btn").prop('disabled', false); // re-enable submit button
            theForm.trigger('reset'); // reset form after successfully create data
            $(".select2").val(null).trigger("change"); // unselect all the select form
            $("#form-range-create input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $("#form-range-create textarea").val('');
            ckEditorNormalFemaleCreate.setData('');
            ckEditorNormalMaleCreate.setData('');
        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
            $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

var deleteRangeData = function (id) {
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
            url: baseUrl('master/'+masterData+'/delete/'+id),
            method: 'DELETE',
            success: function(res) {
                DatatableRefRange.refreshRangeTable();
                toastr.success(res.message, "Delete Success!");
            },
            error: function(request, status, error){
                toastr.error(request.responseJSON.message);
            }
        })
    }
  });
}

var editRangeData = function (id) {
    $.ajax({
        url: baseUrl('master/range/edit/'+id),
        method: 'GET',
        success: function(res){
            setValueModalEditForm(res);
        },
        error: function(res) {

        }
    })
}

var updateRangeData = function () {
    let theForm = $("#form-range-edit");
    let formData = $("#form-range-edit").serialize();
    console.log(formData);
    $.ajax({
        url: baseUrl('master/range/update'),
        data: formData,
        method: 'PUT',
        success: function(res) {
            $("#modal_form_horizontal").modal('hide');
            theForm.trigger('reset');
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
            DatatableRefRange.refreshRangeTable();
            toastr.success(res.message, "Update Success!");
        },
        error: function(res) {
            toastr.error(request.responseJSON.message);
        }
    });
}

var ckEditorNormalMaleCreate; // this is for CKEditor create form
var ckEditorNormalFemaleCreate; // this is for CKEditor create form
var ckEditorNormalMaleEdit;
var ckEditorNormalFemaleEdit;

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableRefRange.init();
    FormRangeValidation.init();

    jQuery.validator.addMethod('greaterThan', function (value, element, params){
      console.log($(params[1]).val(), "<=", $(params[0]).val())
      if (Number($(params[1]).val()) <= Number($(params[0]).val())) {
        return false;
      }
      return true;
    }, 'Max value should be greater than min value');
    // CKEditor('editor-full-male');
    // CKEditor('editor-full-female');

    ClassicEditor
      .create(document.querySelector('#editor-male'), {
          toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ],
      })
      .then(editor => {
          ckEditorNormalMaleCreate = editor;
          editor.editing.view.change(writer=>{
            writer.setStyle('min-height', '120px', editor.editing.view.document.getRoot());
          });
      })
      .catch(error => {
          console.error(error);
      });

    ClassicEditor
      .create(document.querySelector('#editor-female'), {
          toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ]
      })
      .then(editor => {
          ckEditorNormalFemaleCreate = editor;
          editor.editing.view.change(writer=>{
            writer.setStyle('min-height', '120px', editor.editing.view.document.getRoot());
          });
      })
      .catch(error => {
          console.error(error);
      });

    ClassicEditor
      .create(document.querySelector('#editor-male-edit'), {
          toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ],
      })
      .then(editor => {
          ckEditorNormalMaleEdit = editor;
        //   editor.editing.view.change(writer=>{
        //     writer.setStyle('min-height', '120px', editor.editing.view.document.getRoot());
        //   });
      })
      .catch(error => {
          console.error(error);
      });
    
    ClassicEditor
      .create(document.querySelector('#editor-female-edit'), {
          toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ],
      })
      .then(editor => {
          ckEditorNormalFemaleEdit = editor;
        //   editor.editing.view.change(writer=>{
        //     writer.setStyle('min-height', '120px', editor.editing.view.document.getRoot());
        //   });
      })
      .catch(error => {
          console.error(error);
      });

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
