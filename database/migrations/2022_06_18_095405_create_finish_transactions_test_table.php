<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFinishTransactionsTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('finish_transaction_tests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('finish_transaction_id')->unsigned();
            $table->bigInteger('transaction_id')->unsigned();
            $table->bigInteger('test_id')->unsigned()->nullable();
            $table->string('test_name')->nullable();
            $table->bigInteger('package_id')->unsigned()->nullable();
            $table->string('package_name')->nullable();
            $table->bigInteger('price_id')->unsigned()->nullable();
            $table->bigInteger('group_id')->unsigned()->nullable();
            $table->string('group_name')->nullable();
            $table->bigInteger('analyzer_id')->unsigned()->nullable();
            $table->string('analyzer_name')->nullable();
            $table->enum('type', ['single','package']);
            $table->decimal('result_number', 13, 2)->nullable();
            $table->integer('result_label')->nullable();
            $table->text('result_text')->nullable();
            $table->boolean('draw')->nullable()->default(false);
            $table->text('undraw_memo')->nullable();
            $table->boolean('result_status')->nullable()->default(false);
            $table->datetime('draw_time')->nullable();
            $table->datetime('input_time')->nullable();
            $table->boolean('verify')->nullable()->default(false);
            $table->boolean('validate')->nullable()->default(false);
            $table->boolean('report_status')->nullable()->default(false);
            $table->string('report_by')->nullable();
            $table->string('report_to')->nullable();
            $table->text('memo_test')->nullable();
            $table->bigInteger('verify_by')->unsigned()->nullable();
            $table->string('verify_by_name')->nullable();
            $table->bigInteger('validate_by')->unsigned()->nullable();
            $table->string('validate_by_name')->nullable();
            $table->datetime('verify_time')->nullable();
            $table->datetime('validate_time')->nullable();
            $table->boolean('is_print')->nullable()->default(false);
            $table->text('global_result')->nullable();


            $table->timestamps();
        });

        Schema::table('finish_transaction_tests', function(Blueprint $table) {
            $table->foreign('finish_transaction_id')->references('id')->on('finish_transactions')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('price_id')->references('id')->on('prices')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('analyzer_id')->references('id')->on('analyzers')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('verify_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreign('validate_by')->references('id')->on('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('finish_transactions_test');
    }
}
