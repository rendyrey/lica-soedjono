<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinishTransaction extends Model
{
    protected $with = ['patient', 'room', 'insurance', 'doctor'];
    protected $fillable = [
        'patient_id',
        'room_id',
        'doctor_id',
        'insurance_id',
        'analyzer_id',
        'type',
        'no_lab',
        'note',
        'created_time',
        'cito',
        'transaction_id_label',
        'checkin_time',
        'status',
        'completed'
    ];

    public function patient()
    {
        return $this->belongsTo('App\Patient', 'patient_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

    public function insurance()
    {
        return $this->belongsTo('App\Insurance', 'insurance_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor', 'doctor_id', 'id');
    }

    // public function transaction_tests()
    // {
    //     // return 
    //     return $this->hasMany('App\TransactionTest', 'transaction_id', 'id');
    // }
}
