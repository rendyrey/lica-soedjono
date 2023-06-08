"use strict";
var masterData = 'formula'; // required for the url
var withModel = [];

// required for the datatable columns
var buttonActionIndex = 12;
var columnsDataTable = [
    { data: 'id'},
    { data: 'test_reference_id', render: function(data, type, row){
        var reference = row.test_reference_id + ' - ' + row.test_reference_name;
        return reference;
    } 
    },
    { data: 'a_id', name: 'a_id', render: function(data, type, row){
        var a = row.a_id + ' - ' + row.a_name;
        return a;
    } 
    },
    { data: 'a_operation'},
    { data: 'a_value'},
    { data: 'b_id', name: 'b_id', render: function(data, type, row){
        var b = row.b_id + ' - ' + row.b_name;
        return b;
    } 
    },
    { data: 'b_operation'},
    { data: 'b_value'},
    { data: 'c_id', name: 'c_id', render: function(data, type, row){
        if(row.c_id != null){
            var c = row.c_id + ' - ' + row.c_name;
        }else{
            var c = '';
        }
        
        return c;
    } 
    },
    { data: 'c_operation'},
    { data: 'c_value'},
    { data: 'formulas'}
];

var setValueModalEditForm = function(data)
{
    $("#modal_form_horizontal").modal('show');

    $("#modal_form_horizontal select[name='test_reference']").html(
        `<option value='`+data.test_reference_id+`' selected>`+data.test_reference_id+' - '+data.test_reference_name+`</option>`
    )
    $("#modal_form_horizontal select[name='test_a']").html(
        `<option value='`+data.a_id+`' selected>`+data.a_id+' - '+data.a_name+`</option>`
    )
    $("#modal_form_horizontal input[name='operation_a']").val(data.a_operation);
    $("#modal_form_horizontal input[name='value_a']").val(data.a_value);
    $("#modal_form_horizontal select[name='test_b']").html(
        `<option value='`+data.b_id+`' selected>`+data.b_id+' - '+data.b_name+`</option>`
    )
    $("#modal_form_horizontal input[name='operation_b']").val(data.b_operation);
    $("#modal_form_horizontal input[name='value_b']").val(data.b_value);

    if(data.c_id){
        $("#modal_form_horizontal select[name='test_c']").html(
            `<option value='`+data.c_id+`' selected>`+data.c_id+' - '+data.c_name+`</option>`
        )
        $("#modal_form_horizontal input[name='operation_c']").val(data.c_operation);
        $("#modal_form_horizontal input[name='value_c']").val(data.c_value);
    }

    $("#modal_form_horizontal input[name='formulas']").val(data.formulas);
    
}

// required for the form validation rules
var rulesFormValidation = {
    test_reference: {
        required: true
    },
    test_a: {
        required: true,
    },
    test_b: {
        required: true,
        number: true
    },
    formulas: {
        required: true,
    }
};

// this is for open select2 when pressing tab in keyboard
$(document).on('focus', '.select2-selection.select2-selection--single', function (e) {
    $(this).closest(".select2-container").siblings('select:enabled').select2('open');
});


var Select2TestReference = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData + '-reference').select2({
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
                            if ((theData == 'test') && masterData == 'price') {
                                additionalText = item.classes ? `<i> set for classes (`+item.classes+`)</i>` : '';
                            }
                            if ((theData == 'test')) {
                                PrefixText = item.id+" - "
                                additionalText = " ["+item.initial+"]"
                            }
                            
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

var Select2TestA = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData + '-a').select2({
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
                            if ((theData == 'test') && masterData == 'price') {
                                additionalText = item.classes ? `<i> set for classes (`+item.classes+`)</i>` : '';
                            }
                            if ((theData == 'test')) {
                                PrefixText = item.id+" - "
                                additionalText = " ["+item.initial+"]"
                            }
                            
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

var Select2TestB = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData + '-b').select2({
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
                            if ((theData == 'test') && masterData == 'price') {
                                additionalText = item.classes ? `<i> set for classes (`+item.classes+`)</i>` : '';
                            }
                            if ((theData == 'test')) {
                                PrefixText = item.id+" - "
                                additionalText = " ["+item.initial+"]"
                            }
                            
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

var Select2TestC = function (theData, searchKey = 'name') {
    var _componentSelect2 = function() {
        // Initialize
        $('.select-' + theData + '-c').select2({
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
                            if ((theData == 'test') && masterData == 'price') {
                                additionalText = item.classes ? `<i> set for classes (`+item.classes+`)</i>` : '';
                            }
                            if ((theData == 'test')) {
                                PrefixText = item.id+" - "
                                additionalText = " ["+item.initial+"]"
                            }
                            
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

// On document ready
document.addEventListener('DOMContentLoaded', function () {
    DatatablesServerSide.init();
    FormValidation.init();
    Select2TestReference('test').init();
    Select2TestA('test').init();
    Select2TestB('test').init();
    Select2TestC('test').init();

    $('body').tooltip({
        selector: '[data-popup="tooltip"]',
        trigger: 'hover'
    });

});
