<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function () {
    AuditLog::where('created_at', '<', now()->subDays(30))->delete();
})->daily();
 
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

 