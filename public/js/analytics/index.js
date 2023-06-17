var baseUrl = function(url) {
  return base + url;
}

var getAge = function(dateString) {
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
    var group_id = $("select[name='group_id']").val()

    const url = baseUrl('analytics/datatable/'+startDate+'/'+endDate+'/'+group_id);
    DatatableAnalytics.refreshTableAjax(url);
  });

  $("#group_id").on('change', function () {
    $("#daterange-picker").trigger('change')
  });
}

var refreshPatientDetails = (data, transactionId) => {
  $(".name-detail").html(data.patient.name);
  $(".gender-detail").html((data.patient.gender == 'M' ? 'Laki-laki' : 'Perempuan'));
  $(".email-detail").html(data.patient.email);
  $(".age-detail").html(getAgeFull(data.patient.birthdate));
  $(".insurance-detail").html(data.insurance.name);
  $('#confirm-print-all-test').on('click', function(){
    printAndGotoAnalytic(transactionId)
  })
  // $('#verify-all-btn').data('transaction-id', transactionId);
  // $('#unverify-all-btn').data('transaction-id', transactionId);
  // $('#validate-all-btn').data('transaction-id', transactionId);
  // $('#unvalidate-all-btn').data('transaction-id', transactionId);
  $(".test-data-action").removeClass('d-none');
  $("#print-all-test").removeClass('d-none');
  $(".test-data-action").data('transaction-id', transactionId);
  $("#memo-result-btn").data('text', data.memo_result);
  if(data.memo_result){
    $("#note-notif").removeClass('hidden');
    $('#memo-result-btn').attr('title',data.memo_result);
  }else{
    $("#note-notif").addClass('hidden');
    $('#memo-result-btn').attr('title','');
  }

  $("#go-to-post-analytics-btn").data('transaction-id', transactionId);

  $.ajax({
    url: baseUrl('analytics/check-action-btn-test-status/'+transactionId),
    type: 'get',
    success: function(res) {
      if (res.unver_and_val_all) {
        $('#unverify-all-btn').removeAttr('disabled');
        $('#validate-all-btn').removeAttr('disabled');
      } else {
        $('#unverify-all-btn').attr('disabled', 'disabled');
        $('#validate-all-btn').attr('disabled', 'disabled');
      }

      if (res.unval_all) {
        $('#unvalidate-all-btn').removeAttr('disabled');
      } else {
        $('#unvalidate-all-btn').attr('disabled', 'disabled');
      }
    }
  });

  let patientType = '-';
  switch (data.type) {
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
  $(".doctor-detail").html(data.doctor.name);
  $(".note-detail").html(data.note);

  refreshPatientDatatables(transactionId);
}

function printAndGotoAnalytic(transactionId){
  Swal.fire({
    title: 'Send data To Post Analytics while Printing ?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Print and Move it!',
    cancelButtonText: 'No, Print Only',
    customClass: {
      confirmButton: "btn btn-primary",
      cancelButton: 'btn btn-danger'
    }
  }).then(function (isConfirm) {
    if (isConfirm.value) {
      $('#print-all-test').attr('href', baseUrl('printAnalyticResult/' + transactionId))
      $('#print-all-test').printPage();
      $('#go-to-post-analytics-btn').trigger('click');
      $('#print-all-test').trigger('click');
    }else{
      $('#print-all-test').attr('href', baseUrl('printAnalyticResult/' + transactionId));
      $('#print-all-test').printPage();
      $('#print-all-test').trigger('click');
    }
  });
}
var refreshPatientDatatables = (transactionId) => {
  $.ajax({
    url: baseUrl('analytics/datatable-test/'+transactionId),
    type: 'get',
    success: function(data) {
        $("#transaction-test-table-body").html(data.html);
        $('.btn-print-group').printPage();
        checkAllValidate();

        data.data.forEach(function(item) {
          // $(".select-result-label").select2({allowClear: true});
          $("#select-result-label-"+item.id).select2({allowClear: true});
          $("#select-result-label-"+item.id).val(item.result_label).trigger('change');
          $("#select-result-label-"+item.id).attr('onChange',"resultLabelChange("+item.id+",event)");
          $(".row-test-" + item.id).attr('onClick', "loadHistoryTest(" + item.id + ",event)");
          
          
        });
    }
  });
  // alert(transactionId);
}

function resultLabelChange(transactionTestId, e) {
  const component = e.target;
  const value = e.target.value;
  // alert(value)
  // const transactionTestId = component.data('transaction-test-id');
  $.ajax({
      url: baseUrl('analytics/update-result-label/'+transactionTestId),
      type: 'put',
      data: { result: value },
      success: function(res) {
          toastr.success("Success update result label");
          switch (res.label) {
            case 1: // normal
                label = '<span class="badge badge-sm badge-circle badge-success">N</span>';
                break;
            case 2: // low
                label = '<span class="badge badge-sm badge-circle badge-warning">L</span>';
                break;
            case 3: // high
                label = '<span class="badge badge-sm badge-circle badge-warning">H</span>';
                break;
            case 4: // abnormal
                label = '<span class="badge badge-sm badge-circle badge-warning">A</span>';
                break;
            case 5: // critical
                label = '<span class="badge badge-sm badge-circle badge-danger">C</span>';
                break;
            default:
                label = '';
          }
        $("#verify-checkbox-id-"+transactionTestId+"").data('result-status', res.label);
        $("#label-info-"+transactionTestId).html(label);
      },
      error: function(request, status, error) {
          toastr.error(request.responseJSON.message);
          component.focus();
      }
  })
}

var onSelectTransaction = (transactionId) => {
  // set finish transaction id for verificator & validator option modal in print tes
  $("#analytic_transaction_id").val(transactionId);
  
  $.ajax({
    url: baseUrl('analytics/transaction/'+transactionId),
    type: 'get',
    success: function(res) {
      refreshPatientDetails(res.data, transactionId);
      $('body').tooltip({
        selector: '[data-toggle="tooltip"]',
        trigger: 'hover'
      });
    }
  });

  // diffcounting function
  diffCounting(transactionId);

  // DatatableTest.init(transactionId);
  // const newUrl = baseUrl('analytics/datatable-test/'+transactionId);
  // DatatableTest.refreshTableAjax(newUrl);

  loadHistoryTest(0, transactionId, true);
}

// var reportBtn = () => {
//   $("#cancel-modal-btn").on('click', function() {
//     $("#critical-modal").modal('hide');
//   });

//   $("#report-modal-btn").on('click', function(e) {
//     const reportTo = $("#critical-modal input[name='report_to']").val();
//     if (reportTo == '') {
//       alert("You need to insert report to field");
//       return;
//     }
  
//     const reportBy = $("#critical-modal input[name='report_by']").val();
//     const criticalTestIds = $("#critical-modal input[name='transaction_test_ids']").val();
//     const transactionId = $("#critical-modal input[name='transaction_id']").val();
    
//     $.ajax({
//       url: baseUrl('analytics/report-critical-tests'),
//       type: 'put',
//       data: {
//         report_to: reportTo,
//         report_by: reportBy,
//         transaction_test_ids: criticalTestIds,
//         transaction_id: transactionId
//       },
//       success: function(res) {
//         toastr.success("Success reporting critical tests");
//         $("#critical-modal").modal('hide');
//         $("#critical-modal input[name='report_to']").val('');
//       }
//     });

//     e.preventDefault();
//   });

// }

// var verifyAllBtn = () => {

//   $("#verify-all-btn").on('click', function() {
//     const transactionId = $(this).data('transaction-id');
//     $.ajax({
//       url: baseUrl('analytics/verify-all/'+transactionId),
//       type: 'put',
//       success: function(res) {
//         toastr.success("Success verify all test");
//         onSelectTransaction(transactionId);
//       }
//     })
//   });
// }

var verifyAllBtn = () => {
  $("#cancel-modal-btn").on('click', function() {
    $("#critical-modal").modal('hide');
  });

  $("#report-modal-btn").on('click', function(e) {
    const reportTo = $("#critical-modal input[name='report_to']").val();
    if (reportTo == '') {
      alert("You need to insert report to field");
      return;
    }
  
    const reportBy = $("#critical-modal input[name='report_by']").val();
    const criticalTestIds = $("#critical-modal input[name='transaction_test_ids']").val();
    const transactionId = $("#critical-modal input[name='transaction_id']").val();
    
    $.ajax({
      url: baseUrl('analytics/report-critical-tests'),
      type: 'put',
      data: {
        report_to: reportTo,
        report_by: reportBy,
        transaction_test_ids: criticalTestIds,
        transaction_id: transactionId
      },
      success: function(res) {
        toastr.success("Success reporting critical tests");
        $("#critical-modal").modal('hide');
        $("#critical-modal input[name='report_to']").val('');
      }
    });

    $.ajax({
      url: baseUrl('analytics/verify-all/'+transactionId),
      type: 'put',
      success: function(res) {
        onSelectTransaction(transactionId);
      }
    });

    e.preventDefault();
  });

  $("#verify-all-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/check-critical-test/'+transactionId),
      type: 'get',
      success: function(res) {
        // console.log(res.exists);
        if (res.exists) {
          let criticalTests = '';
          let criticalTestIds = [];
          res.data.forEach((item) => {
            criticalTests += '<li>'+item.test.name+'  <i>value: </i>'+(item.result_number || item.res_label)+'</li>';
            criticalTestIds.push(item.id);
          });

          $("#critical-tests").html(criticalTests);
          $("#critical-modal input[name='transaction_test_ids']").val(criticalTestIds.join(','));
          $("#critical-modal input[name='transaction_id']").val(transactionId);
          $("#critical-modal").modal('show');
        } else {
           $.ajax({
            url: baseUrl('analytics/verify-all/'+transactionId),
            type: 'put',
            success: function(res) {
              toastr.success("Success verify all test");
              onSelectTransaction(transactionId);
            }
          });
        }
      }
    });

    // $.ajax({
    //   url: baseUrl('analytics/verify-all/'+transactionId),
    //   type: 'put',
    //   success: function(res) {
    //     onSelectTransaction(transactionId);
    //   }
    // })
  });
}

