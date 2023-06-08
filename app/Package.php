<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Package extends Model
{
    protected $with = ['package_tests','group'];
    protected $fillable = [
        'name',
        'group_id',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required',
        ]);
    }

    public function package_tests()
    {
        return $this->hasMany('App\PackageTest', 'package_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }
}
