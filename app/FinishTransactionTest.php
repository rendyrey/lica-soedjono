<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinishTransactionTest extends Model
{
    protected $with = ['test','finish_transaction'];
    protected $fillable = [
        'draw',
        'draw_time',
        'transaction_id',
        'analyzer_id',
        'test_id',
        'package_id',
        'price_id',
        'group_id',
        'type'
    ];

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }

    public function finish_transaction()
    {
        return $this->belongsTo('App\FinishTransaction', 'transaction_id', 'id');
    }
}
