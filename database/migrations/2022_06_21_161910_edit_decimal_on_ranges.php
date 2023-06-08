<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditDecimalOnRanges extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    //
    Schema::table('ranges', function (Blueprint $table) {
      $table->decimal('min_crit_male', 13, 5)->change();
      $table->decimal('max_crit_male', 13, 5)->change();
      $table->decimal('min_crit_female', 13, 5)->change();
      $table->decimal('max_crit_female', 13, 5)->change();
      $table->decimal('min_male_ref', 13, 5)->change();
      $table->decimal('max_male_ref', 13, 5)->change();
      $table->decimal('min_female_ref', 13, 5)->change();
      $table->decimal('max_female_ref', 13, 5)->change();
    });

    Schema::table('transaction_tests', function (Blueprint $table) {
      $table->decimal('result_number', 13, 5)->change();
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->decimal('result_number', 13, 5)->change();
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
