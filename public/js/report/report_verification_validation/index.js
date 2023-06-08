
var baseUrl = function(url) {
    return base + url;
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
      var test_id = $("select[name='test_id']").val();
      const url = baseUrl('report/verification-validation-datatable/'+startDate+'/'+endDate+'/'+test_id);
      Datatable.refreshTableAjax(url);

      $('#print-report').attr("href", baseUrl('report/verification-validation-print/'+startDate+'/'+endDate+'/'+test_id))
  });

  $("#test_id").on('change', function () {
    $("#daterange-picker").trigger('change')
  });
}

var Select2ServerSideTest = function (theData = 'test', searchKey = 'name') {
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
  
document.addEventListener('DOMContentLoaded', function () {
  Datatable.init();
  DateRangePicker();
  Select2ServerSideTest().init();
  $('.btnPrint').printPage();
  $('body').tooltip({
    selector: '[data-toggle="tooltip"]',
    trigger: 'hover'
  });

});