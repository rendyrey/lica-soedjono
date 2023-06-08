var baseUrl = function(url) {
    return base + url;
}
  
var columnsDataTable = [
    {
      data: "user_id",
        render: function (data, type, row, meta) {
            return meta.row + meta.settings._iDisplayStart + 1;
        }
    },
    { data: 'user'},
    { data: 'checkin',
        render: function (data, type, row, meta) {
            var show = '';
            if(row.checkin != 0){
                show = row.checkin;
            }else{
                show = '-';
            }
        return show;
    }
    },
    { data: 'draw',
        render: function (data, type, row, meta) {
            var show = '';
            if(row.draw != 0){
                show = row.draw;
            }else{
                show = '-';
            }
        return show;
    }
    },
    { data: 'process',
        render: function (data, type, row, meta) {
            var show = '';
            if(row.process != 0){
                show = row.process;
            }else{
                show = '-';
            }
        return show;
    }
    },
    { data: 'verify',
        render: function (data, type, row, meta) {
            var show = '';
            if(row.verify != 0){
                show = row.verify;
            }else{
                show = '-';
            }
        return show;
    }
    },
    { data: 'validate',
        render: function (data, type, row, meta) {
            var show = '';
            if(row.validate != 0){
                show = row.validate;
            }else{
                show = '-';
            }
        return show;
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
            // order: [[0, 'desc']],
            order: false,
            responsive: true,
            searchDelay: 500,
            processing: true,
            serverSide: true,
            stateSave: false,
            ajax: {
                url: baseUrl('report/user-datatable/')
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
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-datatable-test"]');
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