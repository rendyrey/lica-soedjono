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
    const url = baseUrl('post-analytics/datatable/'+startDate+'/'+endDate+ '/' + group_id);
    PostAnalyticDatatable.refreshTableAjax(url);
  });

  $("#group_id").on('change', function () {
    $("#daterange-picker").trigger('change')
  });
}
var onSelectTransaction = (selectedData) => {
    console.log(selectedData)
  const patient = selectedData.patient;
  const room = selectedData.room;
  const transactionId = selectedData.id;
  // set transaction id for edit patient details
  $("#edit-patient-details-btn").data('transaction-id', transactionId);

  // set finish transaction id for verificator modal in print hasil tes
  $("#print-hasil-btn").data('transaction-id', transactionId);

  $(".name-detail").html(patient.name);
  $(".nik-detail").html(patient.nik);
  $(".gender-detail").html((patient.gender == 'M' ? 'Laki-laki' : 'Perempuan'));
  $(".email-detail").html(patient.email);
  $(".phone-detail").html(patient.phone);
  $(".age-detail").html(getAgeFull(patient.birthdate));
  $(".insurance-detail").html(selectedData.insurance.name);
  getFirstPrint(transactionId)

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
  $(".note-detail").html(selectedData.note);
  $(".memo-result").html(selectedData.memo_result);

  if(selectedData.is_print_memo == 1){
    $("#is_memo_print").prop('checked', true);
  }else{
    $("#is_memo_print").prop('checked', false);
  }
  $("#is_memo_print").attr('onChange',"printMemoChange("+transactionId+",event)");

  $('#btnPrintHasil').attr('href',baseUrl('printHasilTest/'+transactionId))

  // // button print hasil
  // $("#btnPrintHasil").on('click', function() {

  //   // radio bahasa
  //   var selectedVal2 = "";
  //   var selected2 = $("input[type='radio'][name='radios22']:checked");
  //   if (selected2.length > 0) {
  //       selectedVal2 = selected2.val();
  //   }

  //   if(selectedVal2 == 'Bahasa Indonesia'){
  //     $('#btnPrintHasil').attr('href',baseUrl('printHasilTest/'+transactionId));
  //   }else{
  //     $('#btnPrintHasil').attr('href',baseUrl('printHasilTestEnglish/'+transactionId));
  //   }   

  // });



  // set the transaction id to note textarea
  $("#transaction-note").data('transaction-id', transactionId);
  $("#transaction-note").val(selectedData.note);

  autosize.update($("#transaction-note"));
  refreshPatientDatatables(transactionId);
  refreshProcessTimeDatatables(transactionId);
  // DatatableTest.init(transactionId);
  // const newUrl = baseUrl('analytics/datatable-test/'+transactionId);
  // DatatableTest.refreshTableAjax(newUrl);
}

var refreshPatientDatatables = (transactionId) => {
  $.ajax({
    url: baseUrl('post-analytics/datatable-test/'+transactionId),
    type: 'get',
    success: function(data) {
        $("#transaction-test-table-body").html(data.html);
        data.data.forEach(function(item) {
          // $(".select-result-label").select2({allowClear: true});
          $("#print-checkbox-id-"+item.id).attr('onChange',"printChange("+item.id+",event)");
        });
    }
  });
}

var theFullMinute = function(date) {
    
  let d = new Date(date);
  let hours = d.getHours();
  let minutes = d.getMinutes();
  let seconds = d.getSeconds();
  let theMinute = hours + ':' + minutes  + ':' + seconds;

  return theMinute;
}

