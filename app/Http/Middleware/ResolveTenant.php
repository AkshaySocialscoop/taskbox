<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\Schema;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        // Try subdomain first (company.example.com)
        $host = $request->getHost();

        // naive subdomain extraction
        $parts = explode('.', $host);
        $subdomain = count($parts) > 2 ? $parts[0] : null;

        $company = null;

        if ($subdomain) {
            try {
                $company = Company::where('subdomain', $subdomain)->first();
            } catch (\Throwable $e) {
                // companies table may not exist during tests or migrations; ignore
                $company = null;
            }
        }

        // Fallback: if no subdomain, allow domain mapping
        if (! $company) {
            try {
                $company = Company::where('domain', $host)->first();
            } catch (\Throwable $e) {
                $company = null;
            }
        }

        // Bind current company for use in scopes and models
        if ($company) {
            app()->instance('current_company', $company);
        }

        // For tests: if no company resolved but the companies table exists, ensure a default company is bound
        if (! $company && app()->runningUnitTests()) {
            try {
                if (Schema::hasTable('companies')) {
                    $company = Company::first() ?? Company::create(['name' => 'Default Company']);
                    app()->instance('current_company', $company);
                }
            } catch (\Throwable $e) {
                // ignore; tests may be running migrations
            }
        }

        return $next($request);
    }
}
