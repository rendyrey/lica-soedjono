<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoIncrementOnActivityLogs2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('activity_logs', function (Blueprint $table) {   
            $table->dropPrimary();
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->primary('id');
        });
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->bigInteger('id', true)->change();
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
