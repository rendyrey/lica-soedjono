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

var newFormId = '#new-pre-analytics';

var buttonActionIndex = 7;
var columnsDataTable = [
  { data: 'created_time', render: function(data, type, row) {
      return theFullDate(data);
    }
  },
  { data: 'transaction_id_label' },
  { data: 'no_lab' },
  { data: 'patient.medrec' },
  { data: 'patient.name' },
  { data: 'room.room' },
  { data: 'cito', render: function(data, type, row) {
    let cito = '';
    if (row.cito == 'cito' || row.cito == 1) {
      cito = '<i class="bi bi-exclamation-triangle-fill text-warning" data-toggle="tooltip" data-placement="top" title="CITO"></i>';
    }
    let analytic = '';
    if (row.status == 1) {
      analytic = '<i class="ms-2 bi bi-check-square-fill text-success" data-toggle="tooltip" data-placement="top" title="Moved to analytic"></i>'
    }

    let igd= '';
    if(row.is_igd == 1){
      igd = `<span style="color:red" data-toggle="tooltip" data-placement="top" title="Ruang IGD"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-pulse" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053.918 3.995.78 5.323 1.508 7H.43c-2.128-5.697 4.165-8.83 7.394-5.857.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17c3.23-2.974 9.522.159 7.394 5.856h-1.078c.728-1.677.59-3.005.108-3.947C13.486.878 10.4.28 8.717 2.01L8 2.748ZM2.212 10h1.315C4.593 11.183 6.05 12.458 8 13.795c1.949-1.337 3.407-2.612 4.473-3.795h1.315c-1.265 1.566-3.14 3.25-5.788 5-2.648-1.75-4.523-3.434-5.788-5Zm8.252-6.686a.5.5 0 0 0-.945.049L7.921 8.956 6.464 5.314a.5.5 0 0 0-.88-.091L3.732 8H.5a.5.5 0 0 0 0 1H4a.5.5 0 0 0 .416-.223l1.473-2.209 1.647 4.118a.5.5 0 0 0 .945-.049l1.598-5.593 1.457 3.642A.5.5 0 0 0 12 9h3.5a.5.5 0 0 0 0-1h-3.162l-1.874-4.686Z"/>
            </svg> </span>`
    }else if(row.is_igd == 0){
        igd = `<span style="color:black" data-toggle="tooltip" data-placement="top" title="Ruang Central"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-c-circle" viewBox="0 0 16 16">
                  <path d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8Zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0ZM8.146 4.992c-1.212 0-1.927.92-1.927 2.502v1.06c0 1.571.703 2.462 1.927 2.462.979 0 1.641-.586 1.729-1.418h1.295v.093c-.1 1.448-1.354 2.467-3.03 2.467-2.091 0-3.269-1.336-3.269-3.603V7.482c0-2.261 1.201-3.638 3.27-3.638 1.681 0 2.935 1.054 3.029 2.572v.088H9.875c-.088-.879-.768-1.512-1.729-1.512Z"/>
                </svg> </span>`
    }
    return "<div>"+cito+' '+analytic+' '+igd+"</div>";
  }, defaultContent: '', responsivePriority: 1}
];

// Datatable Component
var selectedTransactionId;
var DatatablesServerSide = function () {
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
              url: baseUrl('pre-analytics/datatable/')
          },
          columns: columnsDataTable,
          columnDefs: [
              {
                  responsivePriority: 1,
                  targets: buttonActionIndex,
                  data: null,
                  orderable: false,
                  className: 'text-end',
                  searchable: false,
                  render: function (data, type, row) {
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
      dt.on('draw', function () {
          initToggleToolbar();
          toggleToolbars();
          KTMenu.createInstances();
      });

      dt.on('select', function(e, data, type, indexes) {
        const selectedData = data.rows().data()[indexes];
        selectedTransactionId = selectedData.id;
        onSelectTransaction(selectedData);
      });
  }

  // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
  var handleSearchDatatable = function () {
      const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-pre-analytics"]');
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
      },
      getData: function() {
        return dt.rows().data();
      }
  }
}();

var room_class = '0';
var getRoomClass = function () {
  $(".select-room").on('change', function(e) {
    const roomId = $(this).val();
    if (roomId != null && roomId != '') {
      $.ajax({
        url: baseUrl('master/room/edit/'+roomId),
        method: 'GET',
        success: function(res) {
          room_class = res.class;
          const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
          DatatableTestServerSide.refreshNewTable(newUrl);
        }
      })
    } 
  });
}

var DatatableTestServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var selectorName = '.test-datatable-ajax';
  
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
                url: baseUrl('pre-analytics/test/'+room_class+'/datatable'),
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
                        return `<i onClick="addTestList('`+row.unique_id+`','`+row.type+`','`+row.name+`', '`+price+`', event,'`+row.id+`')" class="cursor-pointer bi bi-arrow-right-circle text-success" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Add `+row.name+` to test list"></i>`;
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
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search-test"]');
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

var checkSpecimenDrawStatus = function(transactionId){
  $.ajax({
    url: baseUrl('pre-analytics/specimen-test/is-all-drawn/'+transactionId),
    method: 'GET',
    success: function(res) {
      if (res.all_drawn) {
        $("#undraw-all-btn").show();
        $("#draw-all-btn").hide();    
      } else {
        $("#draw-all-btn").show();
        $("#undraw-all-btn").hide();
      }
    }
  });
}

var checkVerifiedTest = function(transactionId) {
  $.ajax({
    url: baseUrl('pre-analytics/is-verified-test-exists/'+transactionId),
    type: 'get',
    success: function(res) {
      if (res.exists) {
        $("#edit-patient-details-btn").attr('disabled', 'disabled');
        // $("#edit-test-btn").attr('disabled', 'disabled');
        $("#transaction-note").attr('disabled', 'disabled');
        $("#undraw-all-btn").attr('disabled', 'disabled');
        $("#draw-all-btn").attr('disabled', 'disabled');
      } else {
        $("#edit-patient-details-btn").removeAttr('disabled');
        // $("#edit-test-btn").removeAttr('disabled');
        $("#transaction-note").removeAttr('disabled');
        $("#undraw-all-btn").removeAttr('disabled');
        $("#draw-all-btn").removeAttr('disabled');
      }
    }
  })
}

