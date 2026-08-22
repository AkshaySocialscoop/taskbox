<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Google OAuth columns (in logical order)
            $table->string('google_id')->nullable()->unique()->after('email');
            $table->string('google_name')->nullable()->after('google_id');
            $table->string('google_email')->nullable()->after('google_name');
            $table->string('google_avatar')->nullable()->after('google_email');
            $table->text('google_token')->nullable()->after('google_avatar');
            $table->text('google_refresh_token')->nullable()->after('google_token');
            $table->timestamp('google_token_expires_at')->nullable()->after('google_refresh_token'); 
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_id',
                'google_name',
                'google_email',
                'google_avatar',
                'google_token',
                'google_refresh_token',
                'google_token_expires_at'
            ]);
        });
    }
};