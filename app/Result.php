<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Result extends Model
{
    protected $table = 'results';
    protected $fillable = ['test_id', 'result', 'status', 'min_range', 'max_range'];

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                'result' => 'required',
                'test_id' => 'required',
                'status' => 'required'
            ]
        );
    }

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }
}
