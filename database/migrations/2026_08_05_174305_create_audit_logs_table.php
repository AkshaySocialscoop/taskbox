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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('affected_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('module', 100);
            $table->string('action', 50);
            $table->string('event', 100)->nullable();

            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('field_name', 100)->nullable();

            $table->longText('description')->nullable();
            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();

            $table->string('url', 500)->nullable();
            $table->string('method', 10)->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index('company_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
