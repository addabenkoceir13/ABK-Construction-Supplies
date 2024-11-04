<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('tractor_driver_id')->after('user_id');
            $table->foreign('tractor_driver_id')->references('id')->on('tractor_drivers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debts', function (Blueprint $table) {
          $table->dropForeign(['tractor_driver_id']);
          $table->dropColumn('tractor_driver_id');
        });
    }
};