var validateAllBtn = () => {
  $("#validate-all-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/validate-all/'+transactionId),
      type: 'put',
      success: function(res) {
        toastr.success("Success validate all test");
        onSelectTransaction(transactionId);
      }
    })
  });
}

var unverifyAllBtn = () => {
  $("#unverify-all-btn").on('click', function() {
    const transationId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/unverify-all/'+transationId),
      type: 'put',
      success: function(res) {
        toastr.success("Success unverify all test");
        onSelectTransaction(transationId);
      }
    });
  });
}

var unvalidateAllBtn = () => {
  $("#unvalidate-all-btn").on('click', function() {
    const transationId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/unvalidate-all/'+transationId),
      type: 'put',
      success: function(res) {
        toastr.success("Success unvalidate all test");
        onSelectTransaction(transationId);
      }
    })
  });
}

var memoTestModal = (transactionTestId, transactionId, text) => {
  Swal.fire({
    title: 'Test Memo',
    text: 'Please input a memo',
    input: 'text',
    customClass: 'w-600px',
    inputAttributes: {
      autocapitalize: 'off'
    },
    inputValue: text,
    showCancelButton: true,
    confirmButtonText: 'Submit',
    showLoaderOnConfirm: true,
    preConfirm: (reason) => {
      if (reason == '') {
        Swal.showValidationMessage(`Please enter a memo`)
      }
      return { reason: reason }
    },
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl('analytics/update-test-memo'),
        type: 'put',
        data: {
          transaction_test_id: transactionTestId,
          memo: result.value.reason
        },
        success: function(res) {
          toastr.success("Update test memo success!");
          onSelectTransaction(transactionId);
        }
      });
    } else {
      // event.target.checked = true;
    }
  });
}

