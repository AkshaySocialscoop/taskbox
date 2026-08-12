<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_accounts', function (Blueprint $table) {

            $table->id();

            // TaskBox user
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Google account information
            $table->string('google_user_id');
            $table->string('google_email');

            // OAuth tokens
            $table->text('access_token');
            $table->text('refresh_token')->nullable();

            // Token expiry
            $table->timestamp('token_expires_at')->nullable();

            // OAuth scopes granted by Google
            $table->text('scopes')->nullable();

            $table->timestamps();

            // One Google account should belong to one TaskBox user
            $table->unique('user_id');

            // Helpful for Google account lookup
            $table->index('google_user_id');
            $table->index('google_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_accounts');
    }
};