"use strict";
var masterData = 'package'; // required for the url
var withModel = ['group']; // required for the datatable if the model of the datatable has eager load or relationship, set to empty array if not.

// required for the datatable columns
var buttonActionIndex = 4;
var columnsDataTable = [
    { data: 'name' },
    { data: null, render: function(data, type, row){
            let tests = [];
            const show = 6;
            if (row.package_tests.length > show) {
                row.package_tests.slice(0, show).forEach(function(item, index) {
                    tests.push(`<span class="badge badge-secondary">`+item.test.id+` - `+item.test.name+` [`+item.test.initial+`]</span>`);
                });
                row.package_tests.slice(show, row.package_tests.length).forEach(function(item, index) {
                    tests.push(`<span class="badge badge-secondary package-`+row.id+`-item d-none">`+item.test.id+` - `+item.test.name+` [`+item.test.initial+`]</span>`);
                });

                return tests.join(" ") + '<span class="cursor-pointer badge badge-primary package-btn-'+row.id+'" onClick="showMoreTests('+row.id+')"> show ' + (row.package_tests.length - show) + ' more tests</span>';
            } else {
                row.package_tests.forEach(function(item, index) {
                    tests.push(`<span class="badge badge-secondary">`+item.test.id+` - `+item.test.name+` [`+item.test.initial+`]</span>`);
                });
                return tests.join(" ");
            }
        }, searchable: false
    },
    { data: 'group_id', render: function(data, type, row) {
            if (data != null && data != '') {
                return row.group.name;
            }
            return '';
        }
    },
    { data: 'general_code' },
];

var showMoreTests = function(id) {
    $(".package-"+id+"-item").removeClass('d-none');
    $(".package-btn-"+id).addClass('d-none');
}

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');
    $("#modal_form_horizontal input[name='id']").val(data.id);
    $("#modal_form_horizontal input[name='name']").val(data.name);
    $("#modal_form_horizontal input[name='general_code']").val(data.general_code);

    if (data.group_id != null && data.group_id != '') {
        $("#modal_form_horizontal select[name='group_id']").html(
            `<option value='`+data.group_id+`' selected>`+ data.group.name +`</option>`
        );
    } else {
        $("#modal_form_horizontal select[name='group_id']").html('');
    }
    
    $("#modal_form_horizontal select[name='test_ids[]']").html('');
    data.package_tests.forEach(function(item) {
        $("#modal_form_horizontal select[name='test_ids[]']").append(
            `<option value='`+item.test_id+`' selected>`+item.test.id+` - `+item.test.name+` [`+item.test.initial+`]</option>`
        );
    });
}

// required for the form validation rules
var rulesFormValidation = {
    name: {
        required: true
    },
    "test_ids[]": {
        required: true,
    }
};

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2ServerSide('test').init();
    Select2ServerSide('package').init();
    Select2ServerSide('group').init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

    $("#from-another-packages").on('change', function(e) {
        if ($(this).is(':checked')) {
            $("#package-list").removeClass('d-none');
        } else {
            $("#package-list").addClass('d-none');
        }
    })

    $("#select-package-list").on('change', function(e) {
        var packageList = $(this).val();
        if (packageList != '') {
            $.ajax({
                url: baseUrl('master/test-packages/'+packageList.toString()),
                method: 'GET',
                success: function(res){
                    var selectedTestIds = [];
                    var options = '';
                    res.forEach(function(item){
                        // select distinct test id
                        if (selectedTestIds.includes(item.test_id) == false) {
                            selectedTestIds.push(item.test_id);
                            options += `<option value='`+item.test_id+`' selected>`+ item.test.name +`</option>`;
                        }
                    })
                    $("#form-create select[name='test_ids[]']").html(options);
                }
            })
        }
        
    });
});
