var buttonActionIndex = 4;
var analyticsColumnDatatable = [
  { data: 'no_lab' },
  { data: 'patient.medrec' },
  { data: 'room.room' },
  { data: 'patient.name' },
  {
    data: 'cito', render: function (data, type, row) {
      let cito = '';
      if (row.cito == 'cito' || row.cito == 1) {
        cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
      }

      let igd = '';
      if (row.is_igd == 1) {
        igd = `<span style="color:red" data-toggle="tooltip" data-placement="top" title="Ruang IGD"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-pulse" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053.918 3.995.78 5.323 1.508 7H.43c-2.128-5.697 4.165-8.83 7.394-5.857.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17c3.23-2.974 9.522.159 7.394 5.856h-1.078c.728-1.677.59-3.005.108-3.947C13.486.878 10.4.28 8.717 2.01L8 2.748ZM2.212 10h1.315C4.593 11.183 6.05 12.458 8 13.795c1.949-1.337 3.407-2.612 4.473-3.795h1.315c-1.265 1.566-3.14 3.25-5.788 5-2.648-1.75-4.523-3.434-5.788-5Zm8.252-6.686a.5.5 0 0 0-.945.049L7.921 8.956 6.464 5.314a.5.5 0 0 0-.88-.091L3.732 8H.5a.5.5 0 0 0 0 1H4a.5.5 0 0 0 .416-.223l1.473-2.209 1.647 4.118a.5.5 0 0 0 .945-.049l1.598-5.593 1.457 3.642A.5.5 0 0 0 12 9h3.5a.5.5 0 0 0 0-1h-3.162l-1.874-4.686Z"/>
            </svg> </span>`
      }else{
          igd = `<span style="color:black" data-toggle="tooltip" data-placement="top" title="Ruang Central"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-c-circle" viewBox="0 0 16 16">
                  <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8Zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM8.146 4.992c-1.212 0-1.927.92-1.927 2.502v1.06c0 1.571.703 2.462 1.927 2.462.979 0 1.641-.586 1.729-1.418h1.295v.093c-.1 1.448-1.354 2.467-3.03 2.467-2.091 0-3.269-1.336-3.269-3.603V7.482c0-2.261 1.201-3.638 3.27-3.638 1.681 0 2.935 1.054 3.029 2.572v.088H9.875c-.088-.879-.768-1.512-1.729-1.512Z"/>
                </svg> </span>`
      }
      return "<div>" + cito + ' '+igd+ "</div>";
    }, defaultContent: '', responsivePriority: 1
  },
];

var labelType = function (transactionTestId, tabIndex) {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;
  return input;
}

var descriptionType = function () {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;
  return input;
}

var numberType = function (transactionTestId, tabIndex) {
  var input = `
    <div id="result-html-`+transactionTestId+`">
      <select tabindex="`+tabIndex+`" id="select-result-label-`+transactionTestId+`" data-control="select2" data-placeholder="Select label" class="select form-select form-select-sm form-select-solid my-0 me-4">
      </select>
    </div>
  `;

  return input;
}

var freeFormatType = function (transactionTestId, tabIndex) {
  var input = `
    
  `;
}

var transactionTestColumnTable = [
  { data: 'test.name' },
  { data: 'test.range_type', render: function(data, type, row) {
      switch (data) {
        case 'description':
          return 'description';
        case 'number':
          return numberType(row.tt_id, row.DT_RowIndex);
        case 'label':
          return labelType(row.tt_id, row.DT_RowIndex);
        default:
          return 'invalid';
      }
    }
  },
  { data: 'test.name' },
  { data: 'test.name' },
  { data: 'test.name' },
  { data: 'test.name' }
];

var DatatableAnalytics = function () {
  // Shared variables
  var table;
  var dt;
  var selectorName = '.analytics-datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          "createdRow": function( row, data, dataIndex){
            if(data.is_critical == 1){
              $(row).addClass('bg-danger');
            }
          },
          paging: false,
          scrollY: '400px',
          scrollX: '100%',
          select: {
            style: 'single'
          },
          order: [[0, 'desc']],
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: true,
          // info:     false,
          stateSave: false,
          ajax: {
              url: baseUrl('analytics/datatable')
          },
          columns: analyticsColumnDatatable,
          columnDefs: [
              {
                  responsivePriority: 1,
                  targets: buttonActionIndex,
                  data: null,
                  orderable: false,
                  className: 'text-end',
                  searchable: false,
                  render: function(data, type, row) {
                      return `
                              <button class="btn btn-light-danger btn-sm px-2" data-kt-docs-table-filter="delete_row" onClick="deleteTransaction(`+row.id+`)">
                                <i class="bi bi-trash-fill pe-0"></i>
                              </button>
                      `;
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
      dt.on('draw', function (e, data, type, indexes) {
          // debugger;
          console.log(dt.rows().data().toArray());
          console.log(dt.rows().data());
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });

      dt.on('select', function(e, data, type, indexes) {
        const selectedData = data.rows().data()[indexes];
        selectedTransactionId = selectedData.t_id;
        onSelectTransaction(selectedTransactionId);
      });
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-analytics"]');
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
      refreshTableAjax: function(url) {
          dt.ajax.url(url).load();
      }
  }
}();

var DatatableTest = function () {
  // Shared variables
  var table;
  var dt;
  var selectorName = '.transaction-test-table';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          paging: false,
          bInfo: false,
          info: false,
          scrollY: '400px',
          scrollX: '100%',
          // order: [[0, 'desc']],
          order: false,
          responsive: true,
          // searchDelay: 500,
          processing: false,
          serverSide: false,
          stateSave: false
          // ajax: {
          //     url: baseUrl('analytics/datatable-test/0'),
          //     type: 'get',
          //     complete: function(data) {
          //       const testData = data.responseJSON.data;
          //       testData.forEach(function(item){
          //         $.ajax({
          //           url: baseUrl('analytics/result-label/'+item.test_id),
          //           type: 'get',
          //           success: function(res) {
          //             if (res.message) {
          //               $("#result-html-"+item.tt_id).html(res.message);
          //             } else {
          //               $("#select-result-label-"+item.tt_id).html(res);
          //               $("#select-result-label-"+item.tt_id).select2({allowClear:true});
          //               $("#select-result-label-"+item.tt_id).val(item.result_label).trigger('change');
          //               // $("#result-test-"+item.tt_id).attr('onChange',"analyzerChange("+item.id+","+item.transaction_id+",event)");

          //             }
          //           }
          //         })
          //       });
          //       console.log(data.responseJSON.data);
          //     }
          // },
          // columns: transactionTestColumnTable
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
  }


  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-analytics"]');
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
          // handleSearchDatatable();
          // initToggleToolbar();
          // handleFilterDatatable();
          // handleDeleteRows();
          // handleResetForm();
      },
      refreshTable: function() {
          dt.ajax.reload();
      },
      refreshTableAjax: function(url) {
          dt.ajax.url(url).load();
      },
      destroy: function () {
          dt.destroy();
      }
  }
}();