var transactionTestTable;
var transactionSpecimenTable;
var onSelectTransaction = function (selectedData) {
  
  // show all button on details section
  $(".patient-details-btn").removeClass('d-none');

  const patient = selectedData.patient;
  const room = selectedData.room;
  const transactionId = selectedData.id;
  
  goToAnalyticsBtn(transactionId);

  checkSpecimenDrawStatus(transactionId);

  // set transaction id for edit patient details
  $("#edit-patient-details-btn").data('transaction-id', transactionId);

  // set transaction id for cito checkin
  $("#citoCheckin").data('transaction-id', transactionId);

  // set transaction id for final checkin btn
  $("#btn-final-checkin").data('transaction-id', transactionId);

  //set button print barcode
  // $('#btn-print-barcode').attr('href', baseUrl('barcode/' + transactionId))

  // set transaction id for draw modal
  $("#btn-final-draw-all").data('transaction-id', transactionId);
  $("#btn-final-undraw-all").data('transaction-id', transactionId);
  
  $("#draw-all-btn").prop('disabled', false);
  $("#undraw-all-btn").prop('disabled', false);
  $("#draw-all-btn").val(transactionId);
  $("#undraw-all-btn").val(transactionId);

  $(".name-detail").html(patient.name);
  $(".gender-detail").html((patient.gender == 'M' ? 'Laki-laki' : 'Perempuan'));
  $(".email-detail").html(patient.email);
  $(".phone-detail").html(patient.phone);
  $(".age-detail").html(getAgeFull(patient.birthdate));
  $(".insurance-detail").html(selectedData.insurance.name);
  // for check in button
  const autoNolab = (room.auto_nolab == 1 || room.auto_nolab == '1' || room.auto_nolab == true);
  const autoUndraw = (room.auto_undraw == 1 || room.auto_undraw == '1' || room.auto_undrow == true);
  // $("#undraw-all-btn").data('auto-undraw', autoUndraw);
  $("#btn-final-undraw-all").data('auto-undraw', autoUndraw);

  const hasCheckedIn = (selectedData.checkin_time != null && selectedData.checkin_time != '');
  if (hasCheckedIn) {
    $("#check-in-btn").html('No. Lab: ' + selectedData.no_lab);
    $("#check-in-btn").data('has-checked-in', true);
    $("#check-in-btn").prop('disabled', true);
  } else {
    $("#check-in-btn").html('Check in');
    $("#check-in-btn").data('has-checked-in', false);
    $("#draw-all-btn").prop('disabled', true);
    $("#undraw-all-btn").prop('disabled', true);
    $("#check-in-btn").prop('disabled', false);
  }
  $("#check-in-btn").data('transaction-id', transactionId);
  $("#check-in-btn").data('auto-nolab', autoNolab);
  $("#btn-final-checkin").data('auto-nolab', autoNolab);
 


  $("#edit-test-btn").data('transaction-id', transactionId);
  $("#edit-test-btn").data('room-class', room.class);
  // end for check in button

  switch (selectedData.type) {
    case 'rawat_jalan':
       patientType = 'Rawat Jalan';
       break;
    case 'rawat_inap':
      patientType = 'Rawat Inap';
      break;
    case 'igd':
      patientType = 'IGD';
      break;
    case 'rujukan':
      patientType = 'Rujukan';
      break;
    default:
      patientType = '-';
  }

  $(".type-detail").html(patientType);
  $(".room-detail").html(selectedData.room.room);
  $(".medrec-detail").html(patient.medrec);
  $(".doctor-detail").html(selectedData.doctor.name);

  // set the transaction id to note textarea
  $("#transaction-note").data('transaction-id', transactionId);
  $("#transaction-note").val(selectedData.note);
  
  checkVerifiedTest(transactionId);

  autosize.update($("#transaction-note"));
  // Check if #it is a DataTable or not. If not, initialise:, if so, reload with new url
  if ( ! $.fn.DataTable.isDataTable( '.transaction-test-table' ) ) {
    transactionTestTable = $('.transaction-test-table').DataTable({
      paging: false,
      scrollY: '230px',
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      // order: [],
      sort: false,
      stateSave: false,
      ajax: {
          url: baseUrl('pre-analytics/transaction-test/'+transactionId+'/datatable/'),
          complete: function(data) {
            const testData = data.responseJSON.data;
            testData.forEach(function(item){
              $.ajax({
                url: baseUrl('pre-analytics/analyzer-test/'+item.test_id),
                type: 'GET',
                success: function(res) {
                  $("#select-analyzer-"+item.id).html(res);
                  $("#select-analyzer-"+item.id).select2({allowClear:true});
                  $("#select-analyzer-"+item.id).val(item.analyzer_id).trigger('change');
                  $("#select-analyzer-"+item.id).attr('onChange',"analyzerChange("+item.id+","+item.transaction_id+",event)");
                }
              })
            });
          }
      },
      columns: [
        { data: 'id', render: function(data, type, row) {
            if(row.package_id){
                return row.package.name

            }else{
                return row.test.name
            }
          }, defaultContent: ''
        },
        { data: 'test_id', render: function(data, type, row) {
            const selectComponent = `
              <select id="select-analyzer-`+row.id+`" data-control="select2" data-placeholder="Select analyzer" class="select form-select form-select-sm form-select-solid my-0 me-4">
              </select>
            `;
           
            return selectComponent;
            
          }, defaultContent: ''
        }
      ]
    });
  } else {
    transactionTestTable.ajax.url(baseUrl('pre-analytics/transaction-test/'+transactionId+'/datatable/')).load();
  }

  if ( ! $.fn.DataTable.isDataTable( '.transaction-specimen-table' ) ) {
    transactionSpecimenTable = $('.transaction-specimen-table').DataTable({
      paging:false,
      info: false,
      responsive: true,
      searchDelay: 500,
      processing: true,
      serverSide: true,
      // order: [],
      sort: false,
      stateSave: false,
      ajax: {
          url: baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/'),
          complete: function(data) {
            // $.ajax({
            //   url: baseUrl('pre-analytics/is-verified-test-exists/'+transactionId),
            //   type: 'get',
            //   success: function(res) {
            //     if (res.exists) {
            //       $(".specimen-checkbox").attr('disabled', 'disabled');
            //     } else {
            //       $(".specimen-checkbox").removeAttr('disabled');
            //     }
            //   }
            // })
          }
      },
      columns: [
        { data: 'specimen.name', render: function(data, type, row){
            return data;
          } 
        },
        { data: 'volume', render: function(data, type, row) {
            return data;
          }, defaultContent: ''
        },
        { data: 'test_ids', render: function(data, type, row) {
            const checked = row.draw.includes('1') ? 'checked' :'';
            const disabled = ((row.no_lab != null && row.no_lab != '') ? '' : 'disabled');
            const checkboxComponent = `
              <input class="specimen-checkbox undraw-btn" id="specimen-test-`+row.transaction_id+`-`+row.specimen_id+`" type="checkbox" value="`+row.test_ids+`" onChange="drawSpecimenChange(`+row.transaction_id+`,`+row.specimen_id+`,event)" `+checked+` `+disabled+`>
              `;
            return checkboxComponent;
          }, sortable: false, searchable: false
        },
        { data: 'test_ids', render: function(data, type, row) {
          const printIcon = `
            <i class="ms-2 bi bi-printer text-success" id="specimen-test-`+row.transaction_id+`-`+row.specimen_id+`" onClick="printLabelOnClick(`+row.transaction_id+`,`+row.specimen_id+`)" data-toggle="tooltip" data-placement="top" title="Print Label"></i>
            `;
          return printIcon;
        }
      }
      ]
    });
  } else {
    transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')).load();
  }

}

var printLabelOnClick = function (transactionId, specimenId){
  $.ajax({
    url: baseUrl('printBarcode-single-specimen/' + transactionId + '/' + specimenId),
    type: 'GET',
    success: function(res){

    }
  });
}

