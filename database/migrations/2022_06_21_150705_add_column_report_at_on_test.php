<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnReportAtOnTest extends Migration
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
      $table->datetime('report_at')->nullable();
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->datetime('report_at')->nullable();
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
    Schema::table('transaction_tests', function (Blueprint $table) {
      $table->dropColumn('report_at');
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->dropColumn('report_at');
    });
  }
}
