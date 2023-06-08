var editTestTransactionId = '0';
var editTestRoomClass = '0';
var selectedEditTestIds = [];
var selectedEditTestUniqueId = [];
var editTestBtn = function() {
  $("#edit-test-btn").on('click', function(){
    editTestTransactionId = $(this).data('transaction-id');
    editTestRoomClass = $(this).data('room-class');
    $.ajax({
      url: baseUrl('pre-analytics/edit-test/selected-test/'+editTestRoomClass+'/'+editTestTransactionId),
      type: 'POST',
      success: function(res){
        $("#selected-edit-test-unique-ids").val(res.selected_test_unique_ids);
        $("#selected-edit-test-ids").val(res.selected_test_ids);
        selectedEditTestIds = res.selected_test_ids.split(',');
        selectedEditTestUniqueId = res.selected_test_unique_ids.split(',');
        populateSelectedTest(res.data);
      }
    })
    
    $("#edit-test-modal").modal('show');
    const datatableNewUrl =  baseUrl('pre-analytics/edit-test/'+editTestRoomClass+'/'+editTestTransactionId+'/datatable');
    DatatableEditTestServerSide.refreshNewTable(datatableNewUrl);
  });
}

var addEditTestList = function(unique_id, type, name, price, roomClass, event, test_id) {
  selectedTestIds.push(unique_id);
  const isEven = (selectedTestIds.length % 2 == 0);
  const priceFormatted = (price != 'null' && price != '' && price != null) ? 'Rp'+price.toLocaleString('ID') : '';
  $("#selected-edit-test tr:last").after(`
    <tr class="`+(isEven == true ? 'even':'odd')+`">
      <td>`+test_id+` - `+name+`</td>
      <td>`+priceFormatted+`</td>
      <td>`+type+`</td>
      <td class="text-end">
      <i onClick="removeEditTestList('`+unique_id+`', event)" class="cursor-pointer bi bi-arrow-left-circle text-danger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Remove `+name+` to test list"></i>
      </td>
    </tr>
  `);
  event.target.closest('tr').remove();
  selectedEditTestUniqueId.push(unique_id);
  const newUrl = baseUrl('pre-analytics/test/'+roomClass+'/datatable/withoutId/'+selectedEditTestUniqueId.join(','));
  DatatableEditTestServerSide.refreshNewTable(newUrl);

  // $.ajax({
  //   url: baseUrl('pre-analytics/edit-test/add'),
  //   type: 'POST',
  //   data: {
  //     unique_id: unique_id,
  //     type: type,
  //     transaction_id: editTestTransactionId,
  //     room_class: roomClass
  //   },
  //   success: function(res) {
  //     event.target.closest('tr').remove();
  //     $("#selected-edit-test-ids").val(selectedEditTestIds.join(','));
  //     transactionTestTable.ajax.reload();
  //     transactionSpecimenTable.ajax.reload();
  //     DatatableEditTestServerSide.refreshTable();
  //     populateSelectedTest(res.data);

  //     toastr.success(res.message, "Add test successful!");
  //   }
  // })
 
}

var removeEditTestList = function(unique_id, event) {
    event.target.closest('tr').remove();
    let indexRemove = selectedEditTestUniqueId.indexOf(unique_id);
    selectedEditTestUniqueId.splice(indexRemove, 1);
    $("#selected-edit-test-unique-ids").val(selectedEditTestUniqueId.join(','));
    if (selectedEditTestUniqueId.length > 0) {
      const newUrl = baseUrl('pre-analytics/test/'+editTestRoomClass+'/datatable/withoutId/'+selectedEditTestUniqueId.join(','));
      DatatableEditTestServerSide.refreshNewTable(newUrl);
    } else {
      const newUrl = baseUrl('pre-analytics/test/'+editTestRoomClass+'/datatable');
      DatatableEditTestServerSide.refreshNewTable(newUrl);
    }

  // $.ajax({
  //   url: baseUrl('pre-analytics/edit-test/'+transaction_test_id+'/delete'),
  //   type: 'DELETE',
  //   success: function(res) {
  //     event.target.closest('tr').remove();
  //     DatatableEditTestServerSide.refreshTable();
  //     transactionTestTable.ajax.reload();
  //     transactionSpecimenTable.ajax.reload();
  //   }
  // })
}

var populateSelectedTest = function (tests) {
  $("#selected-edit-test").html("<tr></tr>");
  tests.forEach(function(item,index) {
    const isEven = (index % 2 == 0);
    const price = item.price;
    const priceFormatted = (price != null && price != '' && price != 'null') ? 'Rp'+price.toLocaleString('ID') : '';
    const removeButton = item.draw== "0" || item.draw== 0 ? `<i onClick="removeEditTestList('`+item.unique_id+`', event)" class="cursor-pointer bi bi-arrow-left-circle text-danger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Remove `+item.name+` to test list"></i>` : '';
    $("#selected-edit-test tr:last").after(`
    <tr class="`+(isEven == true ? 'even':'odd')+`">
      <td>`+item.id+` - `+item.name+`</td>
      <td>`+priceFormatted+`</td>
      <td>`+item.type+`</td>
      <td class="text-end">
      `+removeButton+`
      </td>
    </tr>
  `);
  });
}

