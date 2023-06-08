<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrinterSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('printer_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('printer_client_target')->nullable();
            $table->string('printer_client_name')->nullable();
            $table->boolean('is_active')->nullable()->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('printer_setting');
    }
}
