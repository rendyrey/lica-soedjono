"use strict";
var masterData = 'price'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var responsiveButtonIndexColumn = 6;
var columnsDataTable = [
    { data: 'DT_RowIndex', orderable: false, searchable: false },
    { data: 'package_id', render: function(data, type, row){
            return row.package.name;
        }
    },
    { data: 'type' },
    { data: 'price', render: function(data, type, row) {
            return 'Rp'+data.toLocaleString('ID');
        }
    },
    { data: 'class' },
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
        orderable: false
    },
    {
        render: function (data, type, row) {
            return '';
        }, orderable: false
    }
    
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal select[name='package_id']").html(
        `<option value='`+data.package_id+`'>`+ data.package.name +`</option>`
    );
    $("#modal_form_horizontal select[name='type']").val(data.type).trigger('change');
    $("#modal_form_horizontal input[name='price']").val(data.price);
    $("#modal_form_horizontal input[name='class']").val(data.class);
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    package_id: {
        required: true,
    },
    type: {
        required: true
    },
    price: {
        required: true,
        number: true
    },
    class: {
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