var DatatableEditTestServerSide = function () {
  // Shared variables
  var table;
  var dt;
  var filterPayment;
  var selectorName = '.edit-test-datatable-ajax';

  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          paging: false,
          scrollY: '250px',
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: false,
          order: [[0, 'asc']],
          stateSave: false,
          ajax: {
              url: baseUrl('pre-analytics/edit-test/'+editTestRoomClass+'/'+editTestTransactionId+'/datatable'),
              method: 'get'
          },
          columns: [
            { data: 'name', searchable: true, render: function(data, type, row){
                return row.id+" - "+row.name;
                }
            },
            { data: 'price', render: function(data, type, row){
                if (data != null && data != '') {
                  return 'Rp'+data.toLocaleString('ID');
                }
                return '';
              }
            },
            { data: 'type' }
          ],
          columnDefs: [
              {
                  responsivePriority: 1,
                  targets: 3,
                  data: null,
                  orderable: false,
                  className: 'text-end',
                  searchable: false,
                  render: function (data, type, row) {
                      const price = (row.price != null && row.price != '') ? row.price : '';
                      return `<i onClick="addEditTestList('`+row.unique_id+`','`+row.type+`','`+row.name+`', '`+price+`', '`+editTestRoomClass+`', event,'`+row.id+`', )" class="cursor-pointer bi bi-arrow-right-circle text-success" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Add `+row.name+` to test list"></i>`;
                  },
              },
          ],
          // Add data-filter attribute
          // createdRow: function (row, data, dataIndex) {
          //     $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
          // }
      });

      table = dt.$;

      // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
      dt.on('draw', function () {
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });
      dt.columns.adjust().draw();
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-edit-test"]');
      filterSearch.addEventListener('keyup', function (e) {
          dt.search(e.target.value).draw();
      });
  }

  // Init toggle toolbar
  var initToggleToolbar = function () {
      // Toggle selected action toolbar
      // Select all checkboxes
      const container = document.querySelector(selectorName);
      const checkboxes = container.querySelectorAll('[type="checkbox"]');
      
      // Toggle delete selected toolbar
      checkboxes.forEach(c => {
          // Checkbox on click event
          c.addEventListener('click', function () {
              setTimeout(function () {
                  // toggleToolbars();
              }, 50);
          });
      });
  }
  

  // Toggle toolbars
  var toggleToolbars = function () {
      // Define variables
      const container = document.querySelector(selectorName);
      const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
      const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

      // Select refreshed checkbox DOM elements
      const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

      // Detect checkboxes state & count
      let checkedState = false;
      let count = 0;

      // Count checked boxes
      allCheckboxes.forEach(c => {
          if (c.checked) {
              checkedState = true;
              count++;
          }
      });

      // Toggle toolbars
      if (checkedState) {
          selectedCount.innerHTML = count;
          toolbarSelected.classList.remove('d-none');
      } else {
          toolbarSelected.classList.add('d-none');
      }
  }

  // Public methods
  return {
      init: function () {
          initDatatable();
          handleSearchDatatable();
          // initToggleToolbar();
          // handleFilterDatatable();
          // handleDeleteRows();
          // handleResetForm();
      },
      refreshTable: function() {
          dt.ajax.reload();
      },
      refreshNewTable: function(url) {
        dt.ajax.url(url).load();
      }
  }
}();

var editTestSubmit = function() {
  $("#edit-test-submit").on('click', function(){
    if (selectedEditTestUniqueId.length == 0) {
      Swal.fire({
        text: "Please select at least 1 test!",
        icon: "error",
        buttonsStyling: false,
        confirmButtonText: "Ok, got it!",
        customClass: {
            confirmButton: "btn btn-primary"
        }
      });
      return false;
    }

    updateEditTest();
    
  });
}

var updateEditTest = function () {
  $.ajax({
    url: baseUrl('pre-analytics/edit-test/update'),
    type: 'POST',
    data: {
      transaction_id: editTestTransactionId,
      unique_ids: selectedEditTestUniqueId.join(',')
    },
    success: function(res) {
      $("#edit-test-modal").modal('hide');
      transactionTestTable.ajax.reload();
      transactionSpecimenTable.ajax.reload();
      if (res.auto_draw) {
        $("#undraw-all-btn").show();
        $("#draw-all-btn").hide();    
      } else {
        $("#draw-all-btn").show();
        $("#undraw-all-btn").hide();
      }
      toastr.success(res.message, "Update test successful!");
    },
    error: function(request, status, error){
      toastr.error(request.responseJSON.message);
    }
  })
}

document.addEventListener('DOMContentLoaded', function () {
  editTestBtn();
  editTestSubmit();
  DatatableEditTestServerSide.init();

});