var deleteTransaction = function (id) {
  Swal.fire({
      title: 'Are you sure?',
      text: 'You will not be able to recover this data!',
      // type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: 'btn btn-secondary'
      }
  }).then(function(isConfirm){
      if(isConfirm.value) {
          $.ajax({
              url: baseUrl('pre-analytics/transaction/delete/'+id),
              method: 'DELETE',
              success: function(res) {
                  DatatablesServerSide.refreshTable();
                  transactionSpecimenTable.ajax.reload();
                  transactionTestTable.ajax.reload();
                  toastr.success(res.message, "Delete Success!");
              },
              error: function(request, status, error){
                  toastr.error(request.responseJSON.message);
              }
          })
      }
  });
}

function analyzerChange(transactionTestId, transactionId, event){
  const analyzerId = event.target.value;
  $.ajax({
    url: baseUrl('pre-analytics/transaction-test/update-analyzer/'+transactionTestId),
    data: {
      analyzer_id: analyzerId
    },
    type: 'POST',
    success: function(res) {
      toastr.success(res.message, "Update analyzer success!");
      goToAnalyticsBtn(transactionId);
    },
    error: function(request, status, error) {
      toastr.error(request.responseJSON.message);
      transactionTestTable.ajax.url(baseUrl('pre-analytics/transaction-test/'+transactionId+'/datatable/')).load();
    }
  })
}

function drawSpecimenChange(transactionId, specimenId, event) {
  if (!event.target.checked) {
    if (!$("#undraw-all-btn").data('auto-undraw')) {
      Swal.fire({
        title: 'Are you sure you want to undraw?',
        text: 'Please input undraw causes',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
          if (reason == '') {
            Swal.showValidationMessage(`Please enter a reason`)
          }
          return { reason: reason }
        },
        allowOutsideClick: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: baseUrl('pre-analytics/specimen-test/update-draw'),
            type: 'POST',
            data: {
              transaction_id: transactionId,
              specimen_id: specimenId,
              test_ids: event.target.value,
              reason: result.value.reason,
              mode: "undraw"
            },
            success: function(res) {
              toastr.success(res.message, "Update draw status success!");
              transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
              checkSpecimenDrawStatus(transactionId);
              goToAnalyticsBtn(transactionId);
            },
            error: function(request, status, error) {
              toastr.error(request.responseJSON.message);
              transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
            }
          });
        } else {
          event.target.checked = true;
        }
      })
      return;
    }
    
    $.ajax({
      url: baseUrl('pre-analytics/specimen-test/update-draw'),
      type: 'POST',
      data: {
        transaction_id: transactionId,
        specimen_id: specimenId,
        test_ids: event.target.value
      },
      success: function(res) {
        toastr.success(res.message, "Update draw status success!");
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
        checkSpecimenDrawStatus(transactionId);
        goToAnalyticsBtn(transactionId);
        DatatablesServerSide.refreshTable();
      },
      error: function(request, status, error) {
        toastr.error(request.responseJSON.message);
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
      }
    });

    return;
  } else {

      Swal.fire({
        title: 'Are you sure you want to draw?',
        text: 'Please input draw causes',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
          if (reason == '') {
            Swal.showValidationMessage(`Please enter a reason`)
          }
          return { reason: reason }
        },
        allowOutsideClick: false
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: baseUrl('pre-analytics/specimen-test/update-draw'),
            type: 'POST',
            data: {
              transaction_id: transactionId,
              specimen_id: specimenId,
              test_ids: event.target.value,
              reason: result.value.reason,
              mode: "draw"
            },
            success: function(res) {
              toastr.success(res.message, "Update draw status success!");
              transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
              checkSpecimenDrawStatus(transactionId);
              goToAnalyticsBtn(transactionId);
              DatatablesServerSide.refreshTable();
            },
            error: function(request, status, error) {
              toastr.error(request.responseJSON.message);
              transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
            }
          });
        } else {
          event.target.checked = false;
        }
        return;
      })
  }


}

var drawAllBtnComponent = function() {
  $("#draw-all-btn").on('click', function(e) {
    const transactionId = $(this).val();
    $.ajax({
      url: baseUrl('pre-analytics/specimen-test/draw-all/1'),
      type: 'POST',
      data: { transaction_id: transactionId },
      success: function(res){
        toastr.success(res.message, 'Success draw all specimen!');
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable')).load();
        checkSpecimenDrawStatus(transactionId);
        goToAnalyticsBtn(transactionId);
        DatatablesServerSide.refreshTable();
      }
    });
  });

  $("#undraw-all-btn").on('click', function(e) {
    const transactionId = $(this).val();

    if (!$("#undraw-all-btn").data('auto-undraw')) {
      Swal.fire({
        title: 'Are you sure you want to undraw?',
        text: 'Please input undraw causes',
        input: 'text',
        inputAttributes: {
          autocapitalize: 'off'
        },
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Submit',
        showLoaderOnConfirm: true,
        preConfirm: (reason) => {
          if (reason == '') {
            Swal.showValidationMessage(`Please enter a reason`)
          }
          return { reason: reason }
        },
        allowOutsideClick: () => !Swal.isLoading()
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: baseUrl('pre-analytics/specimen-test/draw-all/0'),
            type: 'POST',
            data: { transaction_id: transactionId, reason: result.value.reason, mode:"undraw" },
            success: function(res){
              toastr.success(res.message, 'Success undraw all specimen!');
              transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')).load();
              checkSpecimenDrawStatus(transactionId);
              goToAnalyticsBtn(transactionId);
              DatatablesServerSide.refreshTable();
            }
          });
        }
      })
      return false;
    }

    $.ajax({
      url: baseUrl('pre-analytics/specimen-test/draw-all/0'),
      type: 'POST',
      data: { transaction_id: transactionId },
      success: function(res){
        toastr.success(res.message, 'Success undraw all specimen!');
        transactionSpecimenTable.ajax.url(baseUrl('pre-analytics/transaction-specimen/'+transactionId+'/datatable/')).load();
        checkSpecimenDrawStatus(transactionId);
        goToAnalyticsBtn(transactionId);
        DatatablesServerSide.refreshTable();
      }
    });
  });
}


