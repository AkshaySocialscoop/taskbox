<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'departments', 'roles', 'shifts', 'attendances', 'leave_requests',
            'tasks', 'notes', 'projects', 'calendar_events', 'media',
            'social_accounts', 'scheduled_posts', 'messages', 'notifications', 'user_infos'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    if (! Schema::hasColumn($table, 'company_id')) {
                        $t->foreignId('company_id')->nullable()->after('id')->index();
                        $t->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
                    }
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'departments', 'roles', 'shifts', 'attendances', 'leave_requests',
            'tasks', 'notes', 'projects', 'calendar_events', 'media',
            'social_accounts', 'scheduled_posts', 'messages', 'notifications', 'user_infos'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) {
                    $t->dropForeign(['company_id']);
                    $t->dropColumn('company_id');
                });
            }
        }
    }
};
