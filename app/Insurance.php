<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Insurance extends Model
{
    protected $fillable = [
        'name',
        'discount',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
            [
                'name' => 'required',
                'discount' => 'required|numeric|min:1|max:100',
                'general_code' => 'required'
            ]
        );
    }
}
