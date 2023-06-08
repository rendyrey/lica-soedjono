<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDefaultOnMaster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('analyzers', function (Blueprint $table) {
          $table->boolean('is_default')->default(false)->after('group_id');
        });
        Schema::table('results', function (Blueprint $table) {
          $table->boolean('is_default')->default(false)->after('test_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('analyzers', function (Blueprint $table) {
        $table->dropColumn('is_default');
      });
      Schema::table('results', function (Blueprint $table) {
        $table->dropColumn('is_default');
      });
    }
}