var Select2ServerSideUser = function () {
  var _componentSelect2 = function () {
    // Initialize
    $('.select-user').select2({
      allowClear: true,
      ajax: {
        url: baseUrl('pre-analytics/get-user'),
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            query: params.term // search term
          };
        },
        processResults: function (data, params) {

          // parse the results into the format expected by Select2
          // since we are using custom formatting functions we do not need to
          // alter the remote JSON data, except to indicate that infinite
          // scrolling can be used
          // params.page = params.page || 1;

          return {
              
            results: $.map(data, function (item) {
              var PrefixText = ''
              PrefixText = item.user_id + " - "
              // additionalText = " ["+item.general_code+"]"

              return {
                text: PrefixText + item.name,
                id: item.user_id
              }
            })
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      // minimumInputLength: 0,
      // tags: true, // for create new tags
      language: {
        inputTooShort: function () {
          return 'Input is too short';
        },
        errorLoading: function () {
          return `There's error on our side`;
        },
        noResults: function () {
          return 'There are no result based on your search';
        }
      }

    });

  }

  //
  // Return objects assigned to module
  //

  return {
    init: function () {
      _componentSelect2();
    }
  }
}

function getAge(dateString) {
  var today = new Date();
  var birthDate = new Date(dateString);
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
  }

  return age;
}
function getAgeFull(dateString) {
  var now = new Date();
  var today = new Date(now.getYear(), now.getMonth(), now.getDate());

  var yearNow = now.getYear();
  var monthNow = now.getMonth();
  var dateNow = now.getDate();
  var dob = new Date(dateString);

  var yearDob = dob.getYear();
  var monthDob = dob.getMonth();
  var dateDob = dob.getDate();
  var age = {};
  var ageString = "";
  var yearString = "";
  var monthString = "";
  var dayString = "";


  yearAge = yearNow - yearDob;

  if (monthNow >= monthDob)
    var monthAge = monthNow - monthDob;
  else {
    yearAge--;
    var monthAge = 12 + monthNow - monthDob;
  }

  if (dateNow >= dateDob)
    var dateAge = dateNow - dateDob;
  else {
    monthAge--;
    var dateAge = 31 + dateNow - dateDob;

    if (monthAge < 0) {
      monthAge = 11;
      yearAge--;
    }
  }

  age = {
    years: yearAge,
    months: monthAge,
    days: dateAge
  };

  if (age.years > 1) yearString = "Y";
  else yearString = "Y";
  if (age.months > 1) monthString = "M";
  else monthString = "M";
  if (age.days > 1) dayString = "D";
  else dayString = "D";


  if ((age.years > 0) && (age.months > 0) && (age.days > 0))
    ageString = age.years + yearString + "/" + age.months + monthString + "/" + age.days + dayString + "";
  else if ((age.years == 0) && (age.months == 0) && (age.days > 0))
    ageString = "/" + age.days + dayString + "";
  else if ((age.years > 0) && (age.months == 0) && (age.days == 0))
    ageString = age.years + yearString + "";
  else if ((age.years > 0) && (age.months > 0) && (age.days == 0))
    ageString = age.years + yearString + "/" + age.months + monthString + "";
  else if ((age.years == 0) && (age.months > 0) && (age.days > 0))
    ageString = age.months + monthString + "/" + age.days + dayString + "";
  else if ((age.years > 0) && (age.months == 0) && (age.days > 0))
    ageString = age.years + yearString + "/" + age.days + dayString + "";
  else if ((age.years == 0) && (age.months > 0) && (age.days == 0))
    ageString = age.months + monthString + "";
  else ageString = "Oops! Could not calculate age!";

  return ageString;
}

