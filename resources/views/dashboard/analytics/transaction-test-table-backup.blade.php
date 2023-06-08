@php
function testMemo($value) {
$class = $value->memo_test ? '' : 'hidden';
$icon = '<i data-toggle="tooltip" data-placement="top" title="'.$value->memo_test.'" class="bi bi-pencil " style="cursor:pointer" onClick="memoTestModal('.$value->id.','.$value->transaction_id.',`'.$value->memo_test.'`)"></i>';

$notif = ' <span class="note-notif ">'.$icon.'<span class="badge '.$class.'">&nbsp;</span></span>';

return $notif;
}

function labelType($value, $transactionTestId, $testId, $tabIndex) {
$disabled = '';
if($value->mark_duplo == 1){
$disabled='disabled';
}
$results = \App\Result::where('test_id', $testId)->get();
$options = '<option value=""></option>';
foreach($results as $result) {
$selected = $value->result_label == $result->id ? 'selected' : '';
$options .= '<option value="'.$result->id.'" "'.$selected.'">'.$result->result.'</option>';
}
$input = '
<select '.$disabled.' tabindex="'.$tabIndex.'" id="select-result-label-'.$transactionTestId.'" data-transaction-test-id="'.$transactionTestId.'" data-transaction-id="'.$value->transaction_id.'" data-control="select2" data-placeholder="Select label" class="select select-result-label form-select form-select-sm form-select-solid my-0 me-4">
    '.$options.'
</select>
';

return $input;
}

function numberType($value, $transactionTestId, $testId, $tabIndex) {
$disabled = '';
if($value->mark_duplo == 1){
$disabled='disabled';
}
$checkMasterRange = \App\Range::where('test_id', $testId)->exists();
if (!$checkMasterRange) {
return 'PLEASE SET RESULT RANGE';
}
$input = '
<input '.$disabled.' type="text" class="form-control form-control-sm result-number" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$value->id.'" tabindex="'.$tabIndex.'" value="'.$value->result_number.'">
';
return $input;
}

function descriptionType($value, $testId, $tabIndex) {
// $range = \App\Range::where('test_id', $testId)->first();
$result = $value->result_text;
$disabled = '';
if($value->mark_duplo == 1){
$input = '
<textarea id="desc'.$value->id.'" data-existing="'.$result.'" disabled class="result-description form-control" tabindex="'.$tabIndex.'" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$value->id.'">'.$result.'</textarea>
';
}else{
$input = '
<textarea id="desc'.$value->id.'" data-existing="'.$result.'" onClick="openModalDescriptionEditor('.$value->id.')" class="result-description form-control" tabindex="'.$tabIndex.'" data-transaction-id="'.$value->transaction_id.'" data-transaction-test-id="'.$value->id.'">'.$result.'</textarea>
';
}


return $input;
}

