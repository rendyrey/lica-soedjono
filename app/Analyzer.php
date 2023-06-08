<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Analyzer extends Model
{
    protected $with = ['group']; // eager loding by default
    protected $fillable = [
        'group_id',
        'name'
    ];

    /**
     * set the foreign key into
     */
    public function group()
    {
        // the format for one to many (inverse)
        // return $this->belongsTo('App\User', 'foreign_key', 'other_key');

        // the format for one to many
        // return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->belongsTo('App\Group', 'group_id', 'id');
    }

    public static function validate($request)
    {
        return Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'group_id' => 'required',
            ]
        );
    }
}