var DateRangePicker = () => {
  var start = moment();
  var end = moment();

  function cb(start, end) {
      $("#daterange-picker").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
      const startDate = $("#daterange-picker").data('daterangepicker').startDate.format('YYYY-MM-DD');
      const endDate = $("#daterange-picker").data('daterangepicker').endDate.format('YYYY-MM-DD');
  }

  $("#daterange-picker").daterangepicker({
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

  $("#daterange-picker").on('change', function () {
    const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
    const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');
    const url = baseUrl('pre-analytics/datatable/'+startDate+'/'+endDate);
    DatatablesServerSide.refreshTableAjax(url);
  });
}

var Select2ServerSideModal = function (theData, searchKey = 'name', params) {
  var _componentSelect2 = function() {
      // Initialize
       $('.select-' + theData).select2({
          allowClear: true,
          ajax: {
              url: baseUrl('master/select-options/' + theData + '/' + searchKey + (params ? '/' + params : '')),
              dataType: 'json',
              delay: 250,
              data: function (params) {
                  return {
                      query: params.term // search term
                  };
              },
              processResults: function (data, params) {

                  // parse the results into the format expected by Select2
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data, except to indicate that infinite
                  // scrolling can be used
                  // params.page = params.page || 1;

                return {
                    results: $.map(data, function(item){
                        var additionalText = '';
                        var PrefixText = '';
                        if (theData == 'room') {
                            additionalText = `<i> Class `+item.class+`</i>`;
                        }
                        if ((theData == 'patient')) {
                            let medrec = item.medrec;
                            let pad = "";
                            // for (var i = 0; i < 20-medrec.length; i++) {
                            //     pad += "0";
                            // }
                            PrefixText = medrec+pad+" - "
                        }  
                        return {
                            text:PrefixText + item.name + additionalText,
                            id: item.id
                        }
                    })
                };
              },
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          // minimumInputLength: 0,
          // tags: true, // for create new tags
          language: {
              inputTooShort: function () {
                  return 'Input is too short';
              },
              errorLoading: function () {
                  return `There's error on our side`;
              },
              noResults: function () {
                  return 'There are no result based on your search';
              }
          },
          dropdownParent: $("#add-patient-modal .modal-body")
      });

  }

  //
  // Return objects assigned to module
  //

  return {
      init: function() {
          _componentSelect2();
      }
  }
}

var Select2ServerSideEditModal = function (theData, searchKey = 'name', params) {
  var _componentSelect2 = function() {
      // Initialize
       $('.select-' + theData + '-edit').select2({
          allowClear: true,
          ajax: {
              url: baseUrl('master/select-options/' + theData + '/' + searchKey + (params ? '/' + params : '')),
              dataType: 'json',
              delay: 250,
              data: function (params) {
                  return {
                      query: params.term // search term
                  };
              },
              processResults: function (data, params) {

                  // parse the results into the format expected by Select2
                  // since we are using custom formatting functions we do not need to
                  // alter the remote JSON data, except to indicate that infinite
                  // scrolling can be used
                  // params.page = params.page || 1;

                return {
                    results: $.map(data, function(item){
                        var additionalText = '';
                        var PrefixText = '';
                        if (theData == 'room') {
                            additionalText = `<i> Class `+item.class+`</i>`;
                        }
                        if ((theData == 'patient')) {
                            let medrec = item.medrec;
                            let pad = "";
                            // for (var i = 0; i < 20-medrec.length; i++) {
                            //     pad += "0";
                            // }
                            PrefixText = medrec+pad+" - "
                        }  
                        return {
                            text:PrefixText + item.name + additionalText,
                            id: item.id
                        }
                    })
                };
              },
              cache: true
          },
          escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
          // minimumInputLength: 0,
          // tags: true, // for create new tags
          language: {
              inputTooShort: function () {
                  return 'Input is too short';
              },
              errorLoading: function () {
                  return `There's error on our side`;
              },
              noResults: function () {
                  return 'There are no result based on your search';
              }
          },
          dropdownParent: $("#edit-patient-details-modal .modal-body")
      });

  }

  //
  // Return objects assigned to module
  //

  return {
      init: function() {
          _componentSelect2();
      }
  }
}

var addNewPatient = () => {
  $(".add-new-patient").addClass('d-none');
  $(".cancel-new-patient").removeClass('d-none');

  $(newFormId + " .patient-form input").removeAttr('disabled');
  $(newFormId + " .patient-form input").removeAttr('readonly');
  $(newFormId + " .patient-form textarea").removeAttr('disabled');
  $(newFormId + " .patient-form textarea").removeAttr('readonly');

  $(".patient-form label").removeClass('text-muted');
   // Reset the patient form
   $(newFormId + ' .patient-form input[type="text"]').val('');
   $(newFormId + ' .patient-form textarea').val('');
   $(newFormId + ' .patient-form .invalid-feedback').remove();

  $("select[name='patient_id']").val('').trigger('change');
  $("select[name='patient_id']").prop('disabled', true);
  areAllFilled();

}

var cancelNewPatient = () => {
  $(".add-new-patient").removeClass('d-none');
  $(".cancel-new-patient").addClass('d-none');

  // disabled all the patient form
  $(newFormId + " .patient-form input").attr('disabled', true);
  $(newFormId + " .patient-form input").attr('readonly', true);
  $(newFormId + " .patient-form textarea").attr('disabled', true);
  $(newFormId + " .patient-form textarea").attr('readonly', true);

  // mute all label on patient form
  $(".patient-form label").addClass('text-muted');

  $("select[name='patient_id']").prop('disabled', false);

  // Reset the patient form
  $(newFormId + ' .patient-form input[type="text"]').val('');
  $(newFormId + ' .patient-form textarea').val('');
  $(newFormId + ' .patient-form .invalid-feedback').remove();
  areAllFilled();
}

var selectedTestIds = [];
var addTestList = function(unique_id, type, name, price, event, test_id) {
  selectedTestIds.push(unique_id);
  const isEven = (selectedTestIds.length % 2 == 0);
  const priceFormatted = (price != 'null' && price != '' && price != null) ? 'Rp'+price.toLocaleString('ID') : '';
  $("#selected-test tr:last").after(`
    <tr class="`+(isEven == true ? 'even':'odd')+`">
      <td>`+test_id+` - `+name+`</td>
      <td>`+priceFormatted+`</td>
      <td>`+type+`</td>
      <td class="text-end">
      <i onClick="removeTestList('`+unique_id+`', event)" class="cursor-pointer bi bi-arrow-left-circle text-danger" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-dark" data-bs-placement="top" title="Remove `+name+` to test list"></i>
      </td>
    </tr>
  `);
  event.target.closest('tr').remove();
  $("#selected-test-ids").val(selectedTestIds.join(','));
  const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable/withoutId/'+selectedTestIds.join(','));
  DatatableTestServerSide.refreshNewTable(newUrl);
}

var removeTestList = function(unique_id, event) {
  event.target.closest('tr').remove();
  let indexRemove = selectedTestIds.indexOf(unique_id);
  selectedTestIds.splice(indexRemove, 1);
  $("#selected-test-ids").val(selectedTestIds.join(','));
  if (selectedTestIds.length > 0) {
    const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable/withoutId/'+selectedTestIds.join(','));
    DatatableTestServerSide.refreshNewTable(newUrl);
  } else {
    const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
    DatatableTestServerSide.refreshNewTable(newUrl);
  }
}

var Stepper = () => {
   // Stepper lement
   var element = document.querySelector("#kt_stepper_example_basic");

   // Initialize Stepper
   var stepper = new KTStepper(element);
 
   // Handle next step
   stepper.on("kt.stepper.next", function (stepper) {
      $(newFormId).validate();
      if ($(newFormId).valid()) {
        stepper.goNext(); // go next step
        $(".stepper-number-2").addClass('text-white');
        $(".stepper-number-2").removeClass('text-primary');
        $(".stepper-icon-2").addClass('bg-primary');
      }
   });
 
   // Handle previous step
   stepper.on("kt.stepper.previous", function (stepper) {
       stepper.goPrevious(); // go previous step
        $(".stepper-number-2").removeClass('text-white');
        $(".stepper-number-2").addClass('text-primary');
        $(".stepper-icon-2").removeClass('bg-primary');
        $("#selected-test").html('<tr></tr>'); // remove body of table on selected test
        selectedTestIds = []; // set the selected test id to empty

        // refresh the test table because did the back button
        const newUrl = baseUrl('pre-analytics/test/'+room_class+'/datatable');
        DatatableTestServerSide.refreshNewTable(newUrl);
   });
}

var datepicker;
var birthdate = () => {
  const thisYear = new Date().getFullYear();  // returns the current year
  datepicker = $(".birthdate").flatpickr({
    altInput: true,
    altFormat: 'j F Y',
    dateFormat: 'Y-m-d',
    static: true
  });
  datepicker.jumpToDate(new Date(thisYear-20, 0, 1));
}

var createdTimeDatePicker;
var createdTime = () => {
  createdTimeDatePicker = $(".created-time").flatpickr({
    altInput: true,
    altFormat: 'j F Y',
    dateFormat: 'Y-m-d',
    static: true
  });
}

var automaticFillPatientForm = function() {
  $(".select-patient").on('change', function (e) {
    var patientId = $(this).val();
    if (patientId != '' && patientId != null) {
      $.ajax({
        url: baseUrl('master/patient/edit/'+patientId),
        method: 'GET',
        success: function(res) {
          $(".patient-form input[name='name']").val(res.name);
          $(".patient-form input[name='email']").val(res.email);
          $(".patient-form input[name='phone']").val(res.phone);
          $(".patient-form input[name='medrec']").val(res.medrec);
          datepicker.setDate(res.birthdate);
          $(".patient-form input[name='gender']").prop('disabled', false);
          $(".patient-form input[name='gender']").prop('readonly', false);
          $(".patient-form input[name='gender'][value='"+res.gender+"']").trigger('click');
          $(".patient-form input[name='gender']").prop('disabled', true);
          $(".patient-form input[name='gender']").prop('readonly', true);
          $(".patient-form textarea[name='address']").val(res.address);
          areAllFilled();
        }
      });
    } else {
      $(".patient-form input.req-input").val('');
      $(".patient-form input[name='email']").val('');
      $(".patient-form input[name='phone']").val('');
      $(".patient-form textarea").val('');
      areAllFilled();
    }
    
  });
}

var areAllFilled = function() {
  var filled = true;
  $('.patient-right-form select.req-input').each(function() {
      if($(this).val() == '' || $(this).val() == null) {
        filled = false;
      }
  });

  $(".patient-form input.req-input").each(function() {
    if($(this).val() == '' || $(this).val() == null) {
      filled = false;
    }
  });
  
  if (filled == true) {
    $("#continue-btn").prop('disabled', false);
  } else {
    $("#continue-btn").prop('disabled', true);
  }
}

// required for the form validation rules
var rulesFormValidation = {
  name: {
    required: true
  },
  email: {
    email: true
  },
  phone: {
    digits: true
  },
  birthdate: {
    required: true
  },
  gender: {
    required: true
  }
};
// Form Validation Component
var FormValidation = function() {
  //
  // Setup module components
  //

  // Validation config
  var _componentValidation = function() {
      if (!$().validate) {
          console.warn('Warning - validate.min.js is not loaded.');
          return;
      }

      // Initialize
      $('#form-create').validate({
          ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
          errorClass: 'fv-plugins-message-container invalid-feedback',
          successClass: 'validation-valid-label',
          validClass: 'validation-valid-label',
          highlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          unhighlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          // success: function(label) {
          //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
          // },

          // Different components require proper error label placement
          errorPlacement: function(error, element) {

              // Unstyled checkboxes, radios
              if (element.parents().hasClass('form-check')) {
                  error.appendTo( element.parents('.form-check').parent() );
              }

              // Input with icons and Select2
              else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                  error.appendTo( element.parent() );
              }

              // Input group, styled file input
              else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                  error.appendTo( element.parent().parent() );
              }

              // Other elements
              else {
                  error.insertAfter(element);
              }
          },
          rules: rulesFormValidation,
          messages: {
              custom: {
                  required: 'This is a custom error message'
              }
          },
          // submitHandler: function(form, event) {
          // }
      });

      $(newFormId).validate({
          ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
          errorClass: 'fv-plugins-message-container invalid-feedback',
          successClass: 'validation-valid-label',
          validClass: 'validation-valid-label',
          highlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          unhighlight: function(element, errorClass) {
              $(element).removeClass(errorClass);
          },
          // success: function(label) {
          //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
          // },

          // Different components require proper error label placement
          errorPlacement: function(error, element) {

              // Unstyled checkboxes, radios
              if (element.parents().hasClass('form-check')) {
                  error.appendTo( element.parents('.form-check').parent() );
              }

              // Input with icons and Select2
              else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
                  error.appendTo( element.parent() );
              }

              // Input group, styled file input
              else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
                  error.appendTo( element.parent().parent() );
              }

              // Other elements
              else {
                  error.insertAfter(element);
              }
          },
          rules: rulesFormValidation,
          messages: {
              custom: {
                  required: 'This is a custom error message'
              }
          },
          // submitHandler: function(form, event) {
          // }
      });

      // Reset form
      // $('#reset').on('click', function() {
      //     validatorCreate.resetForm();
      // });
  };


  //
  // Return objects assigned to module
  //

  return {
      init: function() {
          _componentValidation();
      }
  }
}();

