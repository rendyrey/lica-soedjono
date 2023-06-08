<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Transaction extends Model
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
        'is_igd',
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

    public static function getTransactionData($transactionId)
    {

        //get all transaction data with master data
        $transactions = DB::table('transactions')
            ->select(
                'transactions.*',
                'patients.name as patient_name',
                'patients.medrec as patient_medrec',
                'patients.address as patient_address',
                'patients.gender as patient_gender',
                'patients.birthdate as patient_birthdate',
                'patients.email as patient_email',
                'patients.phone as patient_phone',
                'rooms.room as room_name',
                'doctors.name as doctor_name',
                'insurances.name as insurance_name',
                'analyzers.name as analyzer_name',
                'verificator.name as verficator_name',
                'validator.name as validator_name',
                'checker.name as checker_name',
            )
            ->leftJoin('patients', 'transactions.patient_id', '=', 'patients.id')
            ->leftJoin('rooms', 'transactions.room_id', '=', 'rooms.id')
            ->leftJoin('doctors', 'transactions.doctor_id', '=', 'doctors.id')
            ->leftJoin('insurances', 'transactions.insurance_id', '=', 'insurances.id')
            ->leftJoin('analyzers', 'transactions.analyzer_id', '=', 'analyzers.id')
            ->leftJoin('users as validator', 'transactions.validator_id', '=', 'validator.id')
            ->leftJoin('users as verificator', 'transactions.verficator_id', '=', 'verificator.id')
            ->leftJoin('users as checker', 'transactions.checkin_by', '=', 'checker.id')
            ->where('transactions.id', $transactionId)
            ->first();

        return $transactions;
    }

    // public function transaction_tests()
    // {
    //     // return 
    //     return $this->hasMany('App\TransactionTest', 'transaction_id', 'id');
    // }
}
