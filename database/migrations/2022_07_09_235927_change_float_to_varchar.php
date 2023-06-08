<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFloatToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::table('ranges', function (Blueprint $table) {
        $table->string('min_crit_male')->change();
        $table->string('max_crit_male')->change();
        $table->string('min_crit_female')->change();
        $table->string('max_crit_female')->change();
        $table->string('min_male_ref')->change();
        $table->string('max_male_ref')->change();
        $table->string('min_female_ref')->change();
        $table->string('max_female_ref')->change();
      });

    Schema::table('transaction_tests', function (Blueprint $table) {
      $table->string('result_number')->change();
    });
    Schema::table('finish_transaction_tests', function (Blueprint $table) {
      $table->string('result_number')->change();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
