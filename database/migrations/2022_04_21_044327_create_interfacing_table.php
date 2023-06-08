<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterfacingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interfacings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('test_id')->unsigned();
            $table->bigInteger('analyzer_id')->unsigned();
            $table->string('code');
            $table->timestamps();
        });

        Schema::table('interfacings', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('analyzer_id')->references('id')->on('analyzers')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interfacings');
    }
}