var createNewData = function() {
  let formData = $(newFormId).serialize();
  let theForm = $(newFormId);
  $.ajax({
    url: baseUrl('pre-analytics/create'),
    method: 'POST',
    data: formData,
    success: function(res) {
        toastr.success(res.message, "Create Success!"); // give notification
        $("#add-patient-modal").modal('hide'); // hide the modal
        $(newFormId).trigger('reset'); // reset the form
        $(newFormId + ' .patient-right-form select').val('').trigger('change'); // reset the select form manually
        $("select[name='patient_id']").val('').trigger('change'); // reset the patient select manually
        $("#selected-test").html('<tr></tr>'); // remove body of table
        selectedTestIds = []; // set the selected test id to empty
        $("#back-btn").trigger('click'); // click back manually on stepper
        DatatablesServerSide.refreshTable();
        $("#select-room").prop('disabled', true);
        createdTime();
    },
    error: function (request, status, error) {
        toastr.error(request.responseJSON.message);
    }
  })
}

// Synchronize
var syncBtn = function () {
  $("#sync-btn").on('click', function(e) {
   $('#cover-spin').show();

    $.ajax({
      url: baseUrl('synchronize/sync-from-server/'),
      type: 'get',
      success: function() {
        DatatablesServerSide.refreshTable();
        $('#cover-spin').hide();
      }
    })

  });
}

var citoCheckin = function(){
  $('#citoCheckin').click(function () {

    const transactionId = $(this).data('transaction-id');

    if ($(this).prop('checked')) {
      $.ajax({
        url: baseUrl('pre-analytics/update-cito-checkin'),
        type: 'put',
        data: { 
          transaction_id: transactionId,
          method: "cito"
        },
        success: function(res){
          
        }
      });
   }else {
      $.ajax({
        url: baseUrl('pre-analytics/update-cito-checkin'),
        type: 'put',
        data: { 
          transaction_id: transactionId,
          method: "unCito"
        },
        success: function(res){
          
        }
      });
   }
})
}

var checkInBtn = function () {
  $("#check-in-btn").on('click', function(e) {
    if ($(this).data('has-checked-in')) {
      return;
    }
    const transactionId = $(this).data('transaction-id');

    $('#checkin-modal').modal('show');
    
    if ($(this).data('auto-nolab')) {
      $("#no-lab-checkin-options").addClass('hidden');
    } else {
      $("#no-lab-checkin-options").removeClass('hidden');
    }
    // if ($(this).data('auto-nolab')) {
    //   $.ajax({
    //     url: baseUrl('pre-analytics/check-in/0'),
    //     type: 'POST',
    //     data: {
    //       transaction_id: transactionId
    //     },
    //     success: function(res) {
    //       toastr.success(res.message, 'Patient successfully checked-in!');
    //       $("#check-in-btn").html('No. Lab: ' + res.no_lab);
    //       $("#check-in-btn").prop('disabled',true);

    //       DatatablesServerSide.refreshTable();
    //       transactionSpecimenTable.ajax.reload();
    //       transactionTestTable.ajax.reload();
    //       $(".draw-btn").prop('disabled', false);
    //       goToAnalyticsBtn(transactionId);
    //     }
    //   })
    // } else {
    //   Swal.fire({
    //     title: 'Set No. Lab',
    //     html: `<input type="text" id="no-lab" class="swal2-input" placeholder="No. Lab" minLength="3" maxLength="3">`,
    //     buttonsStyling: false,
    //     confirmButtonText: 'Check In',
    //     allowEnterKey: true,
    //     focusConfirm: false,
    //     preConfirm: () => {
    //       const noLab = Swal.getPopup().querySelector('#no-lab').value;
    //       if (!noLab) {
    //         Swal.showValidationMessage(`Please enter No. Lab`)
    //       }
    //       let isnum = /^\d+$/.test(noLab);
    //       if (!isnum) {
    //         Swal.showValidationMessage(`Please enter a Valid No. Lab`)
    //       }
    //       return { noLab: noLab }
    //     },
    //     customClass: {
    //         confirmButton: "btn btn-primary",
    //         title: "font-weight-bold"
    //     }
    //   }).then((result) => {
    //     $.ajax({
    //       url: baseUrl('pre-analytics/check-in/1'),
    //       type: 'POST',
    //       data: {
    //         transaction_id: transactionId,
    //         no_lab: result.value.noLab
    //       },
    //       success: function(res) {
    //         toastr.success(res.message, 'Patient successfully checked-in!');
    //         $("#check-in-btn").html('No. Lab: ' + res.no_lab);
    //         DatatablesServerSide.refreshTable();
    //         transactionSpecimenTable.ajax.reload();
    //         transactionTestTable.ajax.reload();
    //         $(".draw-btn").prop('disabled', false);
    //         goToAnalyticsBtn(transactionId);
    //       },
    //       error: function (request, status, error) {
    //         toastr.error(request.responseJSON.message);
    //       }
    //     })
    //   });
    // }
  });
}

$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

// this is for document dynamically binding element event handlers
$(document).on('keyup', function (e) {
  var target = $(e.target);
  if (target.is('#no-lab')) {
    if (e.key === 'Enter') {
      $(".swal2-confirm").trigger('click');
    }
  }
});

$(document).on('click', function(e) {
  var target = $(e.target);
  if (target.is('.swal2-confirm')) {
  }
 
});

