<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Range extends Model
{
    protected $fillable = [
        'test_id',
        'min_age',
        'max_age',
        'min_crit_male',
        'max_crit_male',
        'min_crit_female',
        'max_crit_female',
        'min_male_ref',
        'max_male_ref',
        'min_female_ref',
        'max_female_ref',
        'normal_male',
        'normal_female'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
            [
                'test_id' => 'required',
                'min_age' => 'required',
                'max_age' => 'required',
                'min_male_ref' => 'required',
                'max_male_ref' => 'required',
                'min_female_ref' => 'required',
                'max_female_ref' => 'required',
            ]
        );
    }
}
