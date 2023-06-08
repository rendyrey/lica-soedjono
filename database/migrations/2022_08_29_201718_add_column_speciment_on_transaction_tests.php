<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSpecimentOnTransactionTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    //
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->string('specimen_name')->nullable()->after('analyzer_name');
      $table->bigInteger('specimen_id')->nullable()->after('analyzer_name');
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
