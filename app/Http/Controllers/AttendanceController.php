<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest()->paginate(20);

        $users = User::whereIn('role', ['user', 'admin'])->get();

        return view(
            'super-admin.employee-management.attendance.index',
            compact('attendances', 'users')
        );
    }

    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $oldData = $attendance->toArray();

        $attendance->update($request->all());

        audit_log(
            'Attendance',
            'Update',
            'Attendance Updated',
            $attendance->id,
            null,
            "Attendance updated for {$attendance->user->name} on {$attendance->date}.",
            json_encode($oldData),
            json_encode($attendance->fresh()->toArray())
        );

        return back()->with('success', 'Updated');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date|before_or_equal:today',
            'check_in' => 'nullable|date_format:H:i:s',
            'check_out' => 'nullable|date_format:H:i:s',
            'status' => 'required|in:present,absent,late,half_day,paid_leave,week_off',
        ]);

        $data['company_id'] = auth()->user()->company_id;

        $attendance = Attendance::create($data);

        audit_log(
            'Attendance',
            'Create',
            'Attendance Created',
            $attendance->id,
            null,
            "Attendance created for {$attendance->user->name} on {$attendance->date}.",
            null,
            json_encode($attendance->toArray())
        );

        return back()->with('success', 'Attendance created successfully');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        $oldData = $attendance->toArray();

        audit_log(
            'Attendance',
            'Delete',
            'Attendance Deleted',
            $attendance->id,
            null,
            "Attendance deleted for {$attendance->user->name} on {$attendance->date}.",
            json_encode($oldData),
            null
        );

        $attendance->delete();

        return back()->with('success', 'Deleted');
    }

    public function checkIn(Request $request)
    {
        try {

            $user = auth()->user();

            $status = 'present';

            $shift = Shift::find($user->shift_id);
            if ($shift) {

                // current datetime
                $now = \Carbon\Carbon::now();

                // shift start today
                $shiftStart = \Carbon\Carbon::today()
                    ->setTimeFromTimeString($shift->start_time);

                // late minutes
                $lateMinutes = $shiftStart->diffInMinutes($now);

                // ONLY if current time is after shift start
                if ($now->timestamp > $shiftStart->timestamp) {


                    // 4 hour late
                    if ($lateMinutes >= 240) {

                        $status = 'half_day';
                    }

                    // 1 hour late
                    else if ($lateMinutes >= 60) {

                        $status = 'late';
                    }
                }
            }

            $attendance = Attendance::create([
                'user_id'  => $user->id,
                'shift_id' => $user->shift_id,
                'date'     => now()->toDateString(),
                'check_in' => now(),
                'lat'      => $request->lat,
                'lng'      => $request->lng,
                'status'   => $status,
            ]);

            return response()->json([
                'success' => true,
                'status'  => $status,
                'late_minutes' => $lateMinutes ?? 0
            ]);
        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
                'line'    => $e->getLine()
            ]);
        }
    }
    public function checkOut(Request $request)
    {
        try {
            $attendance = Attendance::where('user_id', auth()->id())
                ->where('date', today())
                ->whereNull('check_out')
                ->first();

            if (!$attendance) {
                return response()->json([
                    'error' => 'No active check-in found'
                ], 400);
            }

            $attendance->update([
                'check_out' => now(),
                'lat' => $request->lat,
                'lng' => $request->lng,
            ]);
            // 🔍 DEBUG
            \Log::info('Attendance Found:', ['id' => $attendance->id]);
            \Log::info('Today:', ['today' => now()->toDateString()]);
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function viewAttendance(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;

        $employees = User::whereIn('role', ['user', 'admin'])->get();

        // Filter by selected month/year
        $attendancesRaw = Attendance::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        // Convert to JS-friendly format
        $attendances = [];

        foreach ($attendancesRaw as $att) {
            $attendances[$att->user_id][] = [
                'formatted_date' => \Carbon\Carbon::parse($att->date)->format('Y-m-d'),
                'status' => $att->status,
                'check_in' => $att->check_in ? \Carbon\Carbon::parse($att->check_in)->format('h:i A') : null,
                'check_out' => $att->check_out ? \Carbon\Carbon::parse($att->check_out)->format('h:i A') : null,
                'shift_id' => $att->shift_id,
                'working_hours' => $att->working_hours,
                'overtime_hours' => $att->overtime_hours,
            ];
        }

        return view(
            'super-admin.employee-management.view-attendance.index',
            compact('employees', 'attendances', 'year', 'month')
        );
    }
}
