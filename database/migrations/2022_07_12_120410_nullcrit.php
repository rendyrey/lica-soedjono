<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Nullcrit extends Migration
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
            $table->string('min_crit_male')->nullable()->change();
            $table->string('min_crit_female')->nullable()->change();
            $table->string('max_crit_male')->nullable()->change();
            $table->string('max_crit_female')->nullable()->change();

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
