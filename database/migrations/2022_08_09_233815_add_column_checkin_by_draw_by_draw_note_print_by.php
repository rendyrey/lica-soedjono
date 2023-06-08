<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCheckinByDrawByDrawNotePrintBy extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    //
    Schema::table('log_print', function (Blueprint $table) {
      $table->integer('print_by')->nullable()->after('id');
    });
    Schema::table('transactions', function (Blueprint $table) {
      $table->bigInteger('checkin_by')->nullable()->after('get_status');
    });
    Schema::table('finish_transactions', function (Blueprint $table) {
      $table->bigInteger('checkin_by')->nullable()->after('get_status');
      $table->string('checkin_by_name')->nullable()->before('checkin_time');
    });
    Schema::table('transaction_tests', function (Blueprint $table) {
      $table->bigInteger('draw_by')->nullable()->before('draw_time');
      $table->text('draw_memo')->nullable()->after('draw');
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->bigInteger('draw_by')->nullable()->before('draw_time');
      $table->string('draw_by_name')->nullable()->before('draw_time');
      $table->text('draw_memo')->nullable()->after('draw');
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
