<?php

namespace Tests\Feature;

use App\Http\Controllers\AttendanceController;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Tests\TestCase;

class AttendanceViewAttendanceTest extends TestCase
{
    public function test_view_attendance_includes_check_in_and_check_out_data(): void
    {
        $company = Company::factory()->create();
        app()->instance('current_company', $company);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'role' => 'user',
        ]);

        Attendance::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'date' => '2026-07-18',
            'check_in' => '09:00:00',
            'check_out' => '18:30:00',
            'status' => 'present',
        ]);

        $view = (new AttendanceController())->viewAttendance(new Request([
            'year' => 2026,
            'month' => 7,
        ]));

        $data = $view->getData();

        $this->assertArrayHasKey($user->id, $data['attendances']);
        $this->assertSame('09:00', $data['attendances'][$user->id][0]['check_in']);
        $this->assertSame('18:30', $data['attendances'][$user->id][0]['check_out']);
    }
}
