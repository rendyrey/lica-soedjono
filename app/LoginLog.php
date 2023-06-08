<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoginLog extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'username',
        'last_login',
        'ip_address',
        'browser'
    ];
}
