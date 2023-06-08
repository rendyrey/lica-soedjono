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
  
  var badgeStatus = 3;
  var buttonJson = 5;
  var buttonJsonResponse = 6;

  // Datatable Component
  var selectedTransactionId;

  // ===============================
  // POST DATA
  // ===============================
  var columnsDataTablePostData = [
    { data: 'timestamp', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'timestamp', render: function(data, type, row) {
        return theFullMinute(data);
      }
    },
    { data: 'no_order' },
    { data: 'status' },
    { data: 'notes' },
    { data: 'return_result' },
  ];

  var DatatablesPostDataServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.post-data-log-datatable-ajax';
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
                url: baseUrl('log-integration/datatable-post-data/')
            },
            columns: columnsDataTablePostData,
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: buttonJson,
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                          <button class="btn btn-primary btn-sm px-2" onClick="showLogJSON(`+row.id+`)">
                            JSON
                          </button>
                        `;
                    },
                },
                {
                  responsivePriority: 1,
                  targets: badgeStatus,
                  data: null,
                  orderable: false,
                  searchable: false,
                  render: function (data, type, row) {
                    var badge;
                      if(row.status == 'insert_transaction_success'){
                        badge = `<span class="badge badge-success ms-2">`+ row.status +`</span>`;
                      }else if(row.status == 'insert_transaction_failed'){
                        badge = `<span class="badge badge-danger ms-2">`+ row.status +`</span>`;
                      }
                      return badge;
                  },
              },
            ],
        });
  
        table = dt.$;
  
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatablePostData = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-post-data-log"]');
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
        $("#daterange-picker-post-data").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-picker-post-data").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-picker-post-data").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-picker-post-data").daterangepicker({
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
  
    $("#daterange-picker-post-data").on('change', function () {
      const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
      const url = baseUrl('log-integration/datatable-post-data/'+startDate+'/'+endDate);
      DatatablesPostDataServerSide.refreshTableAjax(url);
    });
  }
  // ===============================
  // END POST DATA
  // ===============================


  // ===============================
  // POST DATA
  // ===============================
  var columnsDataTableSendData = [
    { data: 'timestamp', render: function(data, type, row) {
        return theFullDate(data);
      }
    },
    { data: 'timestamp', render: function(data, type, row) {
        return theFullMinute(data);
      }
    },
    { data: 'no_order' },
    { data: 'status' },
    { data: 'notes' },
    { data: 'return_result' },
    { data: 'simrs_response' }
  ];
  
  var DatatablesSendDataServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.send-data-log-datatable-ajax';
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
                url: baseUrl('log-integration/datatable-send-data/')
            },
            columns: columnsDataTableSendData,
            columnDefs: [
                {
                    responsivePriority: 1,
                    targets: buttonJson,
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                          <button class="btn btn-primary btn-sm px-2" onClick="showLogJSON(`+row.id+`)">
                            JSON
                          </button>
                        `;
                    },
                },
                {
                  responsivePriority: 1,
                  targets: buttonJsonResponse,
                  data: null,
                  orderable: false,
                  searchable: false,
                  render: function (data, type, row) {
                      return `
                        <button class="btn btn-primary btn-sm px-2" onClick="showLogJSONResponse(`+row.id+`)">
                          JSON
                        </button>
                      `;
                  },
                },
                {
                  responsivePriority: 1,
                  targets: badgeStatus,
                  data: null,
                  orderable: false,
                  searchable: false,
                  render: function (data, type, row) {
                    var badge;
                      if(row.status == 'send_result_success'){
                        badge = `<span class="badge badge-success ms-2">`+ row.status +`</span>`;
                      }else{
                        badge = `<span class="badge badge-danger ms-2">`+ row.status +`</span>`;
                      }
                      return badge;
                  },
              },
            ],
        });
  
        table = dt.$;
  
    }
  
    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatableSendData = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-send-data-log"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }
  
    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatableSendData();
        },
        refreshTable: function() {
            dt.ajax.reload();
        },
        refreshTableAjax: function(url) {
            dt.ajax.url(url).load();
        }
    }
  }();
  
  var DateRangePickerSendData = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-picker-send-data").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-picker-send-data").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-picker-send-data").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-picker-send-data").daterangepicker({
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
  
    $("#daterange-picker-send-data").on('change', function () {
      const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
      const url = baseUrl('log-integration/datatable-send-data/'+startDate+'/'+endDate);
      DatatablesSendDataServerSide.refreshTableAjax(url);
    });
  }
  // ===============================
  // END POST DATA
  // ===============================

  
  // Show Log JSON
  var showLogJSON = function (id) {
  
    $.ajax({
        url: baseUrl('log-integration/show-log/'+id),
        method: 'GET',
        success: function(res) {
          if(res.return_result == 'Data Added'){
            $('#jsonBody').html('Data added successfully');       
            $('#show-json-modal').modal('show'); 
          }else{
            $('#jsonBody').html(JSON.stringify(JSON.parse(res.return_result), null, 2).replace(/\n( *)/g, function (match, p1) {
              return '<br>' + '&nbsp;'.repeat(p1.length);
            }));       
            $('#show-json-modal').modal('show'); 
          }
        }
    })  
  }

  // Show Log JSON Response
  var showLogJSONResponse = function (id) {
  
    $.ajax({
        url: baseUrl('log-integration/show-log/'+id),
        method: 'GET',
        success: function(res) {
          if(res.simrs_response == 'Data Added'){
            $('#jsonResponseBody').html('Data added successfully');       
            $('#show-json-response-modal').modal('show'); 
          }else{
            $('#jsonResponseBody').html(JSON.stringify(JSON.parse(res.simrs_response), null, 2).replace(/\n( *)/g, function (match, p1) {
              return '<br>' + '&nbsp;'.repeat(p1.length);
            }));       
            $('#show-json-response-modal').modal('show'); 
          }
        }
    })  
  }
  
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  
  // On document ready
  document.addEventListener('DOMContentLoaded', function () {
    DatatablesPostDataServerSide.init();
    DatatablesSendDataServerSide.init();
    DateRangePickerPostData();
    DateRangePickerSendData();
  });