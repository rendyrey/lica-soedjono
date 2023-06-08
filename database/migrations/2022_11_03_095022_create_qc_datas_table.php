<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQcDatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_datas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('qc_id')->unsigned()->nullable();
            $table->string('data')->nullable();
            $table->string('position')->nullable();
            $table->integer('qc')->nullable();
            $table->string('atlm')->nullable();
            $table->string('recommendation')->nullable();
            $table->timestamps();
        });

        Schema::table('qc_datas', function (Blueprint $table) {
            $table->foreign('qc_id')->references('id')->on('qcs')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qc_datas');
    }
}