var parameterDataModal = (transactionId, text) => {
  Swal.fire({
    title: 'Add patient memo result',
    text: 'Please input a memo',
    input: 'textarea',
    customClass: 'w-600px',
    inputAttributes: {
      autocapitalize: 'off'
    },
    inputValue: text,
    showCancelButton: true,
    confirmButtonText: 'Submit',
    showLoaderOnConfirm: true,
    preConfirm: (reason) => {
      // if (reason == '') {
      //   Swal.showValidationMessage(`Please enter a memo`)
      // }
      return { reason: reason }
    },
    allowOutsideClick: false
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: baseUrl('analytics/update-memo-result'),
        type: 'put',
        data: {
          transaction_id: transactionId,
          memo_result: result.value.reason
        },
        success: function(res) {
          toastr.success("Update memo result success!");
          onSelectTransaction(transactionId);
        }
      });
    } else {
      // event.target.checked = true;
    }
  });
}

var memoResultBtn = () => {
  $("#memo-result-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    const text = $(this).data('text');
    parameterDataModal(transactionId, text);
  });
}

// finish transaction
var finishTransactionBtn = () => {
  $("#finish-transaction-btn").on('click', function() {

    $("#print-test-modal").modal('show');
  });

}

var goToPostAnalyticBtn = () => {
  $("#go-to-post-analytics-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');
    $.ajax({
      url: baseUrl('analytics/go-to-post-analytics/'+transactionId),
      type: 'put',
      success: function(res) {
          toastr.success("Success Move Transaction To Post analytics");
          DatatableAnalytics.refreshTable();

          // syncDatatoServer(transactionId);
      },
      error: function(request, status, error) {
        alert(request.responseJSON.message);       
      }
    })
  });
}

