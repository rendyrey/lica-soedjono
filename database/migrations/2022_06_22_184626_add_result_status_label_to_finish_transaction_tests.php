<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResultStatusLabelToFinishTransactionTests extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->string('result_status_label')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->dropColumn('result_status_label');
    });
  }
}
