"use strict";
var masterData = 'insurance'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var responsiveButtonIndexColumn = 5;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name' },
    { data: 'discount' },
    { data: 'general_code' },
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
    $("#modal_form_horizontal input[name='discount']").val(data.discount);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    discount: {
        required: true,
        digits: true,
        min: 1,
        max: 100
    },
    general_code: {
        required: true,
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
