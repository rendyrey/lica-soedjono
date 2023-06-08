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
    { data: 'created_time',
        render: function (data, type, row, meta) {
            var date = moment(row.created_time).format("DD/MM/YYYY");
            return date;
        }
    },
    { data: 'no_lab' },
    { data: 'checkin_time', 
        render: function (data, type, row, meta) {
            var checkin_time = moment(row.checkin_time).format("HH:mm:ss");
            return checkin_time;
        }
    },
    { data: 'analytic_time',
        render: function (data, type, row, meta) {
            var checkin_time = moment(row.checkin_time).format("HH:mm:ss");
            var analytic = moment(row.analytic_time).format("HH:mm:ss");
            var analytic_time = moment.utc(moment(analytic, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");

            return '+ ' + analytic_time;
        }
    },
    { data: 'validate_time', name: 'finish_transaction_tests.validate_time',
        render: function (data, type, row, meta) {
            var checkin_time = moment(row.checkin_time).format("HH:mm:ss");
            var validate = moment(row.validate_time).format("HH:mm:ss");
            var validate_time = moment.utc(moment(validate, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");

            return '+ ' + validate_time;
        }
    },
    { data: 'post_time',
        render: function (data, type, row, meta) {
            var checkin_time = moment(row.checkin_time).format("HH:mm:ss");
            var post = moment(row.post_time).format("HH:mm:ss");
            var post_time = moment.utc(moment(post, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");

            return '+ ' + post_time;
        }
    },
    { data: 'id', 
        render: function (data, type, row, meta) {
            // checkin time
            var checkin_time = moment(row.checkin_time).format("HH:mm:ss");
            // analytic time
            var analytic = moment(row.analytic_time).format("HH:mm:ss");
            var analytic_time = moment.utc(moment(analytic, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");
            // validate time
            var validate = moment(row.validate_time).format("HH:mm:ss");
            var validate_time = moment.utc(moment(validate, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");
            // post time
            var post = moment(row.post_time).format("HH:mm:ss");
            var post_time = moment.utc(moment(post, "HH:mm:ss").diff(moment(checkin_time, "HH:mm:ss"))).format("HH:mm:ss");
            // analytic + validate
            var anal_val = moment.duration(analytic_time).add(moment.duration(validate_time))
            var result_anal_val = moment.utc(anal_val.as('milliseconds')).format("HH:mm:ss")
            // (analytic + validate) + post time
            var total = moment.duration(result_anal_val).add(moment.duration(post_time))
            var total_time = moment.utc(total.as('milliseconds')).format("HH:mm:ss")

            return '+ ' + total_time;
        }
    }
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
                url: baseUrl('report/tat-datatable/')
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