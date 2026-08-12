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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->text('description')->nullable();

            $table->unsignedBigInteger('assigned_to'); // employee
            $table->unsignedBigInteger('created_by');  // admin

            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', [ 'Not_Started', 'Pending', 'In_Progress', 'Completed'  ])->default('Not_Started');


            $table->date('due_date')->nullable();
            $table->string('attachment')->nullable();

            $table->timestamps();

            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
