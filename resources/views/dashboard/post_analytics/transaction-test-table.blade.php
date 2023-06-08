<?php
function printPackageName($value)
{
    $checked = $value->print_package_name ? 'checked' : '';
    return '<input data-transaction-test-id="' . $value->id . '" data-package-id="' . $value->package_id  . '" data-transaction-id="' . $value->transaction_id . '" class="form-check-input print-package-name-checkbox" id="print-package-name-checkbox-id-' . $value->id . '" type="checkbox" value="" ' . $checked . ' />';
}
?>

<?php
function testMemo($value)
{
    $fill = $value->memo_test ? '-fill' : '';
    $icon = '<i class="bi bi-pencil' . $fill . '" style="cursor:pointer" onClick="memoTestModal(' . $value->id . ',' . $value->transaction_id . ',`' . $value->memo_test . '`)"></i>';

    return $icon;
}

function getResult($value, $tabIndex)
{
    if ($value->result_number) {
        return $value->result_number;
    } else if ($value->result_label) {
        return $value->result_label;
    } else if ($value->result_text) {
        return $value->result_text;
    } else {
        return '-';
    }
}

function labelType($value, $transactionTestId, $testId, $tabIndex)
{
    $results = \App\Result::where('test_id', $testId)->get();
    $options = '<option value=""></option>';
    foreach ($results as $result) {
        $selected = $value->result_label == $result->id ? 'selected' : '';
        $options .= '<option value="' . $result->id . '" "' . $selected . '">' . $result->result . '</option>';
    }
    $input = '
            <select tabindex="' . $tabIndex . '" id="select-result-label-' . $transactionTestId . '" data-transaction-test-id="' . $transactionTestId . '" data-transaction-id="' . $value->transaction_id . '" data-control="select2" data-placeholder="Select label" class="select select-result-label form-select form-select-sm form-select-solid my-0 me-4">
                ' . $options . '
            </select>
        ';

    return $input;
}

function numberType($value, $transactionTestId, $testId, $tabIndex)
{
    $checkMasterRange = \App\Range::where('test_id', $testId)->exists();
    if (!$checkMasterRange) {
        return 'PLEASE SET RESULT RANGE';
    }
    $input = '
            <input type="text" class="form-control form-control-sm result-number" data-transaction-id="' . $value->transaction_id . '" data-transaction-test-id="' . $value->id . '" tabindex="' . $tabIndex . '" value="' . $value->result_number . '">
        ';
    return $input;
}

function descriptionType($value, $testId, $tabIndex)
{
    // $range = \App\Range::where('test_id', $testId)->first();
    $result = $value->result_text;
    $input = '
            <textarea class="result-description form-control" tabindex="' . $tabIndex . '" data-transaction-id="' . $value->transaction_id . '" data-transaction-test-id="' . $value->id . '">' . $result . '</textarea>
        ';

    return $input;
}

function normalRef($patient, $value, $tabIndex)
{
    // nilai normal text description nggak ada
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

    return '-';
}

function labelInfo($result_status)
{
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

function verifyCheckbox($value)
{
    $checked = $value->verify ? 'checked' : '';
    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                <input 
                data-result-status="' . $value->result_status . '" 
                data-transaction-test-id="' . $value->id . '" 
                data-transaction-id="' . $value->transaction_id . '" 
                data-test-name="' . $value->test->name . '"
                data-result="' . ($value->result_number ? $value->result_number : $value->res_label) . '"
                class="form-check-input verify-checkbox"
                id="verify-checkbox-id-' . $value->id . '" 
                type="checkbox" value="" ' . $checked . '/>
                </div>';
}

function validateCheckbox($value)
{
    $checked = $value->validate ? 'checked' : '';
    $disabled = $value->verify ? '' : 'disabled';
    $checkboxDisabled = $value->verify ? '' : 'style="cursor: not-allowed"';
    return '<div class="form-check form-check-sm form-check-custom form-check-solid ' . $checkboxDisabled . '" ' . $checkboxDisabled . '>
                    <input ' . $disabled . ' data-transaction-test-id="' . $value->id . '" data-transaction-id="' . $value->transaction_id . '" class="form-check-input validate-checkbox" type="checkbox" value="" ' . $checked . '/>
                </div>';
}

function isPrint($value)
{
    $checked = $value->is_print ? 'checked' : '';
    return '<div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input id="print-checkbox-id-' . $value->id . '"  data-transaction-test-id="' . $value->id . '" data-transaction-id="' . $value->transaction_id . '" class="form-check-input validate-checkbox" type="checkbox" value="" ' . $checked . '/>
                </div>';
}

?>

@if(isset($table))
@php
$currentGroupName = '';
$currentPackageName = '';
@endphp

@foreach($table as $key => $value)

@php
$class_duplo = '';
if($value->mark_duplo){
$class_duplo = 'duplo';
}
$testId = $value->id;
$groupName = $value->test->group->name;
$packageName = $value->package_name;

if ($currentGroupName != $groupName) {
$currentGroupoName = $groupName;
$packageName = $value->package_name;
}
$key++; // this is for tab index
@endphp


@if($currentGroupName != $groupName)
<tr>
    <td colspan="7">
        <h5>{{$groupName}}</h5>
    </td>
</tr>
@php
$currentGroupName = $groupName;
@endphp
@endif

@if($packageName != null || $packageName != '')
@if($currentPackageName != $packageName)
<tr>
    <td colspan="7" style="font-size: 10; font-weight:bold">
        {{$packageName}}

        <span> {!! printPackageName($value) !!} </span>
    </td>
</tr>
@php
$currentPackageName = $packageName;
@endphp
@endif
@endif

<tr class="<?php echo $class_duplo ?>">

    <td style="border-right: 1px solid grey">
        <div class="d-flex justify-content-between">
            <span>{{$value->test->name}}</span><span>{!! testMemo($value) !!}</span>
        </div>
    </td>
    <td style="border-right: 1px solid grey">
        {!! $value->global_result !!}
    </td>
    <td style="border-right: 1px solid grey">{!! $value->normal_value !!}</td>
    <td class="text-center" style="border-right: 1px solid grey" id="label-info-{{$value->id}}">{!! labelInfo($value->result_status) !!}</td>
    <td class="text-center" style="border-right: 1px solid grey">{!! $value->report_to !!}</td>
    <td class="text-center" style="border-right: 1px solid grey">{!! $value->report_by !!}</td>
    <td class="text-center" style="border-right: 1px solid grey">{!! isPrint($value) !!}</td>
</tr>
@endforeach
@endif

<script>
    $(".print-package-name-checkbox").on('change', function(e) {
        const transactionId = $(this).data('transaction-id');
        const packageId = $(this).data('package-id');

        const value = e.target.checked ? 1 : 0;
        const msg = value ? 'print' : 'not be print';
        $.ajax({
            url: baseUrl('post-analytics/print-package-name/' + transactionId + '/' + packageId),
            type: 'put',
            data: {
                value: value
            },
            success: function(res) {
                toastr.success("This package will be " + msg + " in result");
                onSelectTransaction(transactionId);
            }
        })

    });
</script>