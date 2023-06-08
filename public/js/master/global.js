
/**
 * set base value to '/' if you are using web server and proper DNS
 * set base value to '/all-new-lica/public' if you just use it from the directory path
 */
var baseUrl = function(url) {
    return base + url;
}

// Datatable Component
var DatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

    // Private functions
    var initDatatable = function () {
        dt = $(".datatable-ajax").DataTable({
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            order: [],
            stateSave: false,
            ajax: {
                url: baseUrl('master/datatable/'+masterData+'/'+withModel.toString())
            },
            columns: columnsDataTable,
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: buttonActionIndex,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    searchable: false,
                    render: function (data, type, row) {
                      let btnactive = '';
                      if (masterData == 'test'){
                        if (data.is_active){
                          btnactive = `<div class="menu-item px-3">
                                          <button class="btn btn-light-warning form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="toogleActive(`+ row.id +`,'0')">
                                          Set Inactive
                                          </button>
                                      </div>`;
                        }else{
                          btnactive = `<div class="menu-item px-3">
                                          <button class="btn btn-light-success form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="toogleActive(`+ row.id + `,'1')">
                                          Set Active
                                          </button>
                                      </div>`;
                        }
                      }
                        return `
                            <a href="#" class="btn btn-light-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                            <i class="fas fa-ellipsis-v"></i>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <button class="btn btn-light-primary form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="editData(`+row.id+`)">
                                    <i class="fas fa-edit"></i>Edit
                                    </button>
                                </div>
                                <!--end::Menu item-->
                                `+ btnactive +`
                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <button class="btn btn-light-danger form-control btn-sm my-1 px-3" data-kt-docs-table-filter="delete_row" onClick="deleteData(`+row.id+`)">
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
            // Add data-filter attribute
            // createdRow: function (row, data, dataIndex) {
            //     $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            // }
        });

        table = dt.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
            KTMenu.createInstances();
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
        const container = document.querySelector('.datatable-ajax');
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
        const container = document.querySelector('.datatable-ajax');
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
            // initToggleToolbar();
            // handleFilterDatatable();
            // handleDeleteRows();
            // handleResetForm();
        },
        refreshTable: function() {
            dt.ajax.reload();
        }
    }
}();

var Select2ServerSide = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData).select2({
            allowClear: true,
            ajax: {
                url: baseUrl('master/select-options/' + theData + '/' + searchKey),
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        query: params.term // search term
                    };
                },
                processResults: function (data, params) {

                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    // params.page = params.page || 1;

                    return {
                        results: $.map(data, function(item){
                            var additionalText = ''
                            var PrefixText = ''
                            if ((theData == 'test') && masterData == 'price') {
                                additionalText = item.classes ? `<i> set for classes (`+item.classes+`)</i>` : '';
                            }
                            if ((theData == 'test')) {
                                PrefixText = item.id+" - "
                                additionalText = " ["+item.initial+"]"
                            }
                            
                            return {
                                text: PrefixText+ item.name + additionalText,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            // minimumInputLength: 0,
            // tags: true, // for create new tags
            language: {
                inputTooShort: function () {
                    return 'Input is too short';
                },
                errorLoading: function () {
                    return `There's error on our side`;
                },
                noResults: function () {
                    return 'There are no result based on your search';
                }
            }
            
        });

    }

    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentSelect2();
        }
    }
}

var createData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create").serialize();
    let theForm = $("#form-create");
    $.ajax({
        url: baseUrl('master/'+masterData+'/create'),
        method: 'POST',
        data: formData,
        success: function(res) {
            toastr.success(res.message, "Create Success!");
            
            DatatablesServerSide.refreshTable();

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
        url: baseUrl('master/'+masterData+'/update'),
        data: formData,
        method: 'PUT',
        success: function(res) {
            $("#modal_form_horizontal").modal('hide');
            theForm.trigger('reset');
            DatatablesServerSide.refreshTable();
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
                url: baseUrl('master/'+masterData+'/delete/'+id),
                method: 'DELETE',
                success: function(res) {
                    DatatablesServerSide.refreshTable();
                    toastr.success(res.message, "Delete Success!");
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}
var toogleActive = function (id,isActive) {
  $.ajax({
    url: baseUrl('master/' + masterData + '/active/' + id + '/' + isActive),
    method: 'PUT',
    success: function (res) {
      DatatablesServerSide.refreshTable();
      toastr.success(res.message, "Change Success!");
    },
    error: function (request, status, error) {
      toastr.error(request.responseJSON.message);
    }
  })
}


var theFullDate = function(date) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    let d = new Date(date);
    let day = d.getDate();
    let month = d.getMonth();
    let year = d.getFullYear();
    let theDate = day + ' ' + monthNames[month] + ' ' + year;

    return theDate;
}


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

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});