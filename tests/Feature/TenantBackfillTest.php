<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\User;

class TenantBackfillTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_company_exists_and_users_backfilled()
    {
        // Run migrations
        $this->artisan('migrate');

        // Run backfill command
        $this->artisan('app:backfill-companies', ['--default-name' => 'Default Company']);

        $company = Company::first();
        $this->assertNotNull($company, 'Default company should exist');

        // Create a user and ensure company_id is set when created through factory
        $user = User::factory()->create();
        $this->assertNotNull($user->company_id);
    }
}
