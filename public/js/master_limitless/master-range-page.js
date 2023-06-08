"use strict";
var masterData = 'range'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship

// required for the datatable columns
var responsiveButtonIndexColumn = 2;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name' },
    {
        render: function (data, type, row) {
            return '';
        }
    }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
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
    $("#modal_form_horizontal input[name='normal_male']").val(data.normal_male);
    $("#modal_form_horizontal input[name='normal_female']").val(data.normal_female);

}

// required for the form validation rules
var rulesFormValidation = {
    min_age: {
        required: true,
        number: true
    },
    max_age: {
        required: true,
        number: true
    },
    min_male_ref: {
        required: true,
        number: true
    },
    max_male_ref: {
        required: true,
        number: true
    },
    min_crit_male: {
        required: true,
        number: true
    },
    max_crit_male: {
        required: true,
        number: true
    },
    min_female_ref: {
        required: true,
        number: true
    },
    max_female_ref: {
        required: true,
        number: true
    },
    min_crit_female: {
        required: true,
        number: true
    },
    max_crit_female: {
        required: true,
        number: true
    },
    normal_male: {
        number: true
    },
    normal_female: {
        number: true
    }
};

var DataTables = function() {
    var dt;
    var rangeTable;
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
            dom: '<"datatable-header"fl><"datatable-scroll-wrap"t><"datatable-footer"ip>',
            language: {
                search: '<span>Filter:</span> _INPUT_',
                searchPlaceholder: 'Type to filter...',
                lengthMenu: '<span>Show:</span> _MENU_',
                paginate: { 'first': 'First', 'last': 'Last', 'next': $('html').attr('dir') == 'rtl' ? '&larr;' : '&rarr;', 'previous': $('html').attr('dir') == 'rtl' ? '&rarr;' : '&larr;' },
            },
        });

        // AJAX sourced data
        dt = $('.datatable-ajax').DataTable({
            select: {
                style: 'single'
            },
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
                url: baseUrl('master/datatable/test/'+withModel.toString())
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

        rangeTable = $(".datatable-range").DataTable({
            responsive: {
                details: {
                    type: 'column',
                    target: -1
                }
            },
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
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { render: function(data, type, row){
                        return row.min_age + ' - ' + row.max_age;
                    }
                },
                { render: function(data, type, row){
                        return row.min_male_ref + ' - ' + row.max_male_ref;
                    }
                },
                { data: 'min_crit_male' },
                { data: 'max_crit_male' },
                { render: function(data, type, row){
                        return row.min_female_ref + ' - ' + row.max_female_ref;
                    } 
                },
                { data: 'min_crit_female' },
                { data: 'max_crit_female' },
                { data: 'normal_male' },
                { data: 'normal_female' },
                { render: function (data, type, row) {
                        let editBtn = 
                            `<button style="margin:2px;" type="button" class="btn btn-sm btn-primary btn-icon rounded-round" data-popup="tooltip" title="Edit data" data-placement="left" onClick="editRangeData(`+row.id+`)">
                                <i class="icon-pencil5"></i>
                            </button>`;
                        let deleteBtn = 
                            `<button style="margin:2px;" type="button" class="btn btn-sm btn-danger btn-icon rounded-round" data-popup="tooltip" title="Delete data" data-placement="left" onClick="deleteRangeData(`+row.id+`)">
                                <i class="icon-trash"></i>
                            </button>`;
                        return editBtn + '' + deleteBtn;
                    },
                    responsivePriority: 1,
                    orderable: false
                },
                {
                    render: function (data, type, row) {
                        return '';
                    }
                }
            ],
            columnDefs: [
                {
                    className: 'control',
                    orderable: false,
                    targets: [11]
                }
            ]
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
        refreshRangeTable: function() {
            rangeTable.ajax.reload();
        }
    }
}();

var FormRangeValidation = function() {
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
        $('#form-range-create').validate({
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

var createRangeData = function () {
    $("#submit-btn").prop('disabled', true); // disabled button
    
    // it is needed because CKEditor need double submit to work if using jquery post
    // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
    if (typeof CKEDITOR != 'undefined') {
        for ( instance in CKEDITOR.instances ){
            CKEDITOR.instances[instance].updateElement();
        }
    }

    let formData = $("#form-range-create").serialize();
    let theForm = $("#form-range-reate");
    $.ajax({
        url: baseUrl('master/'+masterData+'/create'),
        method: 'POST',
        data: formData,
        success: function(res) {
            jGrowlSuccess(res.message);
            
            DataTables.refreshRangeTable();

            $("#submit-btn").prop('disabled', false); // re-enable submit button
            theForm.trigger('reset'); // reset form after successfully create data
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
            $(".select2").val(null).trigger("change"); // unselect all the select form
            $("#form-range-create input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $("#form-range-create textarea").val('');
        },
        error: function (request, status, error) {
            jGrowlError();

            $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

var deleteRangeData = function (id) {
    swalInit({
        title: 'Are you sure?',
        text: 'You will not be able to recover this data!',
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!'
    }).then(function(isConfirm){
        
        if(isConfirm.value) {
            $.ajax({
                url: baseUrl('master/range/delete/'+id),
                method: 'DELETE',
                success: function(res) {
                    DataTables.refreshRangeTable();
                    jGrowlSuccess(res.message);
                },
                error: function(request, status, error){
                    jGrowlError(request.responseJSON.message);
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

    $.ajax({
        url: baseUrl('master/range/update'),
        data: formData,
        method: 'PUT',
        success: function(res) {
            $("#modal_form_horizontal").modal('hide');
            theForm.trigger('reset');
            $(".uniform-choice span").removeClass('checked'); // uncheck all the radio buttons
            DataTables.refreshRangeTable();
            jGrowlSuccess(res.message);
        },
        error: function(res) {
            jGrowlError();
        }
    });
}

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DataTables.init();
    FormRangeValidation.init();
    Select2Component.init();
    // CKEditor('editor-full-male');
    // CKEditor('editor-full-female');

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $("#form-range-create").on('submit', function(e) {
        e.preventDefault();
        if ($(this).valid()) {
            createRangeData();
        }
    });

    $("#form-range-edit").on('submit', function(e) {
        e.preventDefault();
        if ($(this).valid()) {
            // it is needed because CKEditor need double submit to work if using jquery post
            // REF: https://stackoverflow.com/questions/47756773/why-in-my-ckeditor-need-to-double-click-the-submit-button-to-work
            if (typeof CKEDITOR != 'undefined') {
                for ( instance in CKEDITOR.instances ){
                    CKEDITOR.instances[instance].updateElement();
                }
            }
            updateRangeData();
        }
        // editData(e);
    });
});