function syncDatatoServer(id){
  $.ajax({
    url: baseUrl('synchronize/sync-to-server'),
    data: {
      transaction_id: id
    },
    type: 'POST',
    success: function(res) {
      alert('Synchronize success');
        // toastr.success("Success delete test");
        // refreshPatientDatatables($('#duplo_trans_id').val());
        // $('#modal_duplo').modal('hide')
    },
    error: function(request, status, error) {
      alert('Synchronize failed');
        // toastr.error(request.responseJSON.message);   
        // $('#modal_duplo').modal('hide')
    }
  })
}

var loadHistoryTest = (test_id = 0, transactionId = null, all_test = false) => {
    var testHistoryColumnDatatable = [
      { data: 'test_name' },
      { data: 'result_final', name: 'global_result'},
      { data: 'test_date', render: function(data, type, row){
        return data;
      }, name: 'draw_time'  },
      { data: 'memo_test'}
    ];

    // console.log(result_array)
    var dth;
    $('#datatable_history').DataTable().destroy();
    $('#datatable_history').DataTable({
      "dom": 'lrtip',
      responsive: true,
      deferRender:    true,
      scrollY:        340,
      scrollCollapse: true,
      lengthChange:     false,
      pageLength:     -1,
      paging:         false,
      info:         false,
      serverSide: true,
      orderable: true,
      language: {
          emptyTable: "Patient has No History",
          search: "_INPUT_",
      },
      ajax: {
        url: baseUrl('analytics/load-history-test/'+test_id + (transactionId && '/'+transactionId) + (all_test && '/'+all_test)),
      },
      // data : jsons,
      columns    : testHistoryColumnDatatable
  });
}

function checkAllValidate(){

  $('.group-name').each(function(i, obj) {
    let group_id = $(this).attr('data-group-id');
    let trans_id = $(this).attr('data-transaction-id');
    let className = '.validate-checkbox-'+group_id
    if ($(className+':checked').length > 0) {
      $('#btn-print-group-'+group_id).removeClass('hidden')
      var print_hasil_url = baseUrl('printAnalyticResult/'+trans_id+'/'+group_id);
      // $('#btn-print-group-'+group_id).attr('href', print_hasil_url);
    }
  });
}

