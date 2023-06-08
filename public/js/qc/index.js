var baseUrl = function (url) {
    return base + url;
}

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

// analyzer on change
var analyzerOnChange = () => {
    $('#analyzer').on('change', function () {
        var analyzer_id = this.value;

        $.ajax({
            url: baseUrl('quality-control/get-test/' + analyzer_id),
            type: 'get',
            success: function (data) {
                var options = $('#test');
                $('#test').html('');
                $.each(data, function (i, val) {
                    options.append($("<option />").val(val.test_id).text(val.test_id + ' - ' + val.test_name));
                });
            }
        })
    });
}

var CheckQC = () => {
    var month = $('#month').val();
    var year = $('#year').val();
    var analyzer = $('#analyzer').val();
    var test = $('#test').val();

    if (month != '' && year != '' && analyzer != '' && test != '') {

        $.ajax({
            url: baseUrl('quality-control/get-qc-id/' + month + '/' + year + '/' + analyzer + '/' + test),
            type: 'get',
            success: function (json) {
                // console.log(json);
                var data_level1 = json.qc_data1;
                var data_level2 = json.qc_data2;
                var data_level3 = json.qc_data3;

                var qualityControlId1 = 0;
                var qualityControlId2 = 0;
                var qualityControlId3 = 0;
                
                // level 1
                if (data_level1) {
                    qualityControlId1 = json.qc_data1.qc_id;
                    
                    // need qc-id-1
                    $("#daterange-table-1").data('qc-id-1', qualityControlId1);
                    $("#daterange-table-2").data('qc-id-1', qualityControlId1);
                    $("#daterange-table-3").data('qc-id-1', qualityControlId1);
                    $("#button_update_data").data('qc-id-1', qualityControlId1);
                    $("#button_update_data_2").data('qc-id-1', qualityControlId1);
                    $("#button_update_data_3").data('qc-id-1', qualityControlId1);

                    if (data_level2) {
                        qualityControlId2 = json.qc_data2.qc_id;
                    }
                    if (data_level3) {
                        qualityControlId3 = json.qc_data3.qc_id;
                    }
                    
                    $("#print-qc-data-1").data('qc-id', qualityControlId1);

                    autoFillReference1(qualityControlId1);
                    level1Table(qualityControlId1, qualityControlId2, qualityControlId3);
                    // loadGraphData1(qualityControlId1, '','');
                    // console.log('Level 1 : ' + qualityControlId1);

                } else {
                    $('#no_lot').val('');
                    $('#standard_devation1').val('');
                    $('#control_name1').val('');
                    $('#low_value1').val('');
                    $('#high_value1').val('');
                    $('#target_value1').val('');
                    $('#deviation1').val('');
                    $('#add-reference-1').prop('disabled', false);
                }

                // level 2
                if (data_level2) {
                    qualityControlId2 = json.qc_data2.qc_id;

                    // need qc-id-2
                    $("#daterange-table-1").data('qc-id-2', qualityControlId2);
                    $("#daterange-table-2").data('qc-id-2', qualityControlId2);
                    $("#daterange-table-3").data('qc-id-2', qualityControlId2);
                    $("#button_update_data_1").data('qc-id-2', qualityControlId2);
                    $("#button_update_data_2").data('qc-id-2', qualityControlId2);
                    $("#button_update_data_3").data('qc-id-2', qualityControlId2);

                    if (data_level1) {
                        qualityControlId1 = json.qc_data1.qc_id;
                    }
                    if (data_level3) {
                        qualityControlId3 = json.qc_data3.qc_id;
                    }

                    $("#print-qc-data-2").data('qc-id', qualityControlId2);

                    autoFillReference2(qualityControlId2);
                    level2Table(qualityControlId2, qualityControlId1, qualityControlId3);
                    // loadGraphData2(qualityControlId2);
                    // console.log('Level 2 : ' + qualityControlId2);

                } else {
                    $('#no_lot').val('');
                    $('#standard_devation2').val('');
                    $('#control_name2').val('');
                    $('#low_value2').val('');
                    $('#high_value2').val('');
                    $('#target_value2').val('');
                    $('#deviation2').val('');
                    $('#add-reference-2').prop('disabled', false);
                }

                // level 3
                if (data_level3) {
                    qualityControlId3 = json.qc_data3.qc_id;

                    // need qc-id-3
                    $("#daterange-table-1").data('qc-id-3', qualityControlId3);
                    $("#daterange-table-2").data('qc-id-3', qualityControlId3);
                    $("#daterange-table-3").data('qc-id-3', qualityControlId3);
                    $("#button_update_data_1").data('qc-id-3', qualityControlId3);
                    $("#button_update_data_2").data('qc-id-3', qualityControlId3);
                    $("#button_update_data_3").data('qc-id-3', qualityControlId3);

                    if (data_level1) {
                        qualityControlId1 = json.qc_data1.qc_id;
                    }
                    if (data_level2) {
                        qualityControlId2 = json.qc_data2.qc_id;
                    }

                    $("#print-qc-data-3").data('qc-id', qualityControlId3);

                    autoFillReference3(qualityControlId3);
                    level3Table(qualityControlId3, qualityControlId1, qualityControlId2);
                    // console.log('Level 3 : ' + qualityControlId3);

                } else {
                    $('#no_lot').val('');
                    $('#standard_devation3').val('');
                    $('#control_name3').val('');
                    $('#low_value3').val('');
                    $('#high_value3').val('');
                    $('#target_value3').val('');
                    $('#deviation3').val('');
                    $('#add-reference-3').prop('disabled', false);
                }

                // load graph data level 1,2,3
                loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '','');

            }
        })

    } else {
        toastr.error("Please complete the form to continue submission form");
    }

}

// auto fill reference level 1
var autoFillReference1 = (qualityControlId) => {
    $.ajax({
        url: baseUrl('quality-control/get-reference-1/' + qualityControlId),
        type: 'get',
        success: function (json) {
            $('#standard_deviation1').html('');
            var options = $('#standard_deviation1');
            options.append($("<option />").val(json.standard_deviation).text(json.standard_deviation));

            $('#no_lot').val(json.no_lot); 
            $('#control_name1').val(json.control_name);
            $('#low_value1').val(json.low_value);
            $('#high_value1').val(json.high_value);
            $('#target_value1').val(json.target_value);
            $('#deviation1').val(json.deviation);

            // form read only
            $('#no_lot').prop('readonly', true);
            $('#control_name1').prop('readonly', true);
            $('#low_value1').prop('readonly', true);
            $('#high_value1').prop('readonly', true);
            $('#target_value1').prop('readonly', true);
            $('#deviation1').prop('readonly', true);

            $('#add-reference-1').prop('disabled', true);
            
        }
    })
}

