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
        Schema::create('fuel_stations', function (Blueprint $table) {
          $table->id();
          $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
          $table->string('name_owner');
          $table->string('name_driver');
          $table->string('name_distributor');
          $table->dateTime('filing_datetime');
          $table->decimal('liter', 20,2);
          $table->decimal('amount', 20,2);
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
        Schema::dropIfExists('fuel_stations');
    }
};
