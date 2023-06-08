"use strict";
var masterData = 'price'; // required for the url
var withModel = ['test','package']; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 5; // this is equal to column count
var columnsDataTable = [
    { data: 'package.name', render: function (data, type, row) {
        if (row.package_id != null && row.package_id != '') {
          return row.package.name;
          
        }
        return '';
      }, defaultContent: ''
    },
    { data: 'test.name', render: function (data, type, row) {
        if (row.test_id != null && row.test_id != '') {
          return row.test.id+" - "+row.test.name;
        }
        return '';
      }, defaultContent: ''
    },
    { data: 'price', render: function (data, type, row) {
        return 'Rp' + data.toLocaleString('ID');
      }
    },
    { data: 'class' },
    { data: null, render: function(data, type, row) {
        if (row.test_id != null && row.test_id != '') {
          return row.test.group.name;;
        }
        if (row.package_id != null && row.package_id != '') {
          if (row.package.group_id != null && row.package.group_id != '') {
            return row.package.group.name;
          }
        }
      }, defaultContent: '', searchable: false
    }
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal form").trigger('reset');
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    if (data.type == 'test') {
      $("#test-type").trigger('click');
      if (data.test != null) {
        $("#modal_form_horizontal select[name='test_id']").html(
          `<option value='`+data.test_id+`' selected>`+data.test.id+` - `+data.test.name+` [`+data.test.initial+`]</option>`
        );
      }
    } else {
      $("#package-type").trigger('click');
      if (data.package != null) {
        $("#modal_form_horizontal select[name='package_id']").html(
          `<option value='`+data.package_id+`' selected>`+data.package.name+`</option>`
        );
      }
    }
    $("#modal_form_horizontal input[name='class']").val(data.class);
    $("#modal_form_horizontal input[name='price']").val(data.price);
}

// required for the form validation rules
var rulesFormValidation = {
    "class_price[0][class]": {
        required: true,
        digits: true,
        min: 1,
        max: 10,
    },
    "class_price[0][price]": {
        required: true,
        number: true
    },
    "class_price[1][class]": {
      required: true,
      digits: true,
      min: 1,
      max: 10,
    },
    "class_price[1][price]": {
        required: true,
        number: true
    },
    "class_price[2][class]": {
      required: true,
      digits: true,
      min: 1,
      max: 10,
    },
    "class_price[2][price]": {
        required: true,
        number: true
    },
    test_id: {
      required: true
    },
    package_id: {
      required: true
    },
    class: {
      required: true,
      digits: true,
      min: 1,
      max: 10
    },
    price: {
      required: true,
      number: true
    }
    
};

var createPrice = function () {
  $("#submit-btn").prop('disabled', true); // disabled button

  let formData = $("#form-create-price").serialize();
  let theForm = $("#form-create-price");
  $.ajax({
      url: baseUrl('master/'+masterData+'/create'),
      method: 'POST',
      data: formData,
      success: function(res) {
          toastr.success(res.message, "Create Success!");
          
          DatatablesServerSide.refreshTable();

          $("#submit-btn").prop('disabled', false); // re-enable submit button
          $(".type-test, .type-test[value='test']").trigger('click');
          $(".select-two").val('').trigger("change"); // unselect all the select form
      },
      error: function (request, status, error) {
          toastr.error(request.responseJSON.message);
          $("#submit-btn").prop('disabled', false); // re-enable button
      }
  })
}

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('test').init();
    Select2ServerSide('package').init();
    
    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $('#form-create-price input[name="type"]').on('change', function(e) {
      const type = $(this).val();

      if (type == 'test') {
        $("#test-list").removeClass('d-none');
        $("#package-list").addClass('d-none');
        $("#form-create-price select[name='test_id']").removeClass('ignore-this');
        $("#form-create-price select[name='package_id']").addClass('ignore-this');
      } else {
        $("#test-list").addClass('d-none');
        $("#package-list").removeClass('d-none');
        $("#form-create-price select[name='test_id']").addClass('ignore-this');
        $("#form-create-price select[name='package_id']").removeClass('ignore-this');
      }
    });

    $('#form-edit input[name="type"]').on('change', function(e) {
      const type = $(this).val();
      if (type == 'test') {
        $("#test-list-edit").removeClass('d-none');
        $("#package-list-edit").addClass('d-none');
        $("#form-edit select[name='test_id']").removeClass('ignore-this');
        $("#form-edit select[name='package_id']").addClass('ignore-this');
      } else {
        $("#test-list-edit").addClass('d-none');
        $("#package-list-edit").removeClass('d-none');
        $("#form-edit select[name='test_id']").addClass('ignore-this');
        $("#form-edit select[name='package_id']").removeClass('ignore-this');
      }
    });

    $('#class_price').repeater({
      initEmpty: false,
  
      defaultValues: {
          'text-input': 'foo'
      },
  
      show: function () {
          $(this).slideDown();
          $(".thousands-separator").on('keyup', function(){
            var val = $(this).val();
            var valArr;
            val = val.replace(/[^0-9\.]/g,'');
          
            if(val != "") {
              valArr = val.split('.');
              valArr[0] = (parseInt(valArr[0],10)).toLocaleString();
              val = valArr.join('.');
            }
          
            $(this).val(val);
          });
          if ($('#class_price div[data-repeater-item]').length == 3) {
            $("a[data-repeater-create]").parents('.form-group').addClass('d-none');
          } else {
            $("a[data-repeater-create]").parents('.form-group').removeClass('d-none');
          }
      },
  
      hide: function (deleteElement) {
          $(this).slideUp(deleteElement);
          if ($('#class_price div[data-repeater-item]').length < 1) {
            $("a[data-repeater-create]").parents('.form-group').addClass('d-none');
          } else {
            $("a[data-repeater-create]").parents('.form-group').removeClass('d-none');
          }
      }
    });

    $(".thousands-separator").on('keyup', function(){
      var val = $(this).val();
      var valArr;
      val = val.replace(/[^0-9\.]/g,'');
    
      if(val != "") {
        valArr = val.split('.');
        valArr[0] = (parseInt(valArr[0],10)).toLocaleString();
        val = valArr.join('.');
      }
    
      $(this).val(val);
    })

    $("#form-create-price").on('submit', function(e) {
      e.preventDefault();
      if ($(this).valid()) {
          createPrice();
      }
  });
    
});
