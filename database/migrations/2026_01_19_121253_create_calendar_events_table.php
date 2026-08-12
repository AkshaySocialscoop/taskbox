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
        Schema::create('calendar_events', function (Blueprint $table) {
           $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('brand_name')->nullable();
            $table->date('posting_date')->nullable();
            $table->string('post_type')->nullable();
            $table->string('concept')->nullable();

            $table->string('content')->nullable(); // Zoom, Meet, etc
            $table->string('reference')->nullable(); // Zoom, Meet, etc
            $table->text('comment')->nullable();

            $table->enum('status', ['pending', 'completed'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
