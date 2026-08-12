<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users',
            'departments',
            'roles',
            'shifts',
            'attendances',
            'leave_requests',
            'tasks',
            'notes',
            'projects',
            'calendar_events',
            'media',
            'social_accounts',
            'scheduled_posts',
            'messages',
            'notifications',
            'user_infos',
        ];

        foreach ($tables as $tableName) {

            if (!Schema::hasTable($tableName)) {
                continue;
            }

            if (!Schema::hasColumn($tableName, 'company_id')) {
                continue;
            }

            // First remove the foreign key
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['company_id']);
            });

            // Then make company_id optional
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')
                    ->nullable()
                    ->change();
            });
        }
    }

    public function down(): void
    {
        // No foreign key is restored.
        // company_id remains optional.
    }
};