<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Room extends Model
{
    // rawat inap, rawat jalan, IGD
    const TYPE = [
        'rawat_inap' => 'Rawat Inap',
        'rawat_jalan' => 'Rawat Jalan',
        'igd' => 'IGD',
        'rujukan' => 'Rujukan'
    ];

    protected $fillable = [
        'room',
        'room_code',
        'class',
        'auto_checkin',
        'auto_draw',
        'auto_undraw',
        'auto_nolab',
        'type',
        'referral_address',
        'referral_no_phone',
        'referral_email',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'room' => 'required',
            'room_code' => 'required',
            'class' => 'required',
            'type' => 'required',
            'general_code'
        ]);
        
    }

}
