"use strict";
var masterData = 'doctor'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 2;
var columnsDataTable = [
    { data: 'name' },
    { data: 'general_code' }
];

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
