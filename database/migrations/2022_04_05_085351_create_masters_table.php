<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create patients table
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('medrec');
            $table->string('name');
            $table->enum('gender', ['M', 'F']);
            $table->date('birthdate');
            $table->text('address')->nullable();
            $table->string('phone', 17)->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // create rooms table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room');
            $table->string('room_code');
            $table->string('class');
            $table->boolean('auto_checkin')->default(false);
            $table->boolean('auto_draw')->default(false);
            $table->enum('type', ['rawat_inap', 'rawat_jalan', 'igd', 'rujukan']);
            $table->string('referral_address')->nullable();
            $table->string('referral_no_phone')->nullable();
            $table->string('referral_email')->nullable();
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        // create doctors table
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        // create insurance table
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('discount');
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        // create groups table
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('wating_time')->nullable();
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        // create analyzers table
        Schema::create('analyzers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('group_id')->unsigned();
            $table->string('name');
            $table->timestamps();
        });

        // create foreign key
        Schema::table('analyzers', function (Blueprint $table) {
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('specimens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color');
            $table->string('code');
            $table->timestamps();
        });

        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('specimen_id')->unsigned();
            $table->bigInteger('group_id')->unsigned();
            $table->string('name');
            $table->string('initial');
            $table->string('unit');
            $table->decimal('volume', 12, 2);
            $table->enum('range_type', ['number','label','description','free_formatted_text']);
            $table->integer('sequence');
            $table->string('sub_group');
            $table->longText('normal_notes')->nullable();
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('specimen_id')->references('id')->on('specimens')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        Schema::create('package_tests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('test_id')->unsigned();
            $table->bigInteger('package_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('package_tests', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('package_id')->unsigned()->nullable();
            $table->bigInteger('test_id')->unsigned()->nullable();
            $table->string('type'); // TODO: INI APA?
            $table->integer('price');
            $table->string('class');
            $table->timestamps();
        });

        Schema::table('prices', function (Blueprint $table) {
            $table->foreign('package_id')->references('id')->on('packages');
            $table->foreign('test_id')->references('id')->on('tests');
        });

        Schema::create('general_code_tests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('test_id')->unsigned();
            $table->string('general_code')->nullable();
            $table->timestamps();
        });

        Schema::table('general_code_tests', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('ranges', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('test_id')->unsigned();
            $table->integer('min_age');
            $table->integer('max_age');
            $table->decimal('min_crit_male', 13, 2);
            $table->decimal('max_crit_male', 13, 2);
            $table->decimal('min_crit_female', 13, 2);
            $table->decimal('max_crit_female', 13, 2);
            $table->decimal('min_male_ref', 13, 2);
            $table->decimal('max_male_ref', 13, 2);
            $table->decimal('min_female_ref', 13, 2);
            $table->decimal('max_female_ref', 13, 2);
            $table->text('normal_male')->nullable();
            $table->text('normal_female')->nullable();
            $table->timestamps();
        });

        Schema::table('ranges', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade')->onUpdate('cascade');
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('test_id')->unsigned();
            $table->string('result');
            $table->string('status');
            $table->timestamps();
        });

        Schema::table('results', function (Blueprint $table) {
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
        Schema::dropIfExists('ranges');
        Schema::dropIfExists('general_code_tests');
        Schema::dropIfExists('prices');
        Schema::dropIfExists('package_tests');
        Schema::dropIfExists('packages');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('doctors');
        Schema::dropIfExists('insurances');
        Schema::dropIfExists('analyzers');
        Schema::dropIfExists('tests');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('specimens');
    }
}
