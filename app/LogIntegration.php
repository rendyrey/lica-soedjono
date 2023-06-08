<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LogIntegration extends Model
{
    protected $fillable = [
        'no_order',
        'return_result',
        'timestamp',
        'status',
        'notes',
    ];
}
