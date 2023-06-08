<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PrinterSetting extends Model
{
    protected $fillable = [
        'name',
        'width',
        'height',
        'printer_client_target',
        'printer_client_name',
        'is_active',
    ];
}
