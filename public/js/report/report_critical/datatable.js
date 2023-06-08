var baseUrl = function(url) {
    return base + url;
}
  
var columnsDataTable = [
    {
      data: "id",
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }
    },
    { data: 'input_time',
        render: function (data, type, row, meta) {
            var date = moment(row.input_time).format("DD/MM/YYYY");
            return date;
    }
    },
    // { data: 'patient_name' },
    {data: 'no_lab', name: 'finish_transactions.no_lab'},
    {data: 'patient_medrec', name: 'finish_transactions.patient_medrec'},
    {data: 'patient_name', name: 'finish_transactions.patient_name'},
    {data: 'room_name', name: 'finish_transactions.room_name'},
    { data: 'test_name' },
    { data: 'global_result' },
    { data: 'normal_value' },
    { data: 'validate_time',
        render: function (data, type, row, meta) {
            var date = moment(row.validate_time).format("DD/MM/YYYY HH:mm:ss");
            return date;
        }
    },
    { data: 'report_time',
        render: function (data, type, row, meta) {
            var date = moment(row.report_time).format("DD/MM/YYYY HH:mm:ss");
            return date;
        }
    },
    { data: 'patient_medrec', 
        render: function (data, type, row, meta) {
            var report_time = moment(row.report_time).format("HH:mm:ss");
            var validate_time = moment(row.validate_time).format("HH:mm:ss");
            var tat_time = moment.utc(moment(validate_time, "HH:mm:ss").diff(moment(report_time, "HH:mm:ss"))).format("HH:mm:ss");

            return tat_time;
        }
    },

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
            order: [[0, 'desc']],
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: baseUrl('report/critical-datatable/')
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
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-datatable-critical"]');
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