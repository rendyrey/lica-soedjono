<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreDetailToFinishTransactionTests extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->string('sub_group')->nullable();
      $table->string('initial')->nullable();
      $table->string('unit')->nullable();
      $table->string('volume')->nullable();
      $table->string('normal_notes')->nullable();
      $table->string('general_code')->nullable();
      $table->integer('sequence')->nullable();
      $table->string('normal_value')->nullable();
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
      $table->dropColumn('sub_group');
      $table->dropColumn('initial');
      $table->dropColumn('unit');
      $table->dropColumn('volume');
      $table->dropColumn('normal_notes');
      $table->dropColumn('general_code');
      $table->dropColumn('sequence');
      $table->dropColumn('normal_value');
    });
  }
}
