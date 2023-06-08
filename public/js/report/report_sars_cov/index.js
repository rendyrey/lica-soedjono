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
      
      const url = baseUrl('report/sars-cov-datatable/'+startDate+'/'+endDate);
      Datatable.refreshTableAjax(url);

      $('#print-report').attr("href", baseUrl('report/sars-cov-print/'+startDate+'/'+endDate))
  });

}

document.addEventListener('DOMContentLoaded', function () {
  Datatable.init();
  DateRangePicker();
  $('.btnPrint').printPage();
  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });
  
});