// auto fill reference level 2
var autoFillReference2 = (qualityControlId) => {
    $.ajax({
        url: baseUrl('quality-control/get-reference-2/' + qualityControlId),
        type: 'get',
        success: function (json) {
            $('#standard_deviation2').html('');
            var options = $('#standard_deviation2');
            options.append($("<option />").val(json.standard_deviation).text(json.standard_deviation));

            $('#no_lot').val(json.no_lot);
            $('#control_name2').val(json.control_name);
            $('#low_value2').val(json.low_value);
            $('#high_value2').val(json.high_value);
            $('#target_value2').val(json.target_value);
            $('#deviation2').val(json.deviation);

            // form read only
            $('#no_lot').prop('readonly', true);
            $('#control_name2').prop('readonly', true);
            $('#low_value2').prop('readonly', true);
            $('#high_value2').prop('readonly', true);
            $('#target_value2').prop('readonly', true);
            $('#deviation2').prop('readonly', true);

            $('#add-reference-2').prop('disabled', true);
        }
    })
}

// auto fill reference level 3
var autoFillReference3 = (qualityControlId) => {
    $.ajax({
        url: baseUrl('quality-control/get-reference-3/' + qualityControlId),
        type: 'get',
        success: function (json) {
            $('#standard_deviation3').html('');
            var options = $('#standard_deviation3');
            options.append($("<option />").val(json.standard_deviation).text(json.standard_deviation));

            $('#no_lot').val(json.no_lot);
            $('#control_name3').val(json.control_name);
            $('#low_value3').val(json.low_value);
            $('#high_value3').val(json.high_value);
            $('#target_value3').val(json.target_value);
            $('#deviation3').val(json.deviation);

            // form read only
            $('#no_lot').prop('readonly', true);
            $('#control_name3').prop('readonly', true);
            $('#low_value3').prop('readonly', true);
            $('#high_value3').prop('readonly', true);
            $('#target_value3').prop('readonly', true);
            $('#deviation3').prop('readonly', true);

            $('#add-reference-3').prop('disabled', true);
        }
    })
}

var standardDeviationOnChange1 = () => {
    $('#standard_deviation1').on('change', function() {
        if(this.value == '2' || this.value == '3'){
            $('#control_name1').prop('disabled', false)
            $('#low_value1').prop('disabled', false)
            $('#high_value1').prop('disabled', false)    
            $('#target_value1').prop('disabled', false)    
            $('#deviation1').prop('disabled', false)    
        }
    });
}

var standardDeviationOnChange2 = () => {
    $('#standard_deviation2').on('change', function() {
        if(this.value == '2' || this.value == '3'){
            $('#control_name2').prop('disabled', false)
            $('#low_value2').prop('disabled', false)
            $('#high_value2').prop('disabled', false) 
            $('#target_value2').prop('disabled', false)    
            $('#deviation2').prop('disabled', false)     
        }
    });
}

var standardDeviationOnChange3 = () => {
    $('#standard_deviation3').on('change', function() {
        if(this.value == '2' || this.value == '3'){
            $('#control_name3').prop('disabled', false)
            $('#low_value3').prop('disabled', false)
            $('#high_value3').prop('disabled', false)    
            $('#target_value3').prop('disabled', false)    
            $('#deviation3').prop('disabled', false)  
        }
    });
}

// on focus out high value level 1
var onFocusOut1 = () => {
    
    var standard_deviation = $('#standard_deviation1').val()
    var low_value = $('#low_value1').val();
    var high_value = $('#high_value1').val();

    var target_value = 0;
    var deviation = 0;

    if(standard_deviation == '2'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 4;
    }else if(standard_deviation == '3'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 6;
    }

    $('#target_value1').val(target_value);
    $('#deviation1').val(deviation);

}

// on focus out high value level 2
var onFocusOut2 = () => {

    var standard_deviation = $('#standard_deviation2').val()
    var low_value = $('#low_value2').val();
    var high_value = $('#high_value2').val();

    var target_value = 0;
    var deviation = 0;

    if(standard_deviation == '2'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 4;
    }else if(standard_deviation == '3'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 6;
    }

    $('#target_value2').val(target_value);
    $('#deviation2').val(deviation.toFixed(1));


}

// on focus out high value level 3
var onFocusOut3 = () => {

    var standard_deviation = $('#standard_deviation3').val()
    var low_value = $('#low_value3').val();
    var high_value = $('#high_value3').val();

    var target_value = 0;
    var deviation = 0;

    if(standard_deviation == '2'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 4;
    }else if(standard_deviation == '3'){
        target_value = (Number(high_value) + Number(low_value)) / 2;
        deviation = (Number(high_value) - Number(low_value)) / 6;
    }

    $('#target_value3').val(target_value);
    $('#deviation3').val(deviation.toFixed(1));


}

// add reference 1
var addReference1 = () => {
    var month = $('#month').val();
    var year = $('#year').val();
    var analyzer = $('#analyzer').val();
    var test = $('#test').val();

    var no_lot = $('#no_lot').val();
    var standard_deviation = $('#standard_deviation1').val();
    var control_name = $('#control_name1').val();
    var low_value = $('#low_value1').val();
    var high_value = $('#high_value1').val();
    var target_value = $('#target_value1').val();
    var deviation = $('#deviation1').val();

    // console.log(no_lot + ' ' + control_name + ' ' + low_value + ' ' + high_value + ' ' + target_value + ' ' + deviation);
    if(no_lot != '' && standard_deviation != '' && control_name != '' && low_value != '' && high_value != '' && target_value != '' && deviation != ''){

        $.ajax({
            url: baseUrl('quality-control/add-reference-1'),
            type: 'post',
            data: {
                month: month,
                year: year,
                analyzer: analyzer,
                test: test,
                no_lot: no_lot,
                standard_deviation: standard_deviation,
                control_name: control_name,
                low_value: low_value,
                high_value: high_value,
                target_value: target_value,
                deviation: deviation
            },
            success: function (res) {
                $('#no_lot').val('');
                $('#standard_deviation1').val('').trigger('change');
                $('#control_name1').val('');
                $('#low_value1').val('');
                $('#high_value1').val('');
                $('#target_value1').val('');
                $('#deviation1').val('');
                toastr.success(res.message, "Create Success!");
            }
        })

    }else{
        toastr.error("Please complete the form to continue submission form");
    }
}

// add reference 2
var addReference2 = () => {
    var month = $('#month').val();
    var year = $('#year').val();
    var analyzer = $('#analyzer').val();
    var test = $('#test').val();

    var no_lot = $('#no_lot').val();
    var standard_deviation = $('#standard_deviation2').val();
    var control_name = $('#control_name2').val();
    var low_value = $('#low_value2').val();
    var high_value = $('#high_value2').val();
    var target_value = $('#target_value2').val();
    var deviation = $('#deviation2').val();

    // console.log(no_lot + ' ' + control_name + ' ' + low_value + ' ' + high_value + ' ' + target_value + ' ' + deviation);
    if(no_lot != '' && standard_deviation != '' && control_name != '' && low_value != '' && high_value != '' && target_value != '' && deviation != ''){

        $.ajax({
            url: baseUrl('quality-control/add-reference-2'),
            type: 'post',
            data: {
                month: month,
                year: year,
                analyzer: analyzer,
                test: test,
                no_lot: no_lot,
                standard_deviation: standard_deviation,
                control_name: control_name,
                low_value: low_value,
                high_value: high_value,
                target_value: target_value,
                deviation: deviation
            },
            success: function (res) {
                $('#no_lot').val('');
                $('#standard_deviation2').val('').trigger('change');
                $('#control_name2').val('');
                $('#low_value2').val('');
                $('#high_value2').val('');
                $('#target_value2').val('');
                $('#deviation2').val('');
                toastr.success(res.message, "Create Success!");

                // remove level 1
                $('#no_lot').val('');
                $('#standard_deviation1').val('').trigger('change');
                $('#control_name1').val('');
                $('#low_value1').val('');
                $('#high_value1').val('');
                $('#target_value1').val('');
                $('#deviation1').val('');
            }
        })

    }else{
        toastr.error("Please complete the form to continue submission form");
    }
}

