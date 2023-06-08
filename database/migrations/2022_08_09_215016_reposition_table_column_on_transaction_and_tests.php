<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RepositionTableColumnOnTransactionAndTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('transaction_tests', function (Blueprint $table) {
      $table->renameColumn('report_at', 'report_time');
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->renameColumn('report_at', 'report_time');
    });

    DB::statement("ALTER TABLE transaction_tests MODIFY COLUMN report_time DATETIME AFTER validate_time");
    DB::statement("ALTER TABLE finish_transaction_tests MODIFY COLUMN report_time DATETIME AFTER validate_time");
    DB::statement("ALTER TABLE transactions MODIFY COLUMN created_time DATETIME AFTER report_status");
    DB::statement("ALTER TABLE finish_transactions MODIFY COLUMN created_time DATETIME AFTER report_status");
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
