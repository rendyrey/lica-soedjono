"use strict";
var masterData = 'analyzer'; // required for the url
var withModel = []; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 3;
var columnsDataTable = [
    { data: 'name' },
    { data: 'group_id', render: function(data, type, row){
            // debugger;
            return row.group.name;
            // return row.name;
        }
    },
    { data: 'is_default', render: function(data, type, row){
            let checked ="";
            if(row.is_default ==1 ){
                checked = "checked"
            }
            var html = `<div class="form-check form-check-solid form-switch">
                            <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" `+checked+`  onChange="toogleDefaults(`+ row.id + `,`+row.is_default+`)">
                            <label class="form-check-label" for="googleswitch"></label>
                        </div>`;
            return html
        }
    }
];

var toogleDefaults = function (id,isDefault) {
  $.ajax({
    url: baseUrl('master/' + masterData + '/default/' + id + '/' + isDefault),
    method: 'PUT',
    success: function (res) {
      DatatablesServerSide.refreshTable();
      toastr.success(res.message, "Change Success!");
    },
    error: function (request, status, error) {
      toastr.error(request.responseJSON.message);
    }
  })
}

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal select[name='group_id']").html(
        `<option value='`+data.group_id+`' selected>`+ data.group.name +`</option>`
    );
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    group_id: {
        required: true
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    Select2ServerSide('group').init();
    FormValidation.init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });
});
