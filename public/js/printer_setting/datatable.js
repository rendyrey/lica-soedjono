var baseUrl = function(url) {
  return base + url;
}

var columnsDataTable = [
  { data: 'name' },
  { data: 'printer_client_target' },
  { data: 'printer_client_name' },
  { data: 'width' },
  { data: 'height' },
  { data: 'is_active', render: function(data, type, row){
            let checked ="";
            if(row.is_default ==1 ){
                checked = "checked"
            }
            var html = `<div class="form-check form-check-solid form-switch">
                            <input class="form-check-input w-45px h-30px" type="checkbox" id="googleswitch" `+checked+`  onChange="toogleActive(`+ row.id + `,`+row.is_default+`)">
                            <label class="form-check-label" for="googleswitch"></label>
                        </div>`;
            return html
        }
    },
];


var toogleActive = function (id,isDefault) {
  $.ajax({
    url: baseUrl('printer-setting/active/' + id + '/' + isDefault),
    method: 'PUT',
    success: function (res) {
      Datatable.refreshTable();
      toastr.success(res.message, "Change Success!");
    },
    error: function (request, status, error) {
      toastr.error(request.responseJSON.message);
    }
  })
}

// Datatable Component
var selectedTransactionId;
var Datatable = function () {
  // Shared variables
  var dt;
  var selectorName = '.datatable-ajax';
  // Private functions
  var initDatatable = function () {
      dt = $(selectorName).DataTable({
          responsive: true,
          searchDelay: 500,
          processing: true,
          serverSide: true,
          order: [],
          stateSave: false,
          ajax: {
              url: baseUrl('printer-setting/datatable/')
          },
          columns: columnsDataTable,
          columnDefs: [
            {
                responsivePriority: 1,
                targets: 6,
                data: null,
                orderable: false,
                className: 'text-end',
                searchable: false,
                render: function (data, type, row) {
                  let btnactive = '';
                  
                    return `
                        <a href="#" class="btn btn-light-info btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <i class="fas fa-ellipsis-v"></i>
                        </a>
                        <!--begin::Menu-->
                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <button class="btn btn-light-primary form-control btn-sm" data-kt-docs-table-filter="edit_row" onClick="editData(`+row.id+`)">
                                <i class="fas fa-edit"></i>Edit
                                </button>
                            </div>
                            <!--end::Menu item-->
                            `+ btnactive +`
                            <!--begin::Menu item-->
                            <div class="menu-item px-3">
                                <button class="btn btn-light-danger form-control btn-sm my-1 px-3" data-kt-docs-table-filter="delete_row" onClick="deleteData(`+row.id+`)">
                                <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                            <!--end::Menu item-->
                        </div>
                        <!--end::Menu-->
                    `;
                },
            },
        ],
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
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-datatable"]');
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
          initToggleToolbar();
            KTMenu.createInstances();

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