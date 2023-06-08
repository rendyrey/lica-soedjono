"use strict";
var masterData = 'specimen'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 3;
var columnsDataTable = [
    { data: 'name' },
    { data: 'color' },
    { data: 'code' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal select[name='color']").val(data.color).trigger('change');
    $("#modal_form_horizontal input[name='code']").val(data.code);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    color: {
        required: true,
    },
    code: {
        required: true,
    },
};

// this is for open select2 when pressing tab in keyboard
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});

document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();

    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toastr-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "500",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut",
        "opacity": 1
    };

    // steal focus during close - only capture once and stop propogation
    // $('select.form-select').on('select2:closing', function (e) {
    //     alert("ANJAY");
    //     $(e.target).data("select2").$selection.one('focus focusin', function (e) {
    //         e.stopPropagation();
    //     });
    // });
});