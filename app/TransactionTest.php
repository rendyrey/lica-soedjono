<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class TransactionTest extends Model
{
    protected $with = ['test','transaction','package'];
    protected $fillable = [
        'draw',
        'draw_time',
        'input_time',
        'transaction_id',
        'analyzer_id',
        'test_id',
        'package_id',
        'price_id',
        'group_id',
        'result_label',
        'type'
    ];

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }
    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id', 'id');
    }

    public static function getTransactionTestData($where){

        //get al transaction test data with master data
        $transactionTests =  DB::table('transaction_tests')
                    ->select(
                        'transaction_tests.*',
                        'tests.name as test_name',
                        'packages.name as package_name',
                        'groups.name as group_name',
                        'analyzers.name as analyzer_name',
                        'tests.specimen_id as specimen_id',
                        'specimens.name as specimen_name',
                        'verificator.name as verify_by_name',
                        'validator.name as validate_by_name',
                        'drawer.name as draw_by_name',
                        'tests.sub_group','tests.unit','tests.volume','tests.sequence','tests.initial','tests.general_code','tests.normal_notes',
                        'results.result as result_final',
                    )
                    ->leftJoin('tests', 'transaction_tests.test_id', '=', 'tests.id')
                    ->leftJoin('packages', 'transaction_tests.package_id', '=', 'packages.id')
                    ->leftJoin('groups', 'transaction_tests.group_id', '=', 'groups.id')
                    ->leftJoin('analyzers', 'transaction_tests.analyzer_id', '=', 'analyzers.id')
                    ->leftJoin('specimens', 'tests.specimen_id', '=', 'specimens.id')
                    ->leftJoin('results', 'results.id', '=', 'transaction_tests.result_label')
                    ->leftJoin('users as validator', 'transaction_tests.validate_by', '=', 'validator.id')
                    ->leftJoin('users as verificator', 'transaction_tests.verify_by', '=', 'verificator.id')
                    ->leftJoin('users as drawer', 'transaction_tests.draw_by', '=', 'drawer.id')
                    ->where('transaction_tests.transaction_id', $where['transaction_id'])
                    ->get(); 
        return $transactionTests;
    }
}