// add reference 3
var addReference3 = () => {
    var month = $('#month').val();
    var year = $('#year').val();
    var analyzer = $('#analyzer').val();
    var test = $('#test').val();

    var no_lot = $('#no_lot').val();
    var standard_deviation = $('#standard_deviation3').val();
    var control_name = $('#control_name3').val();
    var low_value = $('#low_value3').val();
    var high_value = $('#high_value3').val();
    var target_value = $('#target_value3').val();
    var deviation = $('#deviation3').val();

    // console.log(no_lot + ' ' + control_name + ' ' + low_value + ' ' + high_value + ' ' + target_value + ' ' + deviation);
    if(no_lot != '' && standard_deviation != '' && control_name != '' && low_value != '' && high_value != '' && target_value != '' && deviation != ''){

        $.ajax({
            url: baseUrl('quality-control/add-reference-3'),
            type: 'post',
            data: {
                month: month,
                year: year,
                analyzer: analyzer,
                test: test,
                no_lot: no_lot,
                standard_deviation: standard_deviation,
                control_name: control_name,
                low_value: low_value,
                high_value: high_value,
                target_value: target_value,
                deviation: deviation
            },
            success: function (res) {
                $('#no_lot').val('');
                $('#standard_deviation3').val('').trigger('change');
                $('#control_name3').val('');
                $('#low_value3').val('');
                $('#high_value3').val('');
                $('#target_value3').val('');
                $('#deviation3').val('');
                toastr.success(res.message, "Create Success!");

                // remove level 1 & 2
                $('#no_lot').val('');
                $('#standard_deviation1').val('').trigger('change');
                $('#control_name1').val('');
                $('#low_value1').val('');
                $('#high_value1').val('');
                $('#target_value1').val('');
                $('#deviation1').val('');

                $('#no_lot').val('');
                $('#standard_deviation2').val('').trigger('change');
                $('#control_name2').val('');
                $('#low_value2').val('');
                $('#high_value2').val('');
                $('#target_value2').val('');
                $('#deviation2').val('');
            }
        })

    }else{
        toastr.error("Please complete the form to continue submission form");
    }
}

// =====================
// DATATABLE
// =====================
// DATATABLE MONTHLY QC DATA REQUIREMENT

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

var DateRangePicker1 = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-table-1").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-table-1").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-table-1").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-table-1").daterangepicker({
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
          format: 'DD/MM/YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-table-1").on('change', function () {

        const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }

        const url = baseUrl('quality-control/datatable-qc-data/' +qc_id1+ '/' +startDate+ '/'+endDate);
        $('.datatable-level-1').DataTable().ajax.url(url).load();
        loadGraphData(qc_id1, qc_id2, qc_id3, startDate, endDate);
    });
}

var DateRangePicker2 = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-table-2").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-table-2").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-table-2").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-table-2").daterangepicker({
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
          format: 'DD/MM/YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-table-2").on('change', function () {

        const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }

        const url = baseUrl('quality-control/datatable-qc-data/' +qc_id2+ '/' +startDate+ '/'+endDate);
        $('.datatable-level-2').DataTable().ajax.url(url).load();
        // alert(qc_id2);
        loadGraphData(qc_id1, qc_id2, qc_id3, startDate, endDate);
    });
}

var DateRangePicker3 = () => {
    var start = moment();
    var end = moment();
  
    function cb(start, end) {
        $("#daterange-table-3").html(start.format("YYYY-MM-DD") + "," + end.format("YYYY-MM-DD"));
        const startDate = $("#daterange-table-3").data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $("#daterange-table-3").data('daterangepicker').endDate.format('YYYY-MM-DD');
    }
  
    $("#daterange-table-3").daterangepicker({
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
          format: 'DD/MM/YYYY'
        },
        alwaysShowCalendars: true
    }, cb);
  
    cb(start, end);
  
    $("#daterange-table-3").on('change', function () {

        const startDate = $(this).data('daterangepicker').startDate.format('YYYY-MM-DD');
        const endDate = $(this).data('daterangepicker').endDate.format('YYYY-MM-DD');

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }

        const url = baseUrl('quality-control/datatable-qc-data/' +qc_id3+ '/' +startDate+ '/'+endDate);
        $('.datatable-level-3').DataTable().ajax.url(url).load();
        // alert(qc_id2);
        loadGraphData(qc_id1, qc_id2, qc_id3, startDate, endDate);
    });
}

// Tabel QC Data & Modal Form Level 1
var level1Table = (qualityControlId1, qualityControlId2, qualityControlId3) => {

    $('#add-qc-data-1').prop('disabled', false);
    $('#print-qc-data-1').prop('disabled', false);
    $('#export-qc-data-1').prop('disabled', false);
    $('#daterange-table-1').prop('disabled', false);

    $("#add-qc-data-1").on('click', function() {
        $("#add-qc-data-1").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-1/' + qualityControlId1),
              type: 'get',
              success: function(res) {
                $("#qc_id1").val(res.qc_id);
                $("#qc_id2").val(qualityControlId2);
                $("#qc_id3").val(qualityControlId3);
                $("#lot_no_lv_1").text(res.no_lot);
                $("#month_year_lv_1").text(res.month + ', ' + res.year);
                $("#test_name_lv_1").text(res.test_name);
        
                $("#create-qc-data-1-modal").modal('show');
              }
            });
          });
    });

    $('.datatable-level-1').DataTable().destroy();
    $('.datatable-level-1').DataTable({
        "order": [],
        "dom": 'lrtip',
        responsive: true,
        deferRender: true,
        scrollY: 600,
        scrollCollapse: true,
        scroller: true,
        lengthChange: false,
        pageLength: 250,
       
        'ajax': {
            "type": "GET",
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId1),
            "dataSrc": function(jsons) {
                console.log(jsons)
                var return_data = new Array();
               
                var index = 1;
                jsons.forEach(function(json) {
                    return_data.push({
                        'no': index,
                        'date': moment(json.date).format("DD/MM/YYYY"),
                        'data': json.data,
                        'position': json.position,
                        'qc': json.qc,
                        'atlm': json.atlm,
                        'recommendation': json.recommendation,
                        'action': renderAction1(json.id, json.qc_id, qualityControlId2, qualityControlId3),
                    })
                    index++;
                })
                return return_data;
            },
        },
        "columns": [
            {
                'data': 'no'
            },
            {
                'data': 'date'
            },
            {
                'data': 'data'
            },
            {
                'data': 'position'
            },
            {
                'data': 'qc'
            },
            {
                'data': 'atlm'
            },
            {
                'data': 'recommendation'
            },
            {
                'data': 'action'
            },
        ]
    });
}

