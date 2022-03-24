<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('google2fa_enable')->default(false)->after('remember_token');
            $table->text('google2fa_secret')->nullable()->after('remember_token');
            $table->text('google2fa_recovery_codes')->nullable()->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('google2fa_enable');
            $table->dropColumn('google2fa_secret');
            $table->dropColumn('google2fa_recovery_codes');
        });
    }
};
