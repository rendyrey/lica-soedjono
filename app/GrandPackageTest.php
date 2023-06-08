<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class GrandPackageTest extends Model
{
    protected $with = ['test', 'package'];
    protected $fillable = [
        'test_id',
        'package_id'
    ];

    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }
}
