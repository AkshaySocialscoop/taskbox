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
        Schema::table('attendances', function (Blueprint $table) {
             $table->foreignId('shift_id')->nullable()->constrained()->nullOnDelete();
        $table->decimal('working_hours', 5, 2)->default(0);
        $table->decimal('overtime_hours', 5, 2)->default(0);
        $table->enum('status', ['present','absent','late','half_day','paid_leave','week_off'])->default('present');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
           $table->dropColumn(['shift_id','working_hours','overtime_hours','status']);
        });
    }
};
