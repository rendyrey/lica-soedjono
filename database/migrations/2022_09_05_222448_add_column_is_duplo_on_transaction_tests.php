<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIsDuploOnTransactionTests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('transaction_tests', function (Blueprint $table) {
          $table->boolean('mark_duplo')->nullable()->default(false)->after('analyzer_id');
        });
        Schema::table('finish_transaction_tests', function (Blueprint $table) {
          $table->boolean('mark_duplo')->nullable()->default(false)->after('analyzer_id');
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