function normalRef($patient, $value, $tabIndex) {
// nilai normal text description dan label langsung menggunakan normal notes

if ($value->test->range_type == 'description' || $value->test->range_type == 'label') {
return $value->test->normal_notes;
}

$bornDate = $patient->birthdate;
$ageInDays = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $bornDate)->diffInDays(\Illuminate\Support\Carbon::now());
$range = \App\Range::where('test_id', $value->test_id)->where('min_age', '<=', $ageInDays)->where('max_age', '>=', $ageInDays)->first();

    if ($range) {
    if ($patient->gender == 'M') {
    // return $range->min_male_ref . '-' . $range->max_male_ref;
    return $range->normal_male;
    }
    // return $range->min_female_ref . '-' . $range->max_female_ref;
    return $range->normal_female;
    }

    return "-";
    }

    function labelInfo($result_status) {
    switch ($result_status) {
    case 1: // normal
    return '<span class="badge badge-sm badge-circle badge-success" data-toggle="tooltip" data-placement="top" title="Normal">N</span>';
    case 2: // low
    return '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="Low">L</span>';
    case 3: // high
    return '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="High">H</span>';
    case 4: // abnormal
    return '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="Abnormal">A</span>';
    case 5: // critical
    return '<span class="badge badge-sm badge-circle badge-danger" data-toggle="tooltip" data-placement="top" title="Critical">C</span>';
    }
    }

    function verifyCheckbox($value) {
    $checked = $value->verify ? 'checked' : '';
    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
        <input data-result-status="'.$value->result_status.'" data-transaction-test-id="'.$value->id.'" data-transaction-id="'.$value->transaction_id.'" data-test-name="'.$value->test->name.'" data-result="'.($value->result_number ? $value->result_number : $value->res_label).'" class="form-check-input verify-checkbox" id="verify-checkbox-id-'.$value->id.'" type="checkbox" value="" '.$checked.' />
    </div>';
    }

    function validateCheckbox($value) {
    $checked = $value->validate ? 'checked' : '';
    $disabled = $value->verify ? '' : 'disabled';
    $checkboxDisabled = $value->verify ? '' : 'style="cursor: not-allowed"';
    return '<div class="form-check form-check-sm form-check-custom form-check-solid '.$checkboxDisabled.'" '.$checkboxDisabled.'>
        <input '.$disabled.' data-transaction-test-id="'.$value->id.'" data-transaction-id="'.$value->transaction_id.'" data-group-id="'.$value->group_id.'" class="form-check-input validate-checkbox validate-checkbox-'.$value->group_id.'" type="checkbox" value="" '.$checked.' />
    </div>';
    }

    function deleteTest($value) {
    $disabled = ($value->verify || $value->validate) ? 'disabled' : '';
    $checkboxDisabled = ($value->verify || $value->validate) ? 'style="cursor: not-allowed"' : '';
    if($value->mark_duplo){
    return '';
    }else{
    return '<div class="form-check form-check-sm form-check-custom form-check-solid '.$checkboxDisabled.'" '.$checkboxDisabled.'>
        <i '.$disabled.' class="fa fa-trash" style="color:red" title="delete Test" onClick="deleteTest('.$value->transaction_id.','.$value->id.')">
    </div>';
    }
    }

    function duplo($value) {
    $icon = '<i data-toggle="tooltip" data-placement="top" title="Duplo" class="bi bi-arrow-clockwise" style="cursor:pointer" onClick="openduploModal('.$value->id.','.$value->transaction_id.','.$value->test_id.',`'.$value->test->name.'`)"></i>';
    if($value->mark_duplo){
    $icon = '';
    }

    return $icon;
    }

    @endphp

    @if(isset($table))
    @php
    $currentGroupName = ''
    @endphp

    @foreach($table as $key => $value)
    @php
    $testId = $value->id;
    $groupName = $value->test->group->name;
    if ($currentGroupName != $groupName) {
    $currentGroupoName = $groupName;
    }
    $key++; // this is for tab index
    @endphp
    @php
    $PRINTTESTPERGROUP = config('licaconfig.PRINTTESTPERGROUP');
    @endphp

    @if($currentGroupName != $groupName)
    <tr>
        <td colspan="8">
            <h5 class="group-name" data-group-id="{{$value->group_id}}">{{$groupName}}
                @if($PRINTTESTPERGROUP == true)
                <a href="{{ url('/printAnalyticResult/'.$value->transaction_id.'/'.$value->group_id) }}" class="btn btn-primary btn-sm hidden ml-2 btn-print-group" type="button" id="btn-print-group-{{$value->group_id}}">Print</a>
                @endif
            </h5>
        </td>
    </tr>
    @php
    $currentGroupName = $groupName;
    @endphp
    @endif
    <tr class="row-test-<?php echo $value->id ?>">

        <td style="border-right: 1px solid grey">
            <div class="d-flex justify-content-between">
                <span>{{$value->test->initial}}</span><span>{!! testMemo($value) !!}</span>
            </div>
        </td>
        <td style="border-right: 1px solid grey">
            @if ($value->test->range_type == 'label')
            {!! labelType($value, $value->id, $value->test_id, $key) !!}
            @elseif($value->test->range_type == 'number')
            {!! numberType($value, $value->id, $value->test_id, $key) !!}
            @elseif($value->test->range_type == 'description')
            {!! descriptionType($value, $value->test_id, $key) !!}
            @elseif($value->test->range_type == 'free_formatted_text')
            {!! descriptionType($value, $value->test_id, $key) !!}
            @endif
        </td>
        <td style="border-right: 1px solid grey">{!! normalRef($transaction->patient, $value, $key) !!}</td>
        <td class="text-center" style="border-right: 1px solid grey" id="label-info-{{$value->id}}">{!! labelInfo($value->result_status) !!}</td>
        <td class="text-center" style="border-right: 1px solid grey">{!! verifyCheckbox($value) !!}</td>
        <td>{!! validateCheckbox($value) !!}</td>
        <td>{!! deleteTest($value) !!}</td>
        <td>{!! duplo($value) !!}</td>
    </tr>
    @endforeach
    @endif

    <script>
        var current_result = "";
        $(".result-number").on('focus', function(e) {
            const value = $(this).val();
            current_result = value;
        });
        $(".result-number").on('blur', function(e) {
            const value = $(this).val();
            const transactionTestId = $(this).data('transaction-test-id');
            const transactionId = $(this).data('transaction-id');
            const component = $(this);
            if (current_result != value && value != '') {
                $.ajax({
                    url: baseUrl('analytics/update-result-number/' + transactionTestId),
                    type: 'put',
                    data: {
                        result: value
                    },
                    success: function(res) {
                        toastr.success("Success update result number");
                        // refreshPatientDatatables(transactionId);
                        DatatableAnalytics.refreshTable();
                        let label = '';
                        switch (res.label) {
                            case 1: // normal
                                label = '<span class="badge badge-sm badge-circle badge-success" data-toggle="tooltip" data-placement="top" title="Normal">N</span>'
                                break;
                            case 2: // low
                                label = '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="Low">L</span>';
                                break;
                            case 3: // high
                                label = '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="High">H</span>';
                                break;
                            case 4: // abnormal
                                label = '<span class="badge badge-sm badge-circle badge-warning" data-toggle="tooltip" data-placement="top" title="Abnormal">A</span>';
                                break;
                            case 5: // critical
                                label = '<span class="badge badge-sm badge-circle badge-danger" data-toggle="tooltip" data-placement="top" title="Critical">C</span>';
                                break;
                        }
                        $("#verify-checkbox-id-" + transactionTestId + "").data('result-status', res.label);
                        $("#label-info-" + transactionTestId).html(label);

                    },
                    error: function(request, status, error) {
                        toastr.error(request.responseJSON.message);
                        component.focus();
                        onSelectTransaction(transactionId);
                    }
                });
            }
        });

        $(".result-description").on('change', function(e) {
            const value = $(this).val();
            const transactionTestId = $(this).data('transaction-test-id');
            const transactionId = $(this).data('transaction-id');
            const component = $(this);
            $.ajax({
                url: baseUrl('analytics/update-result-description/' + transactionTestId),
                type: 'put',
                data: {
                    result: value
                },
                success: function(res) {
                    toastr.success("Success update result description");
                },
                error: function(request, status, error) {
                    toastr.error(request.responseJSON.message);
                    component.focus();
                }
            });
        });


        $(".verify-checkbox").on('change', function(e) {
            const transactionTestId = $(this).data('transaction-test-id');
            const transactionId = $(this).data('transaction-id');
            const resultStatus = $(this).data('result-status');
            const testName = $(this).data('test-name');
            const result = $(this).data('result');

            if (resultStatus == 5 && e.target.checked) {
                let criticalTest = '';
                criticalTest += '<li>' + testName + '  <i>value: </i>' + result + '</li>';
                $("#critical-tests").html(criticalTest);
                $("#critical-modal input[name='transaction_test_ids']").val(transactionTestId);
                $("#critical-modal input[name='transaction_id']").val(transactionId);
                $("#critical-modal").modal('show');
                e.target.checked = false;
                return;
            }

            const value = e.target.checked ? 1 : 0;
            const msg = value ? 'verify' : 'unverify';
            $.ajax({
                url: baseUrl('analytics/verify-test/' + transactionTestId),
                type: 'put',
                data: {
                    value: value
                },
                success: function(res) {
                    toastr.success("Success " + msg + " test result");
                    onSelectTransaction(transactionId);
                },
                error: function(request, status, error) {
                    toastr.error(request.responseJSON.message);
                    onSelectTransaction(transactionId);
                }
            });
        });

        $(".validate-checkbox").on('change', function(e) {
            const transactionTestId = $(this).data('transaction-test-id');
            const transactionId = $(this).data('transaction-id');
            const value = e.target.checked ? 1 : 0;
            const msg = value ? 'validate' : 'unvalidate';
            $.ajax({
                url: baseUrl('analytics/validate-test/' + transactionTestId),
                type: 'put',
                data: {
                    value: value
                },
                success: function(res) {
                    toastr.success("Success " + msg + " test result");
                    onSelectTransaction(transactionId);
                }
            })
        });

        function deleteTest(transactionId, id) {
            Swal.fire({
                title: 'Are you sure want to delete this Test ?',
                text: 'This action cannot be undo,',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: 'btn btn-danger'
                }
            }).then(function(isConfirm) {
                if (isConfirm.value) {
                    $.ajax({
                        url: baseUrl('analytics/delete-transaction-test/' + id),
                        type: 'delete',
                        success: function(res) {
                            toastr.success("Success delete test");
                            onSelectTransaction(transactionId);
                        },
                        error: function(request, status, error) {
                            toastr.error(request.responseJSON.message);
                            onSelectTransaction(transactionId);
                        }
                    })
                }
            });

        }


        // if ( ! $.fn.DataTable.isDataTable( '.transaction-test-table' ) ) {
        //     DatatableTest.init();
        // } else {
        //     DatatableTest.destroy();
        //     DatatableTest.init();
        // }
    </script>