var Select2ServerSideTest = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData).select2({
            allowClear: true,
            ajax: {
              url: baseUrl('master/select-options/' + theData + '/' + searchKey),
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
                            var additionalText = ''
                            var PrefixText = ''
                            PrefixText = item.id+" - "
                            // additionalText = " ["+item.general_code+"]"
                            
                            return {
                                text: PrefixText+ item.name + additionalText,
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
            }
            
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


$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

var ckEditorResultDescription; // this is for CKEditor create form
var temp_id = "";
ClassicEditor
  .create(document.querySelector('#result_description'), {
    toolbar: ['bold', 'italic', 'undo', 'redo', 'numberedList', 'bulletedList'],
  })
  .then(editor => {
    ckEditorResultDescription = editor;
    editor.editing.view.change(writer => {
      writer.setStyle('min-height', '120px', editor.editing.view.document.getRoot());
    });
  })
  .catch(error => {
    console.error(error);
  });

function openModalDescriptionEditor(id) {
  temp_id = id
  $('#test-description-editor').modal('show')
  let value = $('#desc' + id).attr('data-existing')
  ckEditorResultDescription.setData(value);
}

$('#submit-modal-description-btn').on('click', function () {
  $('#desc' + temp_id).text(ckEditorResultDescription.getData())
  $('#desc' + temp_id).attr('data-existing', ckEditorResultDescription.getData())
  $('#desc' + temp_id).trigger('change')
  $("#test-description-editor").modal('hide');
});
$("#cancel-modal-description-btn").on('click', function () {
  $("#test-description-editor").modal('hide');
});


// modal duplo 
var openduploModal = (transactionTestId, transactionId, test_id,test_name) => {
  console.log(transactionTestId,transactionId,test_id,test_name)
  $('#duplo_test_id').val(test_id)
  $('#duplo_transaction_test_id').val(transactionTestId)
  $('#duplo_trans_id').val(transactionId)
  $('#duplo_test_name').text(test_name)
  $('#modal_duplo').modal('show')
  $.ajax({
    url: baseUrl('pre-analytics/analyzer-test/'+test_id),
    type: 'GET',
    success: function(res) {
      $("#select-duplo-analyzer").html(res);
      $("#select-duplo-analyzer").select2({allowClear:true});
    }
  })
}

$('#btn-close-modal-duplo').on('click', function(){
  $('#modal_duplo').modal('hide')
})
$('#btn-save-modal-duplo').on('click', function(){
    $.ajax({
        url: baseUrl('analytics/mark-duplo'),
        data: {
          transaction_test_id: $('#duplo_transaction_test_id').val(),
          transaction_id: $('#duplo_trans_id').val(),
          test_id: $('#duplo_test_id').val(),
          analyzer_id: $('#select-duplo-analyzer').val(),
        },
        type: 'POST',
        success: function(res) {
            toastr.success("Success delete test");
            refreshPatientDatatables($('#duplo_trans_id').val());
            $('#modal_duplo').modal('hide')
        },
        error: function(request, status, error) {
            toastr.error(request.responseJSON.message);   
            $('#modal_duplo').modal('hide')
        }
      })
})

var diffCounting = function (transactionId){
  $.ajax({
    url: baseUrl('analytics/diff-counting/'+transactionId),
    type: 'get',
    success: function(data) {
      if(data == 0){
        $('.diff-count-detail').html("-");
      }else{
        $('.diff-count-detail').html(data);
      }
     
    }
  });
}

// On document ready
document.addEventListener('DOMContentLoaded', function () {
  DateRangePicker();
  // reportBtn();
  verifyAllBtn();
  unverifyAllBtn();
  validateAllBtn();
  unvalidateAllBtn();
  memoResultBtn();
  finishTransactionBtn();
  goToPostAnalyticBtn();
  DatatableAnalytics.init();
  Select2ServerSideTest('group').init();

  $(".transaction-test-table").DataTable({
    "scrollY": "500px",
    "scrollCollapse": true,
    "paging": false,
    // "dom": "<'table-responsive'tr>",
    "sort": false,
    autoWidth: false,
    "columnDefs": [
      { "width": "220px", "targets": 0 },
      { "width": "42px", "targets": -1},
      { "width": "42px", "targets": -2}
    ]
  });
  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });
  $('#print-test-btn').printPage();
});