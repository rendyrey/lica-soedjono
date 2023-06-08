var baseUrl = function(url) {
    return base + url;
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
      var doctor_id = $("select[name='doctor_id']").val()
      
      const url = baseUrl('report/doctor-datatable/'+startDate+'/'+endDate+'/'+doctor_id);
      Datatable.refreshTableAjax(url);

      $('#print-report').attr("href", baseUrl('report/doctor-print/'+startDate+'/'+endDate+'/'+doctor_id))
  });

  $("#doctor_id").on('change', function () {
    $("#daterange-picker").trigger('change')
  });
}

var Select2ServerSideDoctor = function () {
    var _componentSelect2 = function () {
      // Initialize
      $('.select-doctor').select2({
        allowClear: true,
        ajax: {
          url: baseUrl('report/select-doctor-options'),
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
                PrefixText = item.doctor_id + " - "
  
                return {
                  text: PrefixText + item.doctor_name + additionalText,
                  id: item.doctor_id
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
  
document.addEventListener('DOMContentLoaded', function () {
  Datatable.init();
  DateRangePicker();
  Select2ServerSideDoctor().init();
  $('.btnPrint').printPage();
  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });
  
});