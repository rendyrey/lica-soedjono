<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class GrandPackage extends Model
{
    protected $with = ['grand_package_tests', 'group'];
    protected $fillable = [
        'name',
        'group_id',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                'name' => 'required',
            ]
        );
    }

    public function grand_package_tests()
    {
        return $this->hasMany('App\GrandPackageTest', 'grand_package_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }
}
