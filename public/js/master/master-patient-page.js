"use strict";
var masterData = 'patient'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 7;
var columnsDataTable = [
    { data: 'name', },
    { data: 'medrec' },
    { data: 'gender' },
    { data: 'birthdate', render: function(data, type, row) {
            return theFullDate(data);
        } 
    },
    { data: 'phone' },
    { data: 'email' },
    { data: 'address' }
]; 

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='email']").val(data.email);
    $("#modal_form_horizontal input[name='phone']").val(data.phone);
    $("#modal_form_horizontal input[name='medrec']").val(data.medrec);
    datepickerEdit.setDate(data.birthdate);
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

var datepickerEdit;


// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    datepickerEdit = $(".birthdate").flatpickr({
        altInput: true,
        altFormat: 'j F Y',
        dateFormat: 'Y-m-d'
    });
});
