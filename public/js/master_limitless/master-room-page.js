"use strict";
var masterData = 'room'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var responsiveButtonIndexColumn = 12;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'room' },
    { data: 'room_code' },
    { data: 'class' },
    { data: 'auto_checkin', render: function(data, type, row){
            if (data == true) {
                return `<i class="fas fa-check-circle mr-3 text-success small"></i>`;
            }
            return `<ii class="fas fa-times-circle mr-3 text-danger small"></i>`;
        }
    },
    { data: 'auto_draw', render: function(data, type, row){
            if (data == true) {
                return `<i class="fas fa-check-circle mr-3 text-success small"></i>`;
            }
            return `<ii class="fas fa-times-circle mr-3 text-danger small"></i>`;
        }
    },
    { data: 'type' },
    { data: 'referral_address' },
    { data: 'referral_no_phone' },
    { data: 'referral_email' },
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
            return editBtn + '' + deleteBtn;
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
    $("#modal_form_horizontal form").trigger('reset');
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='room']").val(data.room);
    $("#modal_form_horizontal input[name='room_code']").val(data.room_code);
    $("#modal_form_horizontal input[name='class']").val(data.class);
    $("#modal_form_horizontal input[name='auto_checkin']").attr('checked', data.auto_checkin == true);
    $("#modal_form_horizontal input[name='auto_draw']").attr('checked', data.auto_draw == true);
    $("#modal_form_horizontal select[name='type']").val(data.type).trigger('change');
    $("#modal_form_horizontal textarea[name='referral_address']").val(data.referral_address);
    $("#modal_form_horizontal input[name='referral_no_phone']").val(data.referral_no_phone);
    $("#modal_form_horizontal input[name='referral_email']").val(data.referral_email);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
}

// required for the form validation rules
var rulesFormValidation = {
    room: {
        required: true
    },
    room_code: {
        required: true,
    },
    class: {
        required: true
    },
    type: {
        required: true
    },
    referral_address: {
        required: true
    },
    referral_no_phone: {
        required: true,
        digits: true
    },
    referral_email: {
        required: true,
        email: true
    },
    general_code: {
        required: true
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatableDataSources.init();
    FormValidation.init();
    Select2Data('package').init();
    Select2Component.init();
    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
