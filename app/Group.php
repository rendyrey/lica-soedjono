<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Group extends Model
{
    protected $fillable = [
        'name',
        'general_code',
        'target_tat',
        'target_tat_cito'
    ];

    public function analyzers() {
        // the format for one to many (inverse)
        // return $this->belongsTo('App\User', 'foreign_key', 'other_key');

        // the format for one to many
        // return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Analyzer','group_id','id');
    }

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required'
        ]);
    }
}
