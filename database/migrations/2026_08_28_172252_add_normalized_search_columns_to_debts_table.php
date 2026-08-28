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
            $table->string('fullname_normalized', 255)->nullable()->after('fullname');
            $table->string('phone_normalized', 255)->nullable()->after('phone');
            $table->index('fullname_normalized', 'debts_fullname_normalized_index');
            $table->index('phone_normalized', 'debts_phone_normalized_index');
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
            $table->dropIndex('debts_fullname_normalized_index');
            $table->dropIndex('debts_phone_normalized_index');
            $table->dropColumn(['fullname_normalized', 'phone_normalized']);
        });
    }
};
