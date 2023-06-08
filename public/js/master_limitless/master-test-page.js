"use strict";
var masterData = 'test'; // required for the url
var withModel = ['group','specimen']; // required for the datatable if the model of the datatable has eager load or relationship

// required for the datatable columns
var responsiveButtonIndexColumn = 12;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name' },
    { data: 'price' },
    { data: 'initial' },
    { data: 'unit' },
    { data: 'volume' },
    { data: 'range_type' },
    { data: 'group', name: 'group.name', render: function(data, type, row){
            return data.name;
        } 
    },
    { data: 'sub_group' },
    { data: 'specimen', name: 'specimen.name', render: function(data, type, row){
            return data.name;
        }
    },
    { data: 'sequence' },
    {
        render: function (data, type, row) {
            let editBtn = 
                `<button style="margin:2px;" type="button" class="btn btn-sm btn-primary btn-icon rounded-round" data-popup="tooltip" title="Edit data" data-placement="left" onClick="editData(`+row.id+`)">
                    <i class="icon-pencil5"></i>
                </button>`;
            let deleteBtn = 
                `<button style="margin:2px;" type="button" class="btn btn-sm btn-danger btn-icon rounded-round" data-popup="tooltip" title="Delete data" data-placement="left" onClick="deleteData(`+row.id+`)">
                    <i class="icon-trash"></i>
                </button>`;
            return editBtn+ '' +deleteBtn;
        },
        responsivePriority: 1,
    },
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
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='initial']").val(data.initial);
    $("#modal_form_horizontal input[name='unit']").val(data.unit);
    $("#modal_form_horizontal input[name='volume']").val(data.volume);
    $("#modal_form_horizontal input[name='range_type']").val(data.range_type);
    $("#modal_form_horizontal select[name='group_id']").html(
        `<option value='`+data.group_id+`'>`+data.group.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sub_group']").val(data.sub_group);
    $("#modal_form_horizontal select[name='specimen_id']").html(
        `<option value='`+data.specimen_id+`'>`+data.specimen.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sequence']").val(data.sequence);
    $("#modal_form_horizontal input[name='price']").val(data.price);
    $("#modal_form_horizontal select[name='range_type']").val(data.range_type).trigger('change');

    if ($("#modal_form_horizontal select[name='range_type']").val() == 'description') {
        $("#normal-notes-edit").removeClass('d-none');
        // $("#modal_form_horizontal textarea[name='normal_notes").val(data.normal_notes);
        // use this instead
        CKEDITOR.instances['editor-full-edit'].setData(data.normal_notes);
    }
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);

}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    initial: {
        required: true,
    },
    volume: {
        required: true,
    },
    group_id: {
        required: true,
    },
    specimen_id: {
        required: true,
    },
    sequence: {
        required: true,
    },
    range_type: {
        required: true,
    },
    general_code: {
        required: true,
    },
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    FormValidation.init();
    Select2Component.init();
    Select2Data('group').init();
    Select2Data('specimen').init();
    CKEditor('editor-full');
    CKEditor('editor-full-edit');

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $(".range-type").on('change', function (e) {
        if ($(this).val() == 'description') {
            $("#normal-notes").removeClass('d-none');
        } else {
            $("#normal-notes").addClass('d-none');
        }
    });
    
    $(".range-type-edit").on('change', function (e) {
        if ($(this).val() == 'description') {
            $("#normal-notes-edit").removeClass('d-none');
        } else {
            $("#normal-notes-edit").addClass('d-none');
        }
    })
});
