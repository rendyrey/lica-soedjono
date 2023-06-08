<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Interfacing extends Model
{
    protected $with = ['test','analyzer'];
    protected $fillable = [
        'test_id',
        'analyzer_id',
        'code'
    ];

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                'analyzer_id' => 'required'
            ]
        );
    }

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }

    public function analyzer()
    {
        return $this->belongsTo('App\Analyzer', 'analyzer_id', 'id');
    }
}