$(document).on('select2:unselecting', function(e) {
  var target = $(e.target);
  if (target.is('select')) {
    $(target).val('').trigger('change');
    $(target + " option[selected]").removeAttr('selected');
    e.preventDefault();
  }
});

var transactionNote = function () {
  $("#transaction-note").on('blur', function(){
    const transactionId = $(this).data('transaction-id');
    const note = $(this).val();
    $.ajax({
      url: baseUrl('pre-analytics/transaction/note/update'),
      method: 'post',
      data: {
        transaction_id: transactionId,
        note: note
      },
      success: function(res) {
        toastr.success(res.message);
        DatatablesServerSide.refreshTable();
      }
    })
  });
}
// end for document dynamically binding element event handlers

var newPatientMedrec = function() {
  $("#new-patient-medrec").on('blur', function() {
    const value = $(this).val();
    if (value != '') {
      $.ajax({
        url: baseUrl('pre-analytics/check-medical-record/'+value),
        method: 'get',
        error: function(request, status, error){
          toastr.error(request.responseJSON.message);
          $("#new-patient-medrec").trigger('focus');
        }
      })
    }
  });
}

var editPatientDetails = function() {
  $("#edit-patient-details-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('pre-analytics/edit-patient-details/'+transactionId),
      method: 'get',
      success: function(res) {
        $("#form-edit-patient-details input[name='id']").val(res.id);
        $("#form-edit-patient-details select[name='insurance_id']").html(
            `<option value='`+res.insurance_id+`' selected>`+ res.insurance.name +`</option>`
        );
        $("#form-edit-patient-details select[name='type']").val(res.type).trigger('change');
        $("#form-edit-patient-details select[name='room_id']").html(
          `<option value='`+res.room_id+`' selected>` + res.room.room + `</option>`
        );
        $("#form-edit-patient-details select[name='doctor_id']").html(
          `<option value='`+res.doctor_id+`' selected>` + res.doctor.name + `</option>`
        );
        if (res.cito) {
          $("#form-edit-patient-details input[name='cito']").prop('checked', true);
        } else {
          $("#form-edit-patient-details input[name='cito']").prop('checked', false);
        }
        
        $("#form-edit-patient-details textarea[name='diagnosis']").html(res.note);
        $("#form-edit-patient-details textarea[name='diagnosis']").val(res.note);
      }
    })
    $("#edit-patient-details-modal").modal('show');
  });

  // Initialize
  $('#form-edit-patient-details').validate({
    ignore: 'input[type=hidden], .select2-search__field, .ignore-this', // ignore hidden fields
    errorClass: 'fv-plugins-message-container invalid-feedback',
    successClass: 'validation-valid-label',
    validClass: 'validation-valid-label',
    highlight: function(element, errorClass) {
        $(element).removeClass(errorClass);
    },
    unhighlight: function(element, errorClass) {
        $(element).removeClass(errorClass);
    },
    // success: function(label) {
    //     label.addClass('validation-valid-label').text('Valid'); // remove to hide Success message
    // },

    // Different components require proper error label placement
    errorPlacement: function(error, element) {

        // Unstyled checkboxes, radios
        if (element.parents().hasClass('form-check')) {
            error.appendTo( element.parents('.form-check').parent() );
        }

        // Input with icons and Select2
        else if (element.parents().hasClass('form-group-feedback') || element.hasClass('select2-hidden-accessible')) {
            error.appendTo( element.parent() );
        }

        // Input group, styled file input
        else if (element.parent().is('.uniform-uploader, .uniform-select') || element.parents().hasClass('input-group')) {
            error.appendTo( element.parent().parent() );
        }

        // Other elements
        else {
            error.insertAfter(element);
        }
    },
    rules: {
      insurance_id: {
        required: true
      },
      type: {
        required: true
      },
      room_id: {
        required: true
      },
      doctor_id: {
        required: true
      }
    },
    messages: {
        custom: {
            required: 'This is a custom error message'
        }
    },
    // submitHandler: function(form, event) {
    // }
});

  $("#form-edit-patient-details").on('submit', function(e) {
    e.preventDefault();
    if ($(this).valid()) {
        let theForm = $("#form-edit-patient-details");
        let formData = $("#form-edit-patient-details").serialize();

        $.ajax({
          url: baseUrl('pre-analytics/update-patient-details'),
          data: formData,
          type: 'put',
          success: function(res) {
            toastr.success(res.message);
            theForm.trigger('reset'); // reset the edit patient details form
            $("#edit-patient-details-modal").modal('hide'); // hide the modal
            DatatablesServerSide.refreshTable(); // refresh the transaction table
            onSelectTransaction(res.data); // refresh the patient details data
          },
          error: function(request, status, error) {
            toastr.error(request.responseJSON.message);
          }
        });
    }
    // editData(e);
});
}

var selectType = function() {
  $("#select-type").on('change', function () {
    if ($(this).val()) {
      const roomType = $(this).val();
      $(".select-room").prop('disabled', false);
      $("#select-room").select2('destroy');
      $("#select-room").val('').trigger('change');
      Select2ServerSideModal('room','room', roomType).init();
    }
  });

  $("#select-type-edit").on('change', function () {
    if ($(this).val()) {
      const roomType = $(this).val();
      $(".select-room-edit").prop('disabled', false);
      $("#select-room-edit").select2('destroy');
      $("#select-room-edit").val('').trigger('change');
      Select2ServerSideEditModal('room','room', roomType).init();
    }
  });
}

// barcode print
var printBarcode = function(transactionId){
  // alert(transactionId);
  $.ajax({
      url: baseUrl('printBarcode-label/' + transactionId),
      type: 'GET',
      success: function(res){
        
      }
    });
}

var goToAnalyticsBtn = function(transactionId) {
  $.ajax({
    url: baseUrl('pre-analytics/go-to-analytics-btn/'+transactionId),
    type: 'get',
    success: function(res) {
      $("#go-to-analytics-btn").off();
      console.log(res.message);
      if (res.valid) {
        $("#go-to-analytics-btn").prop('disabled', false);
        $("#go-to-analytics-btn").on('click', function() {
          $.ajax({
            url: baseUrl('pre-analytics/go-to-analytics'),
            type: 'put',
            data: { transaction_id: transactionId },
            success: function(result) {
              toastr.success(result.message);
              goToAnalyticsBtn(transactionId);
              DatatablesServerSide.refreshTable();
            },
            error: function(request, status, error) {
              toastr.error(request.responseJSON.message);
            }
          })
        });
      } else {
        $("#go-to-analytics-btn").prop('disabled', true);
      }
    }
  });
}

var refreshTable = function(){
  DatatablesServerSide.refreshTable();
}