// Tabel QC Data & Modal Form Level 2
var level2Table = (qualityControlId2, qualityControlId1, qualityControlId3) => {

    $('#add-qc-data-2').prop('disabled', false);
    $('#print-qc-data-2').prop('disabled', false);
    $('#export-qc-data-2').prop('disabled', false);
    $('#daterange-table-2').prop('disabled', false);

    $("#add-qc-data-2").on('click', function() {
        $("#add-qc-data-2").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-2/' + qualityControlId2),
              type: 'get',
              success: function(res) {
                $("#qc_id2_2").val(res.qc_id);
                $("#qc_id1_2").val(qualityControlId1);
                $("#qc_id3_2").val(qualityControlId3);
                $("#lot_no_lv_2").text(res.no_lot);
                $("#month_year_lv_2").text(res.month + ', ' + res.year);
                $("#test_name_lv_2").text(res.test_name);
        
                $("#create-qc-data-2-modal").modal('show');
              }
            });
          });
    });

    $('.datatable-level-2').DataTable().destroy();
    $('.datatable-level-2').DataTable({
        "order": [],
        "dom": 'lrtip',
        responsive: true,
        deferRender: true,
        scrollY: 500,
        scrollCollapse: true,
        scroller: true,
        lengthChange: false,
        pageLength: 250,

        'ajax': {
            "type": "GET",
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId2),
            "dataSrc": function(jsons) {
                console.log(jsons)
                var return_data = new Array();
               
                jsons.forEach(function(json) {
                    return_data.push({
                        'date': moment(json.date).format("DD/MM/YYYY"),
                        'data': json.data,
                        'position': json.position,
                        'qc': json.qc,
                        'atlm': json.atlm,
                        'recommendation': json.recommendation,
                        'action': renderAction2(json.id, json.qc_id, qualityControlId1, qualityControlId3),
                    })
                })
                return return_data;
            },
        },
        "columns": [
            {
                'data': 'date'
            },
            {
                'data': 'data'
            },
            {
                'data': 'position'
            },
            {
                'data': 'qc'
            },
            {
                'data': 'atlm'
            },
            {
                'data': 'recommendation'
            },
            {
                'data': 'action'
            },
        ],
        "columnDefs": [
            // {
            //     "targets": [ 0 ],
            //     "visible": false,
            //     "searchable": false
            // },
            // {
            //     "targets": [ 1 ],
            //     "visible": false,
            //     "searchable": false
            // }
        ]
    });
}

// Tabel QC Data & Modal Form Level 3
var level3Table = (qualityControlId3, qualityControlId1, qualityControlId2) => {

    $('#add-qc-data-3').prop('disabled', false);
    $('#print-qc-data-3').prop('disabled', false);
    $('#export-qc-data-3').prop('disabled', false);
    $('#daterange-table-3').prop('disabled', false);

    $("#add-qc-data-3").on('click', function() {
        $("#add-qc-data-3").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-3/' + qualityControlId3),
              type: 'get',
              success: function(res) {
                $("#qc_id3_3").val(res.qc_id);
                $("#qc_id1_3").val(qualityControlId1);
                $("#qc_id2_3").val(qualityControlId2);
                $("#lot_no_lv_3").text(res.no_lot);
                $("#month_year_lv_3").text(res.month + ', ' + res.year);
                $("#test_name_lv_3").text(res.test_name);
        
                $("#create-qc-data-3-modal").modal('show');
              }
            });
          });
    });

    $('.datatable-level-3').DataTable().destroy();
    $('.datatable-level-3').DataTable({
        "order": [],
        "dom": 'lrtip',
        responsive: true,
        deferRender: true,
        scrollY: 500,
        scrollCollapse: true,
        scroller: true,
        lengthChange: false,
        pageLength: 250,

        'ajax': {
            "type": "GET",
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId3),
            "dataSrc": function(jsons) {
                console.log(jsons)
                var return_data = new Array();
               
                jsons.forEach(function(json) {
                    return_data.push({
                        'date': moment(json.date).format("DD/MM/YYYY"),
                        'data': json.data,
                        'position': json.position,
                        'qc': json.qc,
                        'atlm': json.atlm,
                        'recommendation': json.recommendation,
                        'action': renderAction3(json.id, json.qc_id, qualityControlId1, qualityControlId2),
                    })
                })
                return return_data;
            },
        },
        "columns": [
            {
                'data': 'date'
            },
            {
                'data': 'data'
            },
            {
                'data': 'position'
            },
            {
                'data': 'qc'
            },
            {
                'data': 'atlm'
            },
            {
                'data': 'recommendation'
            },
            {
                'data': 'action'
            },
        ],
        "columnDefs": [
            // {
            //     "targets": [ 0 ],
            //     "visible": false,
            //     "searchable": false
            // },
            // {
            //     "targets": [ 1 ],
            //     "visible": false,
            //     "searchable": false
            // }
        ]
    });
}

// =====================
// END DATATABLE
// ===================== 


// =================================
// QC DATA CREATE FORM LEVEL 1, 2, 3
// =================================

// required for the form validation rules
var rulesFormValidation = {
    // Level 1
    date: {
        required: true
    },
    qc_data: {
        required: true
    },
    position: {
        required: true,
    },
    qc: {
        required: true,
        number: true
    },
    atlm: {
        required: true,
    },
    recommendation: {
        required: true,
    },

    // Level 2
    date_2: {
        required: true
    },
    qc_data_2: {
        required: true
    },
    position_2: {
        required: true,
    },
    qc_2: {
        required: true,
        number: true
    },
    atlm_2: {
        required: true,
    },
    recommendation_2: {
        required: true,
    },

    // Level 3
    date_3: {
        required: true
    },
    qc_data_3: {
        required: true
    },
    position_3: {
        required: true,
    },
    qc_3: {
        required: true,
        number: true
    },
    atlm_3: {
        required: true,
    },
    recommendation_3: {
        required: true,
    }
};

// QC data on focus out 1
var QCDataonFocusOut1 = function (){
    var qc_id = $('#qc_id1').val();
    var qc_value = $('#qc_data').val();

    console.log(qc_id);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-1/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })


}

// QC data on focus out 2
var QCDataonFocusOut2 = function (){
    var qc_id = $('#qc_id2_2').val();
    var qc_value = $('#qc_data_2').val();

    console.log(qc_id);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-2/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position_2').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })


}

// QC data on focus out 3
var QCDataonFocusOut3 = function (){
    var qc_id = $('#qc_id3_3').val();
    var qc_value = $('#qc_data_3').val();

    console.log(qc_id);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-3/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position_3').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })


}