var refreshProcessTimeDatatables = (transactionId) => {

  if ( ! $.fn.DataTable.isDataTable( '.process-time-table' ) ) {
    transactionProcessTimeTable = $('.process-time-table').DataTable({
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
          url: baseUrl('post-analytics/datatable-process-time/'+transactionId),
          complete: function(data) {
            // =====
          }
      },
      columns: [
        { data: 'id', render: function(data, type, row) {
            if(row.package_id){
                return row.package_name

            }else{
                return row.test_name
            }
          }, defaultContent: ''
        },
        { data: 'checkin_time', render: function(data, type, row) {
            return theFullMinute(data);
          }
        },
        { data: 'checkin_by_name', render: function(data, type, row) {
            if(row.checkin_by_name == null){
                return 'Auto';

            }else{
                return row.checkin_by_name;
            }
          }, defaultContent: ''
        },
        { data: 'draw_time', render: function(data, type, row) {
            return theFullMinute(data);
          }
        },
        { data: 'draw_by_name', render: function(data, type, row) {
            if(row.draw_by_name == null){
                return 'Auto';

            }else{
                return row.draw_by_name;
            }
          }, defaultContent: ''
        },
        { data: 'verify_time', render: function(data, type, row) {
            return theFullMinute(data);
          }
        },
        { data: 'verify_by_name'},
        { data: 'validate_time', render: function(data, type, row) {
            return theFullMinute(data);
          }
        },
        { data: 'validate_by_name'},
        { data: 'analytic_time', render: function(data, type, row) {
            return theFullMinute(data);
          }
        },
        { data: 'draw_by_name'},
      ]
    });
  } else {
    transactionProcessTimeTable.ajax.url(baseUrl('post-analytics/datatable-process-time/'+transactionId)).load();
  }
}

var deleteTransaction = function (id) {
  Swal.fire({
      title: 'Are you sure?',
      text: 'This data will be return to Post Analytic',
      // type: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes',
      customClass: {
          confirmButton: "btn btn-danger",
          cancelButton: 'btn btn-secondary'
      }
  }).then(function(isConfirm){
      if(isConfirm.value) {
          $.ajax({
              url: baseUrl('post-analytics/return-to-analytic/'+id),
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              method: 'POST',
              success: function(res) {
                  PostAnalyticDatatable.refreshTable();
                  toastr.success(res.message, "Delete Success!");
              },
              error: function(request, status, error){
                  toastr.error(request.responseJSON.message);
              }
          })
      }
  });
}

function printChange(transactionTestId, e) {
  const component = e.target;
  const value = e.target.value;
  // alert(value)
  // const transactionTestId = component.data('transaction-test-id');
  $.ajax({
      url: baseUrl('post-analytics/update-is-print/'+transactionTestId),
      type: 'put',
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { result: value },
      success: function(res) {
          toastr.success("Success update print status");
          
      },
      error: function(request, status, error) {
          toastr.error(request.responseJSON.message);
          component.focus();
      }
  })
}

function printMemoChange(transactionId, e){
  const component = e.target;
  const value = e.target.value;
  $.ajax({
    url: baseUrl('post-analytics/update-is-print-memo/'+transactionId),
    type: 'put',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    data: { result: value },
    success: function(res) {
        toastr.success("Success update print status");
        
    },
    error: function(request, status, error) {
        toastr.error(request.responseJSON.message);
        component.focus();
    }
  })
}

var getFirstPrint = (transactionId) => {
  $.ajax({
    url: baseUrl('getFirstPrint/'+transactionId),
    type: 'get',
    success: function(data) {
        if(data.data){
            $('.first-printed-detail').text(data.data)
        }else{
            $('.first-printed-detail').text("-")
        }
    }
  });
}

var Select2ServerSideTest = function (theData, searchKey = 'name') {
  var _componentSelect2 = function () {
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
            results: $.map(data, function (item) {
              var additionalText = ''
              var PrefixText = ''
              PrefixText = item.id + " - "
              // additionalText = " ["+item.general_code+"]"

              return {
                text: PrefixText + item.name + additionalText,
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
    init: function () {
      _componentSelect2();
    }
  }
}

// Opsi Print Hasil Tes
var printOptions = function() {
  $("#print-hasil-btn").on('click', function() {
    const transactionId = $(this).data('transaction-id');

    $.ajax({
      url: baseUrl('post-analytics/get-verificator-name/'+transactionId),
      type: 'get',
      success: function(data) {
        
        $("#verificator-hasil-pemeriksaan").val(data.data);

        $("#print-hasil-modal").modal('show');
      }
    });

  });
}

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

document.addEventListener('DOMContentLoaded', function () {
  PostAnalyticDatatable.init();
  printOptions();
  DateRangePicker();
  Select2ServerSideTest('group').init();
  $('.btnPrintHasil').printPage();
  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });
});