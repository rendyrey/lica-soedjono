"use strict";
var masterData = 'group'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 4;
var columnsDataTable = [
    { data: 'name' },
    { data: 'general_code' },
    { data: 'target_tat' },
    { data: 'target_tat_cito' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
    $("#modal_form_horizontal input[name='target_tat']").val(data.target_tat);
    $("#modal_form_horizontal input[name='target_tat_cito']").val(data.target_tat_cito);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    general_code: {
        required: false
    },    
    target_tat: {
        required: false,
        number: true
    },
    target_tat_cito: {
        required: false,
        number: true
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});