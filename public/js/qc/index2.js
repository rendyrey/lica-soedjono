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
                
                // level 1
                if (data_level1) {
                    var qualityControlId = json.qc_data1.qc_id;
                    autoFillReference1(qualityControlId);
                    level1Table(qualityControlId);
                    loadGraphData1(qualityControlId);
                    // renderGraph1();

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
                    var qualityControlId = json.qc_data2.qc_id;
                    autoFillReference2(qualityControlId);
                    level2Table(qualityControlId);

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
                    var qualityControlId = json.qc_data3.qc_id;
                    autoFillReference3(qualityControlId);
                    level3Table(qualityControlId);

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
        }
    });
}

var standardDeviationOnChange2 = () => {
    $('#standard_deviation2').on('change', function() {
        if(this.value == '2' || this.value == '3'){
            $('#control_name2').prop('disabled', false)
            $('#low_value2').prop('disabled', false)
            $('#high_value2').prop('disabled', false)    
        }
    });
}

var standardDeviationOnChange3 = () => {
    $('#standard_deviation3').on('change', function() {
        if(this.value == '2' || this.value == '3'){
            $('#control_name3').prop('disabled', false)
            $('#low_value3').prop('disabled', false)
            $('#high_value3').prop('disabled', false)    
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

// Tabel QC Data & Modal Form Level 1
var level1Table = (qualityControlId) => {

    $('#add-qc-data-1').prop('disabled', false);

    $("#add-qc-data-1").on('click', function() {
        $("#add-qc-data-1").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-1/' + qualityControlId),
              type: 'get',
              success: function(res) {
                $("#qc_id").val(res.qc_id);
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
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId),
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
                        'action': renderAction1(json.id, json.qc_id),
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
        ]
    });
}

// Tabel QC Data & Modal Form Level 2
var level2Table = (qualityControlId) => {

    $('#add-qc-data-2').prop('disabled', false);

    $("#add-qc-data-2").on('click', function() {
        $("#add-qc-data-2").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-2/' + qualityControlId),
              type: 'get',
              success: function(res) {
                $("#qc_id_2").val(res.qc_id);
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
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId),
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
                        'action': renderAction2(json.id),
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
var level3Table = (qualityControlId) => {

    $('#add-qc-data-3').prop('disabled', false);

    $("#add-qc-data-3").on('click', function() {
        $("#add-qc-data-3").on('click', function() {
          
            $.ajax({
              url: baseUrl('quality-control/get-reference-3/' + qualityControlId),
              type: 'get',
              success: function(res) {
                $("#qc_id_3").val(res.qc_id);
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
            "url": baseUrl('quality-control/datatable-qc-data/' + qualityControlId),
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
                        'action': renderAction3(json.id),
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
        required: true,
        number: true
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
        required: true,
        number: true
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
    var qc_id = $('#qc_id').val();
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

// create new Data level 1
var createDataLevel1 = function () {
    // $("#submit-btn").prop('disabled', true); // disabled button

    let formData = $("#form-create-level-1").serialize();
    let theForm = $("#form-create-level-1");

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-1'),
        method: 'POST',
        data: formData,
        success: function(res) {
            var qualityControlId = res.qc_id;
            $('#create-qc-data-1-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            theForm.trigger('reset'); // reset form after successfully create data
            $("#form-create-level-1 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            $('.daterange-picker').daterangepicker('hide');

            refresh();
            loadGraphData1(qualityControlId);
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

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-2'),
        method: 'POST',
        data: formData,
        success: function(res) {
            $('#create-qc-data-2-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            refresh();
            theForm.trigger('reset'); // reset form after successfully create data
            $(".select-two").val(null).trigger("change"); // unselect all the select form
            $("#form-create-level-2 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            ckeditor.setData(''); //remove value ckeditor
            $('#create-qc-data-2-modal').modal('hide');

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

    $.ajax({
        url: baseUrl('quality-control/create-qc-data-level-3'),
        method: 'POST',
        data: formData,
        success: function(res) {
            $('#create-qc-data-3-modal').modal('hide');
            toastr.success(res.message, "Create Success!");
            refresh();
            theForm.trigger('reset'); // reset form after successfully create data
            $(".select-two").val(null).trigger("change"); // unselect all the select form
            $("#form-create-level-3 input:visible:enabled:first").trigger('focus'); // set focus to first element of input
            ckeditor.setData(''); //remove value ckeditor
            $('#create-qc-data-3-modal').modal('hide');

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
var renderAction1 = (dataId, qualityControlId) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData1(`+dataId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="btn btn-light-danger btn-sm" onClick="deleteData1(`+dataId+`,`+qualityControlId+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </a>  
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

    console.log(qc_id);

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

                    refresh();
                    loadGraphData1(qc_id);
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
var deleteData1 = function (dataId, qualityControlId) {
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
                url: baseUrl('quality-control/delete-qc-data-level-1/' + dataId + '/' + qualityControlId),
                method: 'DELETE',
                success: function(res) {
                    var qualityControlId = res.qc_id;
                    toastr.success(res.message, "Delete Success!");
                    
                    refresh();
                    loadGraphData1(qualityControlId);
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}

// render action datatable level 2
var renderAction2 = (qualityControlId) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData2(`+qualityControlId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="btn btn-light-danger btn-sm" onClick="deleteData2(`+qualityControlId+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </a>  
    `;

    return action;
}

// edit data level 2
var editData2 = function (qualityControlId) {
    $.ajax({
        url: baseUrl('quality-control/edit-qc-data-2/'+ qualityControlId),
        method: 'GET',
        success: function(res){
            $("#edit-qc-data-2-modal").modal('show');
            $("#edit-qc-data-2-modal input[name='qc_data_id_edit_2']").val(res.id);
            $("#edit-qc-data-2-modal input[name='day_edit_2']").val(res.day);
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

// update data level 2
var updateData2 = function () {
    $('#button_update_data_2').on('click', function() {

        var id = $('#qc_data_id_edit_2').val();
        var day = $('#day_edit_2').val();
        var qc_data = $('#qc_data_edit_2').val();
        var position = $('#position_edit_2').val();
        var qc = $('#qc_edit_2').val();
        var atlm = $('#atlm_edit_2').val();
        var recommendation = $('#recommendation_edit_2').val();

        if(day != '' && qc_data != '' && position != '' && qc != '' && atlm != '' && recommendation != ''){
            $.ajax({
                url: baseUrl('quality-control/update-qc-data-level-2'),
                data: {
                    'id': id,
                    'day': day,
                    'qc_data': qc_data,
                    'position': position,
                    'qc': qc,
                    'atlm': atlm,
                    'recommendation': recommendation,
                },
                method: 'PUT',
                success: function(res) {
                    $("#edit-qc-data-2-modal").modal('hide');
                    refresh();
                    toastr.success(res.message, "Update Success!");
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
var deleteData2 = function (qualityControlId) {
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
                url: baseUrl('quality-control/delete-qc-data-level-2/'+qualityControlId),
                method: 'DELETE',
                success: function(res) {
                    refresh();
                    toastr.success(res.message, "Delete Success!");
                },
                error: function(request, status, error){
                    toastr.error(request.responseJSON.message);
                }
            })
        }
    });
}

// render action datatable level 3
var renderAction3 = (qualityControlId) => {
    var action = `
    <a class="btn btn-light-primary btn-sm" onClick="editData3(`+qualityControlId+`)" data-toggle="tooltip" data-placement="top" title="Edit Data">
    <i class="fas fa-pencil-alt"></i>
    </a>
    <a class="btn btn-light-danger btn-sm" onClick="deleteData3(`+qualityControlId+`)" data-toggle="tooltip" data-placement="top" title="Delete Data">
    <i class="fas fa-trash"></i>
    </a>  
    `;

    return action;
}

// edit data level 3
var editData3 = function (qualityControlId) {
    $.ajax({
        url: baseUrl('quality-control/edit-qc-data-3/'+ qualityControlId),
        method: 'GET',
        success: function(res){
            $("#edit-qc-data-3-modal").modal('show');
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

// update data level 3
var updateData3 = function () {
    $('#button_update_data_3').on('click', function() {

        var id = $('#qc_data_id_edit_3').val();
        var day = $('#day_edit_3').val();
        var qc_data = $('#qc_data_edit_3').val();
        var position = $('#position_edit_3').val();
        var qc = $('#qc_edit_3').val();
        var atlm = $('#atlm_edit_3').val();
        var recommendation = $('#recommendation_edit_3').val();

        if(day != '' && qc_data != '' && position != '' && qc != '' && atlm != '' && recommendation != ''){
            $.ajax({
                url: baseUrl('quality-control/update-qc-data-level-3'),
                data: {
                    'id': id,
                    'day': day,
                    'qc_data': qc_data,
                    'position': position,
                    'qc': qc,
                    'atlm': atlm,
                    'recommendation': recommendation,
                },
                method: 'PUT',
                success: function(res) {
                    $("#edit-qc-data-3-modal").modal('hide');
                    refresh();
                    toastr.success(res.message, "Update Success!");
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
var deleteData3 = function (qualityControlId) {
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
                url: baseUrl('quality-control/delete-qc-data-level-3/'+qualityControlId),
                method: 'DELETE',
                success: function(res) {
                    refresh();
                    toastr.success(res.message, "Delete Success!");
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

// graph level 1
// var renderGraph1 = function (){

//     var xValues = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31];
//     var yValues = [7,8,8,9,9,9,10,11,14,14,15];

//     return new Chart("graph1", {
//     type: "line",
//     data: {
//         labels: xValues,
//         datasets: [{
//         backgroundColor: "#F9B224",
//         borderColor: "orange",
//         fill: false,
//         data: yValues
//         }]
//     },
//     options: {
//         legend: {display: false},
//         scales: {
//           yAxes: [{ticks: {min: 6, max:16}}],
//         }
//     }
//     });

// }

var loadGraphData1 = function (qualityControlId){
    $.ajax({
        url: baseUrl('quality-control/load-graph-data-level-1/'+ qualityControlId),
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


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var refresh = function() {
    $('.datatable-level-1').DataTable().ajax.reload(null, false);
    // $('.datatable-level-2').DataTable().ajax.reload(null, false);
    // $('.datatable-level-3').DataTable().ajax.reload(null, false);
}

// =====================
// END QC DATA FORM
// =====================

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    analyzerOnChange();
    standardDeviationOnChange1();
    standardDeviationOnChange2();
    standardDeviationOnChange3();
    FormValidation.init();
    updateData1();
    updateData2();
    updateData3();
    // clearData();
    // DatatablesLevel1DataServerSide.init();
    // DatatablesLevel2DataServerSide.init();

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
    
});