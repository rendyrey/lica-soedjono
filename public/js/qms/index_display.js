var baseUrl = function(url) {
    return base + url;
  }
  
  // ===============================
  // PRE ANALYTICS DATA
  // ===============================
  function loadData() {
    url = baseUrl('qms/datatable-pre-display');
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                var row = '';
                var num;
                $.each(data, function(i, val) {
                  num = i + 1;

                  if(val.no_lab != null){
                      var no_lab = val.no_lab;
                      var nolab = no_lab.substring(8);
                  }else{
                      var nolab = "-";
                  }
              
                  row += '<tr>';
                  row += '<td width="10%" style="font-size: 18px;">' + num + '.' + '</td>';
                  row += '<td width="60%" style="font-size: 18px;">' + val.patient_name + '</td>';
                  row += '<td width="30%" style="font-size: 18px;">'+ nolab +'</td>';
                  row += '</tr>';

                });
                $("#data_1").html(row);
            }
    });
  }
  // ===============================
  // END PRE ANALYTICS DATA
  // ===============================

  // ===============================
  // ANALYTICS & POST DATA
  // ===============================
  function loadData2() {
    url = baseUrl('qms/datatable-proses-display');
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                var row = '';
                var num;
                $.each(data, function(i, val) {
                  num = i + 1;

                  if(val.no_lab != null){
                      var no_lab = val.no_lab;
                      var nolab = no_lab.substring(8);
                  }else{
                      var nolab = "-";
                  }

                  if(val.status == '0'){
                    var status = 'Dalam Antrian';
                    var label = 'warning';
                  }else if(val.status == '1'){
                    var status = 'Sedang Analisis';
                    var label = 'primary';
                  }else{
                    var status = 'Persiapan Hasil';
                    var label = 'info';
                  }
              
                  row += '<tr>';
                  row += '<td width="10%" style="font-size: 18px;">' + num + '.' + '</td>';
                  row += '<td width="50%" style="font-size: 18px;">' + val.patient_name + '</td>';
                  row += '<td width="20%" style="font-size: 18px;">'+ nolab +'</td>';
                  row += '<td width="20%" style="font-size: 18px;"><span class="badge badge-lg badge-' + label + ' ms-2">'+ status +'</span></td>';
                  row += '</tr>';

                });
                $("#data_2").html(row);
            }
    });
  }
  // ===============================
  // END ANALYTICS DATA
  // ===============================

  // ===============================
  // POST ANALYTICS DATA
  // ===============================
  function loadData3() {
    url = baseUrl('qms/datatable-selesai-display');
        $.ajax({
            type: "GET",
            url: url,
            success: function(data) {
                var row = '';
                var num;
                $.each(data, function(i, val) {
                  num = i + 1;

                  if(val.no_lab != null){
                      var no_lab = val.no_lab;
                      var nolab = no_lab.substring(8);
                  }else{
                      var nolab = "-";
                  }
              
                  row += '<tr>';
                  row += '<td width="10%" style="font-size: 18px;">' + num + '.' + '</td>';
                  row += '<td width="50%" style="font-size: 18px;">' + val.patient_name + '</td>';
                  row += '<td width="20%" style="font-size: 18px;">'+ nolab +'</td>';
                  row += '</tr>';

                });
                $("#data_3").html(row);
            }
    });
  }
  // ===============================
  // END POST ANALYTICS DATA
  // ===============================
  

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    function scrollDiv() {

      if (document.getElementById('table_pre').scrollTop < (document.getElementById('table_pre').scrollHeight - document.getElementById('table_pre').offsetHeight)) {
        -1
        document.getElementById('table_pre').scrollTop = document.getElementById('table_pre').scrollTop + 1
      } else {
        document.getElementById('table_pre').scrollTop = 0;
      }
    }

    function scrollDiv2() {

      if (document.getElementById('table_proses').scrollTop < (document.getElementById('table_proses').scrollHeight - document.getElementById('table_proses').offsetHeight)) {
        -1
        document.getElementById('table_proses').scrollTop = document.getElementById('table_proses').scrollTop + 1
      } else {
        document.getElementById('table_proses').scrollTop = 0;
      }
    }

    function scrollDiv3() {

      if (document.getElementById('table_selesai').scrollTop < (document.getElementById('table_selesai').scrollHeight - document.getElementById('table_selesai').offsetHeight)) {
        -1
        document.getElementById('table_selesai').scrollTop = document.getElementById('table_selesai').scrollTop + 1
      } else {
        document.getElementById('table_selesai').scrollTop = 0;
      }
    }

    function startTime() {
      var today = new Date();
      var h = today.getHours();
      var m = today.getMinutes();
      var s = today.getSeconds();
      h = checkTime(h);
      m = checkTime(m);
      s = checkTime(s);
      document.getElementById('time').innerHTML =
        h + ":" + m + ":" + s;
      var t = setTimeout(startTime, 500);
    }
    
    function checkTime(i) {
      if (i < 10) {
        i = "0" + i
      }; // add zero in front of numbers < 10
      return i;
    }
  
  // On document ready
  document.addEventListener('DOMContentLoaded', function () {
    loadData();
    loadData2();
    loadData3();
    scrollDiv();
    scrollDiv2();
    // tabel loadData
    setInterval(function() {
        loadData();
      }, 60000);
    setInterval(scrollDiv, 50)
    // tabel loadData2
    setInterval(function() {
      loadData2();
    }, 60000);
    setInterval(scrollDiv2, 50)
    // tabel loadData3
    setInterval(function() {
      loadData3();
    }, 60000);
    setInterval(scrollDiv3, 50)

    startTime();
  });