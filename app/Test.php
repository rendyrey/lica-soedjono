<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Test extends Model
{
    const RANGE_TYPE = [
        'number' => 'Number',
        'label' => 'Label',
        'description' => 'Description',
        'free_formatted_text' => 'Free Formatted Text'
    ];

    protected $with = ['group', 'specimen'];

    protected $fillable = [
        'specimen_id',
        'group_id',
        'name',
        'initial',
        'unit',
        'volume',
        'range_type',
        'sequence',
        'sub_group',
        'normal_notes',
        'general_code',
        'format_decimal',
        'format_diff_count'
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

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }

    public function specimen()
    {
        return $this->belongsTo('App\Specimen', 'specimen_id', 'id');
    }
}
