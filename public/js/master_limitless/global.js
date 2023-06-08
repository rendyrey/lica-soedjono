
/**
 * set base value to '/' if you are using web server and proper DNS
 * set base value to '/all-new-lica/public' if you just use it from the directory path
 */
var baseUrl = function(url) {
    return base + url;
}

var jGrowlSuccess = function (message) {
    $.jGrowl(message, {
        header: 'Well done!',
        theme: 'bg-success'
    });
}

var jGrowlError = function (message = 'Something error happen on our side. Data unsuccessfully saved') {
    $.jGrowl(message, {
        header: 'Oh snap!',
        theme: 'bg-danger'
    });
}

var createData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button
    
    // it is needed because CKEditor need double submit to work if using jquery post
    // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
    if (typeof CKEDITOR != 'undefined') {
        for ( instance in CKEDITOR.instances ){
            CKEDITOR.instances[instance].updateElement();
        }
    }

    let formData = $("#form-create").serialize();
    let theForm = $("#form-create");
    $.ajax({
        url: baseUrl('master/'+masterData+'/create'),
        method: 'POST',
        data: formData,
        success: function(res) {
            jGrowlSuccess(res.message);
            
            DatatableDataSources.refreshTable();

            $("#submit-btn").prop('disabled', false); // re-enable submit button
            theForm.trigger('reset'); // reset form after successfully create data
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
            $(".select2").val(null).trigger("change"); // unselect all the select form
            $("#form-create input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $("#form-create textarea").val('');
        },
        error: function (request, status, error) {
            jGrowlError();

            $("#submit-btn").prop('disabled', false); // re-enable button
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
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
            DatatableDataSources.refreshTable();
            jGrowlSuccess(res.message);
        },
        error: function(res) {
            jGrowlError();
        }
    });
}
    // Defaults
var swalInit = swal.mixin({
    buttonsStyling: false,
    confirmButtonClass: 'btn btn-primary',
    cancelButtonClass: 'btn btn-light'
});

var deleteData = function (id) {
    swalInit({
        title: 'Are you sure?',
        text: 'You will not be able to recover this data!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(isConfirm){
        
        if(isConfirm.value) {
            $.ajax({
                url: baseUrl('master/'+masterData+'/delete/'+id),
                method: 'DELETE',
                success: function(res) {
                    DatatableDataSources.refreshTable();
                    jGrowlSuccess(res.message);
                },
                error: function(request, status, error){
                    jGrowlError(request.responseJSON.message);
                }
            })
        }
    });
}

var DatatableDataSources = function() {
    var dt;
    // Basic Datatable examples
    var _componentDatatableDataSources = function() {
        if (!$().DataTable) {
            console.warn('Warning - datatables.min.js is not loaded.');
            return;
        }

        // Setting datatable defaults
        $.extend( $.fn.dataTable.defaults, {
            autoWidth: false,
            responsive: true,
            columnDefs: [{ 
                // orderable: false,
                // width: 100,
                // targets: [ 8 ]
            }],
            dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' },
            },
            // infoCallback: function( settings, start, end, max, total, pre ) {
            //     if (total > 1000) {
            //         return 'Showing ' + start + ' to ' + end + ' of (more than 10.000)';
            //     }
            //     // return start +" to "+ end;
            //     return 'Showing ' + start + ' to ' + end + ' of ' + total.toLocaleString('id-ID');
            //   }
        });

        // AJAX sourced data
        dt = $('.datatable-ajax').DataTable({
            // order: [],
            // pagingType: 'simple',
            // info: false,
            responsive: {
                details: {
                    type: 'column',
                    target: -1
                }
            },
            searchDelay: 500,
            processing: true,
            serverSide: true,
            // order: [[1, 'desc']],
            ajax: {
                url: baseUrl('master/datatable/'+masterData+'/'+withModel.toString())
            },
            columns: columnsDataTable,
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    targets: [responsiveButtonIndexColumn]
                }
            ]
        });
    };

    // Select2 for length menu styling
    var _componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        }

        // Initialize
        $('.dataTables_length select').select2({
            minimumResultsForSearch: Infinity,
            dropdownAutoWidth: true,
            width: 'auto'
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentDatatableDataSources();
            _componentSelect2();
        },
        refreshTable: function() {
            dt.ajax.reload();
        }
    }
}();

