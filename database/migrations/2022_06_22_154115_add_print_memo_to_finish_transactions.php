<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintMemoToFinishTransactions extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('finish_transactions', function (Blueprint $table) {
      //
      $table->boolean('is_print_memo')->nullable()->default(false);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('finish_transactions', function (Blueprint $table) {
      //
      $table->dropColumn('is_print_memo');
    });
  }
}
