"use strict";
var masterData = 'test'; // required for the url
var withModel = ['group','specimen']; // required for the datatable if the model of the datatable has eager load or relationship

// required for the datatable columns
var buttonActionIndex = 14;
var columnsDataTable = [
    { data: 'id'},
    { data: 'name'},
    { data: 'initial' },
    { data: 'unit' },
    { data: 'volume' },
    { data: 'range_type' },
    { data: 'group', name: 'group.name', render: function(data, type, row){
            if(data){
                return data.name;
            }else{
                return '';
            }
        } 
    },
    { data: 'sub_group' },
    { data: 'specimen', name: 'specimen.name', render: function(data, type, row){
            if(data){
                return data.name;
            }else{
                return '';
            }
        }
    },
    { data: 'sequence' },
    {data: 'is_active', name: 'is_active', render: function (data, type, row) {
      if (row.is_active) {
        return `<div class="menu-item px-3">
                    <button class="btn btn-warning form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="toogleActive(`+ row.id +`,'0')">
                    Set Inactive
                    </button>
                </div>`;
      } else {
        return `<div class="menu-item px-3">
                    <button class="btn btn-success form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="toogleActive(`+ row.id + `,'1')">
                    Set Active
                    </button>
                </div>`;
      }
    },
  },
  { data: 'general_code' },
  { data: 'format_decimal' },
  { data: 'format_diff_count' }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='initial']").val(data.initial);
    $("#modal_form_horizontal input[name='unit']").val(data.unit);
    $("#modal_form_horizontal input[name='volume']").val(data.volume);
    $("#modal_form_horizontal input[name='range_type']").val(data.range_type);
    $("#modal_form_horizontal select[name='group_id']").html(
        `<option value='`+data.group_id+`' selected>`+data.group.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sub_group']").val(data.sub_group);
    $("#modal_form_horizontal select[name='specimen_id']").html(
        `<option value='`+data.specimen_id+`' selected>`+data.specimen.name+`</option>`
    )
    $("#modal_form_horizontal input[name='sequence']").val(data.sequence);
    $("#modal_form_horizontal select[name='range_type']").val(data.range_type).trigger('change');

  if ($("#modal_form_horizontal select[name='range_type']").val() == 'description' || $("#modal_form_horizontal select[name='range_type']").val() == 'label') {
        $("#normal-notes-edit").removeClass('d-none');
        if(data.normal_notes == null){
          ckeditor_edit.setData('');
        }else{
          ckeditor_edit.setData(data.normal_notes);
        }
    }
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);
    $("#modal_form_horizontal input[name='format_decimal']").val(data.format_decimal);
    $("#modal_form_horizontal input[name='format_diff_count']").val(data.format_diff_count);

}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    initial: {
        required: true,
    },
    volume: {
        required: true,
        number: true
    },
    group_id: {
        required: true,
    },
    specimen_id: {
        required: true,
    },
    sequence: {
        required: true,
        digits: true
    },
    range_type: {
        required: true,
    }
};

// this is for open select2 when pressing tab in keyboard
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});

var ckeditor,ckeditor_edit;

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('group').init();
    Select2ServerSide('specimen').init();
    ClassicEditor
        .create(document.querySelector('#editor-full'), {
            toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ]
        })
        .then(editor => {
          ckeditor = editor;
        })
        .catch(error => {
            console.error(error);
        });
    
        ClassicEditor
        .create(document.querySelector('#editor-full-edit'), {
            toolbar: [ 'bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList' ]
        })
        .then(editor => {
          ckeditor_edit = editor;
        })
        .catch(error => {
            console.error(error);
        });

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $(".range-type").on('change', function (e) {
        if ($(this).val() == 'description' || $(this).val() == 'label') {
            $("#normal-notes").removeClass('d-none');
            $("#decimal-format").toggle(false);
            $("#diff-count-format").toggle(false);
        } else if($(this).val() == 'number') {
            $("#normal-notes").addClass('d-none');
            $("#decimal-format").toggle(true);
            $("#diff-count-format").toggle(true);
        }
    });
    
    $(".range-type-edit").on('change', function (e) {
        if ($(this).val() == 'description' || $(this).val() == 'label') {
            $("#normal-notes-edit").removeClass('d-none');
            $("#decimal-format-edit").toggle(false);
            $("#diff-count-format-edit").toggle(false);
        } else {
            $("#normal-notes-edit").addClass('d-none');
            $("#decimal-format-edit").toggle(true);
            $("#diff-count-format-edit").toggle(true);
        }
    })
});
