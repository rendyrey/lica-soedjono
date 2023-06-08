var baseUrl = function(url) {
    return base + url;
  }
  
  var theFullDate = function(date) {
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    let d = new Date(date);
    let day = d.getDate();
    let month = d.getMonth() + 1;
    let year = d.getFullYear();
    let theDate = day + '/' + ('0'+month).slice(-2)  + '/' + year;
  
    return theDate;
  }

  var theFullMinute = function(date) {
    
    let d = new Date(date);
    let hours = d.getHours();
    let minutes = d.getMinutes();
    let seconds = d.getSeconds();
    let theMinute = hours + ':' + minutes  + ':' + seconds;
  
    return theMinute;
  }
 
  // Datatable Component
  var selectedTransactionId;

  // ===============================
  // PRE ANALYTICS DATA
  // ===============================
  var columnsDataTablePreData = [
    { data: 'created_time', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'transaction_id_label' },
    { data: 'no_lab' },
    { data: 'patient.name' },
    { data: 'cito', render: function(data, type, row) {
        let cito = '';
        if (row.cito == 'cito' || row.cito == 1) {
          cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
        }
        return "<div>"+cito+"</div>";
      }, defaultContent: '', responsivePriority: 1},
  ];

  var DatatablesPreDataServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.pre-analytics-datatable-ajax';
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
                url: baseUrl('qms/datatable-pre-analytics/')
            },
            columns: columnsDataTablePreData
        });
  
        table = dt.$;
  
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatablePreData = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-pre-analytics"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatablePreData();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshTableAjax: function(url) {
            dt.ajax.url(url).load();
        }
    }
  }();

  var DateRangePickerPreData = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-picker-pre-analytics").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-picker-pre-analytics").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-picker-pre-analytics").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-picker-pre-analytics").daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
        "Today": [moment(), moment()],
        "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        },
        locale:{
          format: 'DD MMMM YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-picker-pre-analytics").on('change', function () {
      const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
      const url = baseUrl('qms/datatable-pre-analytics/'+startDate+'/'+endDate);
      DatatablesPreDataServerSide.refreshTableAjax(url);
    });
  }
  // ===============================
  // END PRE ANALYTICS DATA
  // ===============================

  // ===============================
  // ANALYTICS DATA
  // ===============================
  var columnsDataTableAnalyticsData = [
    { data: 'created_time', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'transaction_id_label' },
    { data: 'no_lab' },
    { data: 'patient.name' },
    { data: 'cito', render: function(data, type, row) {
        let cito = '';
        if (row.cito == 'cito' || row.cito == 1) {
          cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
        }
        return "<div>"+cito+"</div>";
      }, defaultContent: '', responsivePriority: 1},
  ];

  var DatatablesAnalyticsDataServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.analytics-datatable-ajax';
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
                url: baseUrl('qms/datatable-analytics/')
            },
            columns: columnsDataTableAnalyticsData
        });
  
        table = dt.$;
  
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatableAnalyticsData = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-analytics"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatableAnalyticsData();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshTableAjax: function(url) {
            dt.ajax.url(url).load();
        }
    }
  }();

  var DateRangePickerAnalyticsData = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-picker-analytics").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-picker-analytics").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-picker-analytics").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-picker-analytics").daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
        "Today": [moment(), moment()],
        "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        },
        locale:{
          format: 'DD MMMM YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-picker-analytics").on('change', function () {
      const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
      const url = baseUrl('qms/datatable-analytics/'+startDate+'/'+endDate);
      DatatablesAnalyticsDataServerSide.refreshTableAjax(url);
    });
  }
  // ===============================
  // END ANALYTICS DATA
  // ===============================

  // ===============================
  // POST ANALYTICS DATA
  // ===============================
  var columnsDataTablePostData = [
    { data: 'created_time', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'no_lab' },
    { data: 'patient.name' },
    { data: 'cito', render: function(data, type, row) {
        let cito = '';
        if (row.cito == 'cito' || row.cito == 1) {
          cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
        }
        let print = '';
        if (row.status == 4) {
            print = '<i class="ms-2 bi bi-printer text-success" data-toggle="tooltip" data-placement="top" title="Printed"></i>'
        }
        let completed = '';
        if (row.completed == 1) {
          completed = '<i class="ms-2 bi bi-check-square-fill text-success" data-toggle="tooltip" data-placement="top" title="Completed"></i>'
          }

        return "<div>"+cito+' '+print+' '+completed+"</div>";
      }, defaultContent: '', responsivePriority: 1},
  ];

  var DatatablesPostDataServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.post-analytics-datatable-ajax';
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
                url: baseUrl('qms/datatable-post-analytics/')
            },
            columns: columnsDataTablePostData
        });
  
        table = dt.$;

        dt.on('select', function(e, data, type, indexes) {
            const selectedData = data.rows().data()[indexes];
            selectedTransactionId = selectedData.id;
            onSelectTransactionPost(selectedData);
            // alert(selectedTransactionId);
          });
    }

    var onSelectTransactionPost = (selectedData) => {
        // console.log(selectedData)
        const finishTransactionId = selectedData.id;
        
        Swal.fire({
            title: 'Want to complete this patient?',
            customClass: 'w-600px',
            inputAttributes: {
              autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Confirm',
            showLoaderOnConfirm: true,
            allowOutsideClick: false
          }).then((result) => {
            if (result.isConfirmed) {
              $.ajax({
                url: baseUrl('qms/update-completed-patient'),
                type: 'put',
                data: {
                    finishTransactionId: finishTransactionId
                },
                success: function(res) {
                    toastr.success("Patient has been completed");
                    DatatablesPostDataServerSide.refreshTable();
                }
              });
            } else {
              // event.target.checked = true;
            }
        });
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatablePostData = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-post-analytics"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatablePostData();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshTableAjax: function(url) {
            dt.ajax.url(url).load();
        }
    }
  }();

  var DateRangePickerPostData = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-picker-post-analytics").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-picker-post-analytics").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-picker-post-analytics").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-picker-post-analytics").daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
        "Today": [moment(), moment()],
        "Yesterday": [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Last 7 Days": [moment().subtract(6, "days"), moment()],
        "Last 30 Days": [moment().subtract(29, "days"), moment()],
        "This Month": [moment().startOf("month"), moment().endOf("month")],
        "Last Month": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        },
        locale:{
          format: 'DD MMMM YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-picker-post-analytics").on('change', function () {
      const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
      const url = baseUrl('qms/datatable-post-analytics/'+startDate+'/'+endDate);
      DatatablesPostDataServerSide.refreshTableAjax(url);
    });
  }
  // ===============================
  // END POST ANALYTICS DATA
  // ===============================
  
    var onSelectTransaction = (transactionId) => {
    console.log(transactionId)
    }

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });
    
  
  // On document ready
  document.addEventListener('DOMContentLoaded', function () {
    DatatablesPreDataServerSide.init();
    DateRangePickerPreData();
    DatatablesAnalyticsDataServerSide.init();
    DateRangePickerAnalyticsData();
    DatatablesPostDataServerSide.init();
    DateRangePickerPostData();

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
        trigger: 'hover'
      });
  });