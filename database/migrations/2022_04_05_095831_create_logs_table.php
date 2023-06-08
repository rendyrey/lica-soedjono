<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create activity logs table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('action');
            $table->longText('description')->nullable();
            $table->timestamps();
        });

        // create login logs table
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('username');
            $table->dateTime('last_login');
            $table->string('ip_address');
            $table->string('browser');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('login_logs');
    }
}
