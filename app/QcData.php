<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QcData extends Model
{
    public $table = "qc_datas";

    protected $fillable = [
        'id',
        'qc_id',
        'data',
        'position',
        'qc',
        'atlm',
        'recommendation',
        'created_at',
        'updated_at',
    ];
}
