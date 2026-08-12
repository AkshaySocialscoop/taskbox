<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Company;

class BackfillCompanies extends Command
{
    protected $signature = 'app:backfill-companies {--default-name=Default Company}';

    protected $description = 'Create default company and backfill company_id on existing tenant tables';

    public function handle()
    {
        DB::beginTransaction();
        try {
            $name = $this->option('default-name');
            $company = Company::firstOrCreate(['name' => $name]);

            $this->info('Using company id: ' . $company->id);

            $tables = [
                'users','departments','roles','shifts','attendances','leave_requests',
                'tasks','notes','projects','calendar_events','media',
                'social_accounts','scheduled_posts','messages','notifications','user_infos'
            ];

            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table) && DB::getSchemaBuilder()->hasColumn($table, 'company_id')) {
                    $updated = DB::table($table)->whereNull('company_id')->update(['company_id' => $company->id]);
                    $this->info("Updated {$updated} rows in {$table}");
                }
            }

            DB::commit();
            $this->info('Backfill completed successfully');
            return 0;

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Backfill failed: ' . $e->getMessage());
            return 1;
        }
    }
}