var moveToAnalytics = function() {
  DatatablesServerSide.refreshTable();
  let transactions = DatatablesServerSide.getData();
  let tableBody = '';
  for (var i = 0; i < transactions.length; i++) {
    let labNo = transactions[i].no_lab ? transactions[i].no_lab : '';
    let move = transactions[i].able_to_analytics ? `<input type="checkbox" name="transaction_ids[]" class="form-check-input" value="`+transactions[i].id+`" data-transaction-id="`+transactions[i].id+`"></input>` : '<span class="text text-danger">Draw incomplete</span>'
    if (transactions[i].moved_to_analytics) {
      move = '<span class="text text-success">Moved to Analytics</span>';
    }
    tableBody += `
      <tr>
        <td>`+theFullDate(transactions[i].created_time)+`</td>
        <td>`+transactions[i].transaction_id_label+`</td>
        <td>`+labNo+`</td>
        <td>`+transactions[i].patient.medrec+`</td>
        <td>`+transactions[i].patient.name+`</td>
        <td>`+transactions[i].room.room+`</td>
        <td class='text-end'>`+move+`</td>
      </tr>
    `
  }

  $(".move-to-analytics-table tbody").html(tableBody);
  $("#move-to-analytics-modal").modal('show');
}

var moveToAnalyticsBulk = function() {
  let moveToAnalyticsForm = $("#move-to-analytics-bulk-form")
    let formData = moveToAnalyticsForm.serialize();

    $.ajax({
      url: baseUrl('pre-analytics/go-to-analytics-bulk'),
      method: 'POST',
      data: formData,
      success: function(res) {
          toastr.success(res.message, "Move Success!"); // give notification
          $("#move-to-analytics-modal").modal('hide');
          DatatablesServerSide.refreshTable();
      },
      error: function (request, status, error) {
          toastr.error(request.responseJSON.message);
      }
    })
}

var finalCheckinBtn = function (){
  $("#btn-final-checkin").on('click', function(e) {
   
    const transactionId = $(this).data('transaction-id');
    $("#no-lab-checkin-options").removeClass('hidden');
    const noLabCheckin = $("#no-lab-checkin").val();

    if ($(this).data('auto-nolab')) {
      $.ajax({
        url: baseUrl('pre-analytics/check-in/0'),
        type: 'POST',
        data: {
          transaction_id: transactionId
        },
        success: function(res) {
          toastr.success(res.message, 'Patient successfully checked-in!');
          $("#check-in-btn").html('No. Lab: ' + res.no_lab);
          $("#check-in-btn").prop('disabled',false);
          $("#checkin-modal").modal('hide');
          $('#citoCheckin').prop('checked', false);

          DatatablesServerSide.refreshTable();
          transactionSpecimenTable.ajax.reload();
          transactionTestTable.ajax.reload();
          $(".draw-btn").prop('disabled', false);
          goToAnalyticsBtn(transactionId);
        }
      })
    } else {
      // debugger
      
      // Swal.fire({
      //   title: 'Set No. Lab',
      //   html: `<input type="text" id="no-lab" class="swal2-input" placeholder="No. Lab" minLength="3" maxLength="3" style="z-index: 999">`,
      //   buttonsStyling: false,
      //   confirmButtonText: 'Check In',
      //   allowEnterKey: true,
      //   focusConfirm: false,
      //   preConfirm: () => {
      //     const noLab = Swal.getPopup().querySelector('#no-lab').value;
      //     if (!noLab) {
      //       Swal.showValidationMessage(`Please enter No. Lab`)
      //     }
      //     let isnum = /^\d+$/.test(noLab);
      //     if (!isnum) {
      //       Swal.showValidationMessage(`Please enter a Valid No. Lab`)
      //     }
      //     return { noLab: noLab }
      //   },
      //   customClass: {
      //       confirmButton: "btn btn-primary",
      //       title: "font-weight-bold"
      //   }
      // }).then((result) => {
      //   $.ajax({
      //     url: baseUrl('pre-analytics/check-in/1'),
      //     type: 'POST',
      //     data: {
      //       transaction_id: transactionId,
      //       no_lab: result.value.noLab
      //     },
      //     success: function(res) {
      //       toastr.success(res.message, 'Patient successfully checked-in!');
      //       $("#check-in-btn").html('No. Lab: ' + res.no_lab);
      //       DatatablesServerSide.refreshTable();
      //       transactionSpecimenTable.ajax.reload();
      //       transactionTestTable.ajax.reload();
      //       $(".draw-btn").prop('disabled', false);
      //       goToAnalyticsBtn(transactionId);
      //     },
      //     error: function (request, status, error) {
      //       toastr.error(request.responseJSON.message);
      //     }
      //   })
      // });
      $.ajax({
        url: baseUrl('pre-analytics/check-in/1'),
        type: 'POST',
        data: {
          transaction_id: transactionId,
          no_lab: noLabCheckin
        },
        success: function(res) {
          toastr.success(res.message, 'Patient successfully checked-in!');
          $("#check-in-btn").html('No. Lab: ' + res.no_lab);
          DatatablesServerSide.refreshTable();
          transactionSpecimenTable.ajax.reload();
          transactionTestTable.ajax.reload();
          $(".draw-btn").prop('disabled', false);
          goToAnalyticsBtn(transactionId);
        },
        error: function (request, status, error) {
          toastr.error(request.responseJSON.message);
        }
      })
    }

  });
}

var setDefaultTransaction = function() {
  $("#add-patient-modal-btn").on('click', function() {
    $.ajax({
      url: baseUrl('pre-analytics/get-default-transaction'),
      type: 'GET',
      success: function(res) {
        var selectedInsurance = new Option(res.insurance.name, res.insurance.id, false, true);
        var selectedRoom = new Option(res.room.room, res.room.id, false, true);
        var selectedDoctor = new Option(res.doctor.name, res.doctor.id, false, true);

        $("select.select-room-type").val(res.room.type).trigger('change');
        $("select.select-insurance").append(selectedInsurance).trigger('change');
        $("select.select-room").append(selectedRoom).trigger('change');
        $("select.select-doctor").append(selectedDoctor).trigger('change');
      },
      error: function (request, status, error) {
  
      }
    })
  });
 
}


// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DatatablesServerSide.init();
  DatatableTestServerSide.init();
  FormValidation.init();
  getRoomClass();
  automaticFillPatientForm();
  DateRangePicker();
  Select2ServerSideModal('patient').init();
  Select2ServerSideModal('insurance').init();
  Select2ServerSideModal('room','room', '').init();
  Select2ServerSideModal('doctor').init();
  Select2ServerSideUser('user').init();

  Select2ServerSideEditModal('insurance').init();
  Select2ServerSideEditModal('room','room', '').init();
  Select2ServerSideEditModal('doctor').init();
  Stepper();
  birthdate();
  createdTime();
  drawAllBtnComponent();
  syncBtn();
  citoCheckin();
  checkInBtn();
  finalCheckinBtn();
  transactionNote();
  newPatientMedrec();
  editPatientDetails();
  selectType();
  setDefaultTransaction();

  $(".patient-form label").addClass('text-muted');

  $(".patient-right-form select.req-input").on('change', function(e) {
    areAllFilled();
  });

  $(".patient-form input.req-input").on('change keyup input', function(e) {
    areAllFilled();
  });

  $(newFormId).on('submit', function(e) {
    e.preventDefault();
    if (selectedTestIds.length == 0) {
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

    if ($(this).valid()) {
      createNewData();
    }
  });

  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });

  // barcode print
  $("#btn-print-barcode").on('click', function(e) {
    printBarcode(selectedTransactionId);
  });

  // $('#btn-print-barcode').printPage();
});