<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsIgdOnTableTransactionsAndFinishTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    //
    Schema::table('transactions', function (Blueprint $table) {
      $table->boolean('is_igd')->nullable()->default(false)->after('status');
    });
    Schema::table('finish_transactions', function (Blueprint $table) {
      $table->boolean('is_igd')->nullable()->default(false)->after('status');
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