// create new Data level 1
var createDataLevel1 = function () {
    // $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create-level-1").serialize();
    let theForm = $("#form-create-level-1");

    var qualityControlId2 = $('#qc_id2').val();
    var qualityControlId3 = $('#qc_id3').val();

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-1'),
        method: 'POST',
        data: formData,
        success: function(res) {
            var qualityControlId1 = res.qc_id;
            $('#create-qc-data-1-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            theForm.trigger('reset'); // reset form after successfully create data
            $("#form-create-level-1 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $('.daterange-picker').daterangepicker('hide');

            refresh1();
            loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
            // $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

// create new Data level 2
var createDataLevel2 = function () {
    // $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create-level-2").serialize();
    let theForm = $("#form-create-level-2");

    var qualityControlId1 = $('#qc_id1_2').val();
    var qualityControlId3 = $('#qc_id3_2').val();

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-2'),
        method: 'POST',
        data: formData,
        success: function(res) {
            var qualityControlId2 = res.qc_id;
            // alert(qualityControlId);
            $('#create-qc-data-2-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            theForm.trigger('reset'); // reset form after successfully create data
            $("#form-create-level-2 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $('#create-qc-data-2-modal').modal('hide');
            $('.daterange-picker').daterangepicker('hide');

            refresh2();
            loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
            // $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

// create new Data level 3
var createDataLevel3 = function () {
    // $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create-level-3").serialize();
    let theForm = $("#form-create-level-3");

    var qualityControlId1 = $('#qc_id1_3').val();
    var qualityControlId2 = $('#qc_id2_3').val();

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-3'),
        method: 'POST',
        data: formData,
        success: function(res) {
            var qualityControlId3 = res.qc_id;
            // alert(qualityControlId);
            $('#create-qc-data-3-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            theForm.trigger('reset'); // reset form after successfully create data
            $("#form-create-level-3 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $('#create-qc-data-3-modal').modal('hide');
            $('.daterange-picker').daterangepicker('hide');

            refresh3();
            loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
            // $("#submit-btn").prop('disabled', false); // re-enable button
        }
    })
}

// Form Validation Component
var FormValidation = function() {

    // Validation config
    var _componentValidation = function() {
        if (!$().validate) {
            console.warn('Warning - validate.min.js is not loaded.');
            return;
        }

        // Initialize Create Level 1
        $('#form-create-level-1').validate({
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

        $("#form-create-level-1").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckeditor != 'undefined') {
                ckeditor.updateSourceElement();
            }
            if ($(this).valid()) {
                createDataLevel1();
            }
        });

        // Initialize Create Level 2
        $('#form-create-level-2').validate({
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

        $("#form-create-level-2").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckeditor != 'undefined') {
                ckeditor.updateSourceElement();
            }
            if ($(this).valid()) {
                createDataLevel2();
            }
        });

        // Initialize Create Level 3
        $('#form-create-level-3').validate({
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

        $("#form-create-level-3").on('submit', function(e) {
            e.preventDefault();
            if (typeof ckeditor != 'undefined') {
                ckeditor.updateSourceElement();
            }
            if ($(this).valid()) {
                createDataLevel3();
            }
        });
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

// ==================================
// QC DATA UPDATE FORM LEVEL 1, 2 , 3
// ==================================

// render action datatable level 1
var renderAction1 = (dataId, qualityControlId1, qualityControlId2, qualityControlId3) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData1(`+dataId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <button class="btn btn-light-danger btn-sm" onClick="deleteData1(`+dataId+`,`+qualityControlId1+`,`+qualityControlId2+`,`+qualityControlId3+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </button>  
    `;

    return action;
}

// edit data level 1
var editData1 = function (dataId) {
    $.ajax({
        url: baseUrl('quality-control/edit-qc-data-1/'+ dataId),
        method: 'GET',
        success: function(res){
            
            $("#edit-qc-data-1-modal").modal('show');
            $("#edit-qc-data-1-modal input[name='qc_id_edit']").val(res.qc_id);
            $("#edit-qc-data-1-modal input[name='qc_data_id_edit']").val(res.id);
            $("#edit-qc-data-1-modal input[name='date_edit']").val(moment(res.date).format('MM/DD/YYYY'));
            $("#edit-qc-data-1-modal input[name='qc_data_edit']").val(res.data);
            $("#edit-qc-data-1-modal input[name='position_edit']").val(res.position);
            $("#edit-qc-data-1-modal input[name='qc_edit']").val(res.qc);
            $("#edit-qc-data-1-modal input[name='atlm_edit']").val(res.atlm);
            $("#edit-qc-data-1-modal input[name='recommendation_edit']").val(res.recommendation);
        },
        error: function(res) {

        }
    })
}

// QC data on focus out 1
var QCDataonFocusOutEdit1 = function (){
    var qc_id = $('#qc_id_edit').val();
    var qc_value = $('#qc_data_edit').val();

    // alert(qc_id);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-1-edit/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position_edit').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })
}

// update data level 1
var updateData1 = function () {
    $('#button_update_data').on('click', function() {

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }
        
        var qc_id = $('#qc_id_edit').val();
        var id = $('#qc_data_id_edit').val();
        var date = $('#date_edit').val();
        var qc_data = $('#qc_data_edit').val();
        var position = $('#position_edit').val();
        var qc = $('#qc_edit').val();
        var atlm = $('#atlm_edit').val();
        var recommendation = $('#recommendation_edit').val();

        if(date != '' && qc_data != '' && position != '' && qc != '' && atlm != '' && recommendation != ''){
            $.ajax({
                url: baseUrl('quality-control/update-qc-data-level-1'),
                data: {
                    'id': id,
                    'date': date,
                    'qc_data': qc_data,
                    'position': position,
                    'qc': qc,
                    'atlm': atlm,
                    'recommendation': recommendation,
                },
                method: 'PUT',
                success: function(res) {
                    $("#edit-qc-data-1-modal").modal('hide');
                    toastr.success(res.message, "Update Success!");

                    refresh1();
                    loadGraphData(qc_id, qc_id2, qc_id3, '', '');
                },
                error: function(request, status, error) {
                    toastr.error(request.responseJSON.message);
                }
            });
        }else{
            toastr.error("Please complete the form to continue submission form");
        }
    });
}

// delete data level 1
var deleteData1 = function (dataId, qualityControlId1, qualityControlId2, qualityControlId3) {
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
                url: baseUrl('quality-control/delete-qc-data-level-1/' + dataId + '/' + qualityControlId1),
                method: 'DELETE',
                success: function(res) {
                    var qualityControlId = res.qc_id;
                    toastr.success(res.message, "Delete Success!");
                    
                    refresh1();
                    loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}

// render action datatable level 2
var renderAction2 = (dataId, qualityControlId2, qualityControlId1, qualityControlId3) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData2(`+dataId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="btn btn-light-danger btn-sm" onClick="deleteData2(`+dataId+`,`+qualityControlId2+`,`+qualityControlId1+`,`+qualityControlId3+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </a>  
    `;

    return action;
}

// edit data level 2
var editData2 = function (dataId) {
    $.ajax({
        url: baseUrl('quality-control/edit-qc-data-2/'+ dataId),
        method: 'GET',
        success: function(res){
            $("#edit-qc-data-2-modal").modal('show');
            $("#edit-qc-data-2-modal input[name='qc_id_edit_2']").val(res.qc_id);
            $("#edit-qc-data-2-modal input[name='qc_data_id_edit_2']").val(res.id);
            $("#edit-qc-data-2-modal input[name='date_edit_2']").val(moment(res.date).format('MM/DD/YYYY'));
            $("#edit-qc-data-2-modal input[name='qc_data_edit_2']").val(res.data);
            $("#edit-qc-data-2-modal input[name='position_edit_2']").val(res.position);
            $("#edit-qc-data-2-modal input[name='qc_edit_2']").val(res.qc);
            $("#edit-qc-data-2-modal input[name='atlm_edit_2']").val(res.atlm);
            $("#edit-qc-data-2-modal input[name='recommendation_edit_2']").val(res.recommendation);
        },
        error: function(res) {

        }
    })
}

// QC data on focus out 2
var QCDataonFocusOutEdit2 = function (){
    var qc_id = $('#qc_id_edit_2').val();
    var qc_value = $('#qc_data_edit_2').val();

    // alert(qc_id + ' ' + qc_value);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-2-edit/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position_edit_2').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })
}

// update data level 2
var updateData2 = function () {
    $('#button_update_data_2').on('click', function() {

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }

        var qc_id = $('#qc_id_edit_2').val();
        var id = $('#qc_data_id_edit_2').val();
        var date = $('#date_edit_2').val();
        var qc_data = $('#qc_data_edit_2').val();
        var position = $('#position_edit_2').val();
        var qc = $('#qc_edit_2').val();
        var atlm = $('#atlm_edit_2').val();
        var recommendation = $('#recommendation_edit_2').val();
        
        if(date != '' && qc_data != '' && position != '' && qc != '' && atlm != '' && recommendation != ''){
            $.ajax({
                url: baseUrl('quality-control/update-qc-data-level-2'),
                data: {
                    'id': id,
                    'date': date,
                    'qc_data': qc_data,
                    'position': position,
                    'qc': qc,
                    'atlm': atlm,
                    'recommendation': recommendation,
                },
                method: 'PUT',
                success: function(res) {
                    $("#edit-qc-data-2-modal").modal('hide');
                    toastr.success(res.message, "Update Success!");

                    refresh2();
                    loadGraphData(qc_id1, qc_id, qc_id3, '', '');
                },
                error: function(request, status, error) {
                    toastr.error(request.responseJSON.message);
                }
            });
        }else{
            toastr.error("Please complete the form to continue submission form");
        }
    });
}

// delete data level 2
var deleteData2 = function (dataId, qualityControlId2, qualityControlId1, qualityControlId3) {
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
                url: baseUrl('quality-control/delete-qc-data-level-2/' + dataId + '/' + qualityControlId2),
                method: 'DELETE',
                success: function(res) {
                    var qualityControlId = res.qc_id;
                    toastr.success(res.message, "Delete Success!");
                    
                    refresh2();
                    loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}

// render action datatable level 3
var renderAction3 = (dataId, qualityControlId3, qualityControlId1, qualityControlId2) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData3(`+dataId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="btn btn-light-danger btn-sm" onClick="deleteData3(`+dataId+`,`+qualityControlId3+`,`+qualityControlId1+`,`+qualityControlId2+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </a>  
    `;

    return action;
}

// edit data level 3
var editData3 = function (dataId) {
    $.ajax({
        url: baseUrl('quality-control/edit-qc-data-3/'+ dataId),
        method: 'GET',
        success: function(res){
            $("#edit-qc-data-3-modal").modal('show');
            $("#edit-qc-data-3-modal input[name='qc_id_edit_3']").val(res.qc_id);
            $("#edit-qc-data-3-modal input[name='qc_data_id_edit_3']").val(res.id);
            $("#edit-qc-data-3-modal input[name='day_edit_3']").val(res.day);
            $("#edit-qc-data-3-modal input[name='qc_data_edit_3']").val(res.data);
            $("#edit-qc-data-3-modal input[name='position_edit_3']").val(res.position);
            $("#edit-qc-data-3-modal input[name='qc_edit_3']").val(res.qc);
            $("#edit-qc-data-3-modal input[name='atlm_edit_3']").val(res.atlm);
            $("#edit-qc-data-3-modal input[name='recommendation_edit_3']").val(res.recommendation);
        },
        error: function(res) {

        }
    })
}

// QC data on focus out 3
var QCDataonFocusOutEdit3 = function (){
    var qc_id = $('#qc_id_edit_3').val();
    var qc_value = $('#qc_data_edit_3').val();

    // alert(qc_id + ' ' + qc_value);

    $.ajax({
        url: baseUrl('quality-control/check-position-qc-data-level-3-edit/' + qc_id + '/' + qc_value),
        method: 'get',
        success: function(data) {
            console.log(data);
            $('#position_edit_3').val(data);

        },
        error: function (request, status, error) {
            toastr.error(request.responseJSON.message);
        }
    })
}

// update data level 3
var updateData3 = function () {
    $('#button_update_data_3').on('click', function() {

        // id need
        var id1 = $(this).data('qc-id-1');
        var id2 = $(this).data('qc-id-2');
        var id3 = $(this).data('qc-id-3');

        var qc_id1 = 0;
        var qc_id2 = 0;
        var qc_id3 = 0;

        if(id1){
            qc_id1 = id1;
        }
        if(id2){
            qc_id2 = id2;
        }
        if(id3){
            qc_id3 = id3;
        }

        var qc_id = $('#qc_id_edit_3').val();
        var id = $('#qc_data_id_edit_3').val();
        var date = $('#date_edit_3').val();
        var qc_data = $('#qc_data_edit_3').val();
        var position = $('#position_edit_3').val();
        var qc = $('#qc_edit_3').val();
        var atlm = $('#atlm_edit_3').val();
        var recommendation = $('#recommendation_edit_3').val();
        
        if(date != '' && qc_data != '' && position != '' && qc != '' && atlm != '' && recommendation != ''){
            $.ajax({
                url: baseUrl('quality-control/update-qc-data-level-3'),
                data: {
                    'id': id,
                    'date': date,
                    'qc_data': qc_data,
                    'position': position,
                    'qc': qc,
                    'atlm': atlm,
                    'recommendation': recommendation,
                },
                method: 'PUT',
                success: function(res) {
                    $("#edit-qc-data-3-modal").modal('hide');
                    toastr.success(res.message, "Update Success!");

                    refresh3();
                    loadGraphData(qc_id1, qc_id2, qc_id, '', '');
                },
                error: function(request, status, error) {
                    toastr.error(request.responseJSON.message);
                }
            });
        }else{
            toastr.error("Please complete the form to continue submission form");
        }
    });
}

// delete data level 3
var deleteData3 = function (dataId, qualityControlId3, qualityControlId1, qualityControlId2) {
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
                url: baseUrl('quality-control/delete-qc-data-level-3/' + dataId + '/' + qualityControlId3),
                method: 'DELETE',
                success: function(res) {
                    var qualityControlId = res.qc_id;
                    toastr.success(res.message, "Delete Success!");

                    refresh3();
                    loadGraphData(qualityControlId1, qualityControlId2, qualityControlId3, '', '');
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}

// ==================================
// QC GRAPH LEVEL 1, 2 , 3
// ==================================

var loadGraphData = function (qualityControlId1, qualityControlId2, qualityControlId3, startDate, endDate){
    // console.log(qualityControlId1 + ', ' + qualityControlId2 + ', ' + qualityControlId3);
    $.ajax({
        //  url: baseUrl('quality-control/load-graph-data-level-1/'+ qualityControlId + '/' + startDate + '/' + endDate),
        url: baseUrl('quality-control/load-graph-data/'+ qualityControlId1 + '/' + qualityControlId2 + '/' + qualityControlId3 + '/' + startDate + '/' + endDate),
        method: 'GET',
        success: function(jsons){

            // LEVEL 1
            // =====================================
            var dataset_date1 = [];
            var dataset_qc_value1 = [];
            var dataset_position1 = [];

            // qc
            var test_name = jsons.qc1.test_name;
            var month = jsons.qc1.month;
            var year = jsons.qc1.year;

            // reference data
            // var sd1 = jsons.reference_data1.standard_deviation;
            // var control_name1 = jsons.reference_data1.control_name;
            // var low_value1 = jsons.reference_data1.low_value;
            // var high_value1 = jsons.reference_data1.high_value;
            // var target_value1 = jsons.reference_data1.target_value;
            // var deviation1 = jsons.reference_data1.deviation;

            // qc data
            jsons.qc_data1.forEach(function(json) {
                var date_convert1 = moment(json.date).format("D")

                var date1 = date_convert1;
                var qc_value1 = json.data;
                var position1 = json.position;

                dataset_date1.push(date1);
                dataset_qc_value1.push(qc_value1);
                dataset_position1.push(Number(position1));

                // console.log(date + ' / ' + qc_value + ' / ' + position);
            });

            console.log(dataset_date1);
            console.log(dataset_qc_value1);
            console.log(dataset_position1);


            // LEVEL 2
            // =====================================
            var dataset_position2 = [];
            if(jsons.qc_data2){
                jsons.qc_data2.forEach(function(json) {
                    var position2 = json.position;
                    dataset_position2.push(Number(position2));
                });

                console.log(dataset_position2);
            }


            // LEVEL 3
            // =====================================
            var dataset_position3 = [];
            if(jsons.qc_data3){
                jsons.qc_data3.forEach(function(json) {
                    var position3 = json.position;
                    dataset_position3.push(Number(position3));
                });

                console.log(dataset_position3);
            }
            

            // renderGraph1(test_name, month, year, low_value, high_value, target_value, deviation, dataset_date, dataset_qc_value, dataset_position)
            renderGraph(test_name, month, year, dataset_date1, dataset_position1, dataset_position2, dataset_position3)

        },
        error: function(res) {

        }
    })
}

var renderGraph = function (test_name, month, year, dataset_date1, dataset_position1, dataset_position2, dataset_position3){
    // alert(dataset_position);
    return Highcharts.chart('graph1', {

        title: {
          text: 'Quality Control ' + test_name + ' (' + month + ', ' + year + ')'
        },
      
        // subtitle: {
        //   text: 'Source: <a href="https://irecusa.org/programs/solar-jobs-census/" target="_blank">IREC</a>'
        // },
      
        yAxis: {
          title: {
            text: 'Standard Deviation'
          },
            min: -3,
            max: 3
        },
      
        xAxis: {
          categories : dataset_date1
        },
      
        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
        },
      
        plotOptions: {
          series: {
            label: {
              allowPointSelect: true
            }
          }
        },
      
        // series: [{
        //   name: 'Level 1',
        //   data: dataset_position1
        // //   data: [1, 2, 3, 2, 4, 1, 3]
        // }],

        series: [{
            name: 'Level 1',
            color: '#0095E8',
            data: dataset_position1
          }, {
            name: 'Level 2',
            color: '#5014D0',
            data: dataset_position2,
          },{
            name: 'Level 3',
            color: '#fd7e14',
            data: dataset_position3,
          }],
      
        responsive: {
          rules: [{
            condition: {
              maxWidth: 700
            },
            chartOptions: {
              legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
              }
            }
          }]
        }
      
      });
}

var loadGraphData1 = function (qualityControlId, startDate, endDate){
    $.ajax({
        url: baseUrl('quality-control/load-graph-data-level-1/'+ qualityControlId + '/' + startDate + '/' + endDate),
        method: 'GET',
        success: function(jsons){

            var reference_data = [];
            var dataset_date = [];
            var dataset_qc_value= [];
            var dataset_position = [];

            // qc
            var test_name = jsons.qc.test_name;
            var month = jsons.qc.month;
            var year = jsons.qc.year;

            // reference data
            var sd = jsons.reference_data.standard_deviation;
            var control_name = jsons.reference_data.control_name;
            var low_value = jsons.reference_data.low_value;
            var high_value = jsons.reference_data.high_value;
            var target_value = jsons.reference_data.target_value;
            var deviation = jsons.reference_data.deviation;

            // console.log(sd + ' / ' + control_name + ' / ' + low_value + ' / ' + high_value + ' / ' + target_value + ' / ' + deviation);

            // qc data
            jsons.qc_data.forEach(function(json) {
                var date_convert = moment(json.date).format("D")

                var date = date_convert;
                var qc_value = json.data;
                var position = json.position;

                dataset_date.push(date);
                dataset_qc_value.push(qc_value);
                dataset_position.push(Number(position));

                // console.log(date + ' / ' + qc_value + ' / ' + position);
            });

            console.log(dataset_date);
            console.log(dataset_qc_value);
            console.log(dataset_position);

            renderGraph1(test_name, month, year, low_value, high_value, target_value, deviation, dataset_date, dataset_qc_value, dataset_position)

        },
        error: function(res) {

        }
    })
}

var loadGraphData2 = function (qualityControlId){
    $.ajax({
        url: baseUrl('quality-control/load-graph-data-level-2/'+ qualityControlId),
        method: 'GET',
        success: function(jsons){

            var reference_data = [];
            var dataset_date = [];
            var dataset_qc_value= [];
            var dataset_position = [];

            // qc
            var test_name = jsons.qc.test_name;
            var month = jsons.qc.month;
            var year = jsons.qc.year;

            // reference data
            var sd = jsons.reference_data.standard_deviation;
            var control_name = jsons.reference_data.control_name;
            var low_value = jsons.reference_data.low_value;
            var high_value = jsons.reference_data.high_value;
            var target_value = jsons.reference_data.target_value;
            var deviation = jsons.reference_data.deviation;

            // console.log(sd + ' / ' + control_name + ' / ' + low_value + ' / ' + high_value + ' / ' + target_value + ' / ' + deviation);

            // qc data
            jsons.qc_data.forEach(function(json) {
                var date_convert = moment(json.date).format("D")

                var date = date_convert;
                var qc_value = json.data;
                var position = json.position;

                dataset_date.push(date);
                dataset_qc_value.push(qc_value);
                dataset_position.push(Number(position));

                // console.log(date + ' / ' + qc_value + ' / ' + position);
            });

            console.log(dataset_date);
            console.log(dataset_qc_value);
            console.log(dataset_position);

            renderGraph2(test_name, month, year, low_value, high_value, target_value, deviation, dataset_date, dataset_qc_value, dataset_position)

        },
        error: function(res) {

        }
    })
}

var renderGraph1 = function (test_name, month, year, low_value, high_value, target_value, deviation, dataset_date, dataset_qc_value, dataset_position){
    // alert(dataset_position);
    return Highcharts.chart('graph1', {

        title: {
          text: 'Quality Control ' + test_name + ' (' + month + ', ' + year + ')'
        },
      
        // subtitle: {
        //   text: 'Source: <a href="https://irecusa.org/programs/solar-jobs-census/" target="_blank">IREC</a>'
        // },
      
        yAxis: {
          title: {
            text: 'Standard Deviation'
          },
            min: -3,
            max: 3
        },
      
        xAxis: {
          categories : dataset_date
        },
      
        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
        },
      
        plotOptions: {
          series: {
            label: {
              allowPointSelect: true
            }
          }
        },
      
        series: [{
          name: 'QC Data',
          data: dataset_position
        //   data: [1, 2, 3, 2, 4, 1, 3]
        }],
      
        responsive: {
          rules: [{
            condition: {
              maxWidth: 700
            },
            chartOptions: {
              legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
              }
            }
          }]
        }
      
      });
}

var renderGraph2 = function (test_name, month, year, low_value, high_value, target_value, deviation, dataset_date, dataset_qc_value, dataset_position){
    // alert(dataset_position);
    return Highcharts.chart('graph2', {

        title: {
          text: 'Quality Control ' + test_name + ' (' + month + ', ' + year + ')'
        },
      
        // subtitle: {
        //   text: 'Source: <a href="https://irecusa.org/programs/solar-jobs-census/" target="_blank">IREC</a>'
        // },
      
        yAxis: {
          title: {
            text: 'Standard Deviation'
          },
            min: -3,
            max: 3
        },
      
        xAxis: {
          categories : dataset_date
        },
      
        legend: {
          layout: 'vertical',
          align: 'right',
          verticalAlign: 'middle'
        },
      
        plotOptions: {
          series: {
            label: {
              allowPointSelect: true
            }
          }
        },
      
        series: [{
          name: 'QC Data',
          data: dataset_position
        //   data: [1, 2, 3, 2, 4, 1, 3]
        }],
      
        responsive: {
          rules: [{
            condition: {
              maxWidth: 700
            },
            chartOptions: {
              legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom'
              }
            }
          }]
        }
      
      });
}

// ==================================
// END QC GRAPH LEVEL 1, 2 , 3
// ==================================

// ========================================
// Print Report/Export Excel LEVEL 1, 2 , 3
// ========================================

// Print Report QC Level 1
var PrintReportQCData1 = function() {
    $("#print-qc-data-1").on('click', function() {
      const qc_id = $(this).data('qc-id');

      var date = $('#daterange-table-1').val();
      let start = date.substring(0, 11);
      let end = date.substring(13);
      let startDate = moment(start).format("YYYY-DD-MM");
      let endDate = moment(end).format("YYYY-DD-MM");

      $('#print-qc-data-1').attr('href',baseUrl('quality-control/print-qc-data-level-1/' + qc_id + '/' + startDate + '/' + endDate))
    
    //   $.ajax({
    //     url: baseUrl('quality-control/print-qc-data-level-1/' + qc_id + '/' + startDate + '/' + endDate),
    //     type: 'get',
    //     success: function(data) {
          
    //     //   $("#verificator-hasil-pemeriksaan").val(data.data);
  
    //     //   $("#print-hasil-modal").modal('show');
    //     }
    //   });
  
    });
}

// Print Report QC Level 2
var PrintReportQCData2 = function() {
    $("#print-qc-data-2").on('click', function() {
      const qc_id = $(this).data('qc-id');

      var date = $('#daterange-table-2').val();
      let start = date.substring(0, 11);
      let end = date.substring(13);
      let startDate = moment(start).format("YYYY-DD-MM");
      let endDate = moment(end).format("YYYY-DD-MM");

      $('#print-qc-data-2').attr('href',baseUrl('quality-control/print-qc-data-level-2/' + qc_id + '/' + startDate + '/' + endDate))
    

    });
}

// Print Report QC Level 3
var PrintReportQCData3 = function() {
    $("#print-qc-data-3").on('click', function() {
      const qc_id = $(this).data('qc-id');

      var date = $('#daterange-table-3').val();
      let start = date.substring(0, 11);
      let end = date.substring(13);
      let startDate = moment(start).format("YYYY-DD-MM");
      let endDate = moment(end).format("YYYY-DD-MM");

      $('#print-qc-data-3').attr('href',baseUrl('quality-control/print-qc-data-level-3/' + qc_id + '/' + startDate + '/' + endDate))
    

    });
}

var exportToExcel = function (){
    $.ajax({
        url: baseUrl('quality-control/export-qc-data'),
        method: 'GET',
        success: function(jsons){

        },
        error: function(res) {

        }
    })
}

// ============================================
// End Print Report/Export Excel LEVEL 1, 2 , 3
// ============================================


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var refresh1 = function() {
    $('.datatable-level-1').DataTable().ajax.reload(null, false);
}

var refresh2 = function() {
    $('.datatable-level-2').DataTable().ajax.reload(null, false);
}

var refresh3 = function() {
    $('.datatable-level-3').DataTable().ajax.reload(null, false);
}

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    analyzerOnChange();
    DateRangePicker1();
    DateRangePicker2();
    DateRangePicker3();
    standardDeviationOnChange1();
    standardDeviationOnChange2();
    standardDeviationOnChange3();
    FormValidation.init();
    updateData1();
    updateData2();
    updateData3();
    PrintReportQCData1();
    PrintReportQCData2();
    PrintReportQCData3();

    // single date picker
    $(".daterange-picker").daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            autoclose: true,
            minYear: 1901,
            maxYear: parseInt(moment().format("YYYY"),12)
        }, function(start, end, label) {
            // var years = moment().diff(start, "years");
            // alert("You are " + years + " years old!");
        }
    );

    $('#daterange-table-1').val('');
    $('#daterange-table-2').val('');
    $('#daterange-table-3').val('');
    $('#print-qc-data-1').printPage();
    $('#print-qc-data-2').printPage();
    $('#print-qc-data-3').printPage();
    
});