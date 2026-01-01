<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->date('date_of_birth')->nullable()->after('username');
            $table->string('profile_picture')->nullable()->after('date_of_birth');
            $table->text('about_me')->nullable()->after('profile_picture');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'date_of_birth', 'profile_picture', 'about_me']);
        });
    }
};
