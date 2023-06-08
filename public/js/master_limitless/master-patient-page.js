"use strict";
var masterData = 'patient'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var responsiveButtonIndexColumn = 9;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'name', orderable: true },
    { data: 'medrec' },
    { data: 'gender' },
    { data: 'birthdate' },
    { data: 'phone' },
    { data: 'email' },
    { data: 'address' },
    {
        render: function (data, type, row) {
            let editBtn = 
                `<button style="margin:2px;" type="button" class="btn btn-sm btn-primary btn-icon rounded-round" data-popup="tooltip" title="Edit data" data-placement="left" onClick="editData(`+row.id+`)">
                    <i class="icon-pencil5"></i>
                </button>`;
            let deleteBtn = 
                `<button style="margin:2px;" type="button" class="btn btn-sm btn-danger btn-icon rounded-round" data-popup="tooltip" title="Delete data" data-placement="left" onClick="deleteData(`+row.id+`)">
                    <i class="icon-trash"></i>
                </button></div>`;
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
    $("#modal_form_horizontal input[name='email']").val(data.email);
    $("#modal_form_horizontal input[name='phone']").val(data.phone);
    $("#modal_form_horizontal input[name='medrec']").val(data.medrec);
    $("#modal_form_horizontal input[name='birthdate']").val(theFullDate(data.birthdate));
    $("#modal_form_horizontal input[name='birthdate_submit']").val(data.birthdate);
    $("#modal_form_horizontal input[name='gender'][value='"+data.gender+"']").trigger('click');
    $("#modal_form_horizontal textarea[name='address']").val(data.address);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    email: {
        required: true
    },
    phone: {
        digits: true,
        minlength: 6,
        maxlength: 17,
        required: true
    },
    medrec: {
        required: true,
    },
    birthdate: {
        required: true
    },
    gender: {
        required: true
    },
    address: {
        required: true
    }

};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    DateTimePickers.init();
    InputsCheckboxesRadios.initComponents();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
