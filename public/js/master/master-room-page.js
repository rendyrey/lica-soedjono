"use strict";
var masterData = 'room'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 12;
var columnsDataTable = [
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
    { data: 'auto_undraw', render: function(data, type, row){
        if (data == true) {
            return `<i class="fas fa-check-circle mr-3 text-success small"></i>`;
        }
        return `<ii class="fas fa-times-circle mr-3 text-danger small"></i>`;
        }
    },
    { data: 'auto_nolab', render: function(data, type, row){
        if (data == true) {
            return `<i class="fas fa-check-circle mr-3 text-success small"></i>`;
        }
        return `<ii class="fas fa-times-circle mr-3 text-danger small"></i>`;
        }
    },
    { data: 'type', render: function(data, type, row) {
            switch(row.type) {
                case 'rawat_inap':
                    return 'Rawat Inap';
                case 'rawat_jalan':
                    return 'Rawat Jalan';
                case 'igd':
                    return 'IGD';
                case 'rujukan':
                    return 'Rujukan';
                default:
                    return 'Undefined';
            }
        }
    },
    { data: 'referral_address' },
    { data: 'referral_no_phone' },
    { data: 'referral_email' },
    { data: 'general_code' }
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
    $("#modal_form_horizontal input[name='auto_undraw']").attr('checked', data.auto_undraw == true);
    $("#modal_form_horizontal input[name='auto_nolab']").attr('checked', data.auto_nolab == true);
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
        required: true,
        digits: true
    },
    type: {
        required: true
    },
    referral_no_phone: {
        digits: true
    },
    referral_email: {
        email: true
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('package').init();
    
    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $("#select-type").on('change', function (e) {
        if ($(this).val() == 'rujukan') {
            $(".referral-type").removeClass('d-none');
        } else {
            $(".referral-type").addClass('d-none');
        }
    })

    $("#select-type-edit").on('change', function (e) {
        if ($(this).val() == 'rujukan') {
            $(".referral-type-edit").removeClass('d-none');
        } else {
            $(".referral-type-edit").addClass('d-none');
        }
    })
});
