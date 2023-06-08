<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Formula extends Model
{
    protected $fillable = [
        'id',
        'test_reference_id',
        'test_reference_name',
        'a_id',
        'a_name',
        'a_operation',
        'a_value',
        'b_id',
        'b_name',
        'b_operation',
        'b_value',
        'c_id',
        'c_name',
        'c_operation',
        'c_value',
        'formulas'
    ];

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                // 'name' => 'required',
                // 'price' => 'required',
                // 'general_code' => 'required',
            ]
        );
    }
}
