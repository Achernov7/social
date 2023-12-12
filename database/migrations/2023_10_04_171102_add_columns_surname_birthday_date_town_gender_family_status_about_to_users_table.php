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
            $table->string('surname')->nullable();
            $table->date('birthdayDate')->nullable();
            $table->string('town')->nullable();
            $table->string('gender')->nullable();
            $table->string('familyStatus')->nullable();
            $table->string('about')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->dropColumn('birthdayDate');
            $table->dropColumn('town');
            $table->dropColumn('gender');
            $table->dropColumn('familyStatus');
            $table->dropColumn('about');
        });
    }
};
