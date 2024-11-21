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
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tractor_driver_id')->constrained('tractor_drivers')->cascadeOnDelete();
            $table->string('fullname');
            $table->string('phone');
            $table->date('date_debut_debt');
            $table->decimal('total_debt_amount', 20, 2)->nullable();
            $table->decimal('rest_debt_amount', 20, 2)->nullable();
            $table->date('date_end_debt')->nullable();
            $table->enum('status',['paid', 'unpaid'])->default('unpaid');
            $table->longText('note')->nullable();
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
        Schema::dropIfExists('debts');
    }
};
