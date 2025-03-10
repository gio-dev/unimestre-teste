<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('document', 50)->unique();
            $table->string('login')->unique();
            $table->dateTime('birth')->nullable();
            $table->string('gender', 1)->default('P');
            $table->string('marital_status', 20)->nullable();
            $table->string('education_level', 50)->nullable();
            $table->double('salary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('document');
            $table->dropColumn('birth');
            $table->dropColumn('gender');
            $table->dropColumn('marital_status');
            $table->dropColumn('education_level');
            $table->dropColumn('salary');
        });
    }
};