var DateTimePickers = function() {
    // Pickadate picker
    var _componentPickadate = function() {
        if (!$().pickadate) {
            console.warn('Warning - picker.js and/or picker.date.js is not loaded.');
            return;
        }
        // Dropdown selectors
        $('.pickadate-selectors').pickadate({
            selectYears: true,
            selectMonths: true,
            max: true,
            selectYears: 100,
            format: 'd mmmm yyyy',
            formatSubmit: 'yyyy-mm-dd',
            // container: 'body'
        });
    };


    //
    // Return objects assigned to module
    //

    return {
        init: function() {
            _componentPickadate();
        }
    }
}();

var InputsCheckboxesRadios = function () {
    // Uniform
    var _componentUniform = function() {
        if (!$().uniform) {
            console.warn('Warning - uniform.min.js is not loaded.');
            return;
        }
        // Default initialization
        $('.form-check-input-styled').uniform();
    };

    //
    // Return objects assigned to module
    //

    return {
        initComponents: function() {
            _componentUniform();
        }
    }
}();

var Select2Data = function(theData, searchKey = 'name') {
    // Select2 examples
    var _componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        }

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
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
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

            // steal focus during close - only capture once and stop propogation
            $('select.select2').on('select2:closing', function (e) {
                $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                e.stopPropagation();
                });
            });
        }
    }
};

var Select2Component = function () {
        // Select2 examples
        var _componentSelect2 = function() {
            if (!$().select2) {
                console.warn('Warning - select2.min.js is not loaded.');
                return;
            }
    
            // Initialize
            $('.form-select2').select2({
                minimumResultsForSearch: Infinity,
                allowClear: true        
            });
    
        }
        //
        // Return objects assigned to module
        //
    
        return {
            init: function() {
                _componentSelect2();
    
                // steal focus during close - only capture once and stop propogation
                $('select.select2').on('select2:closing', function (e) {
                    $(e.target).data("select2").$selection.one('focus focusin', function (e) {
                    e.stopPropagation();
                    });
                });
            }
        }
}();

var Select2DataMultipleComponent = function(theData, searchKey = 'name') {
    // Select2 examples
    var _componentSelect2 = function() {
        if (!$().select2) {
            console.warn('Warning - select2.min.js is not loaded.');
            return;
        }

        // Initialize
        $('.select-' + theData + '-multiple').select2({
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
                            return {
                                text: item.name,
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            // escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
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
};

var CKEditor = function(id) {
    // CKEditor
    var _componentCKEditor = function() {
        if (typeof CKEDITOR == 'undefined') {
            console.warn('Warning - ckeditor.js is not loaded.');
            return;
        }

        // CKEDITOR.editorConfig = function (config) {
            
        // }
        

        // Full featured editor
        // ------------------------------

        // Setup
        CKEDITOR.replace(id, {
            height: 100,
            extraPlugins: 'forms',
            toolbarGroups: [
                {
                    'name':'paragrap',
                    'groups':['list']
                },
                {
                    'name':'basicstyles',
                    'group':['basicstyles']
                }
            ]
        });
    };
    //
    // Return objects assigned to module
    //
    _componentCKEditor();
}

// this is for open select2 when pressing tab in keyboard
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});

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
            if ($(this).valid()) {
                createData();
            }
        });

        $("#form-edit").on('submit', function(e) {
            e.preventDefault();
            if ($(this).valid()) {
                // it is needed because CKEditor need double submit to work if using jquery post
                // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
                if (typeof CKEDITOR != 'undefined') {
                    for ( instance in CKEDITOR.instances ){
                        CKEDITOR.instances[instance].updateElement();
                    }
                }
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