<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PackageTest extends Model
{
    protected $with = ['test'];
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
