var baseUrl = function(url) {
    return base + url;
}
  
var columnsDataTable = [
    {
      data: "group_id",
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }
    },
    // { data: 'created_time', name: 'finish_transactions.created_time',
    //     render: function (data, type, row, meta) {
    //         var date = moment(row.created_time).format("DD/MM/YYYY");
    //         return date;
    //     }
    // },
    { data: 'group_name' },
    { data: 'jumlah_pemeriksaan' },
    { data: 'target_tat' },
    { data: 'final_target_dibawah' },
    { data: 'final_target_diatas' }
  ];
  
  // Datatable Component
  var selectedTransactionId;
  var Datatable = function () {
    // Shared variables
    var dt;
    var selectorName = '.datatable-ajax';
    // Private functions
    var initDatatable = function () {
        dt = $(selectorName).DataTable({
            paging: false,
            scrollY: '400px',
            scrollX: '100%',
            select: {
              style: 'single'
            },
            order: [[0, 'asc']],
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: baseUrl('report/tat-target-datatable/')
            },
            columns: columnsDataTable,
            // columnDefs: [{
            //   "defaultContent": "-",
            //   "targets": "_all"
            // }]
        });
    }
  
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-datatable-tat"]');
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