<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Doctor extends Model
{
    protected $fillable = [
        'name',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
            [
                'name' => 'required'
            ]
        );
    }
}
