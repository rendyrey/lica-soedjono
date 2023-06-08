<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class GeneralCodeTest extends Model
{
    protected $with = ['test'];

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }
    protected $fillable = [
        'test_id',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                'test_id' => 'required',
                'general_code' => 'required'
            ]
        );
    }
}
