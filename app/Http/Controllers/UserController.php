<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\CalendarEvent;
use App\Models\Attendance;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        // 🔍 Attendance Check
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        // ✅ Show Check-In Modal
        $showCheckInModal = !$attendance;

        // ✅ Show Workflow Modal (only after check-in, once per day)
        $sessionKey = 'workflow_modal_shown_' . $today;

        $showWorkflowModal = $attendance && $attendance->check_in && !session()->has($sessionKey);

        if ($showWorkflowModal) {
            session([$sessionKey => true]);
        }

        // 📊 Other Data
        $tasks = Task::where('assigned_to', Auth::id())
            ->latest()
            ->get();

        $projects = Project::latest()->get();

        $calendarEvents = CalendarEvent::all();

        $meetings = $calendarEvents
            ->groupBy('posting_date')
            ->map(function ($items) {
                return $items->map(function ($event) {
                    return [
                        'title'    => $event->brand_name,
                        'postType' => $event->post_type,
                        'status'   => ucfirst($event->status),
                        'concept'  => $event->concept,
                        'content'  => $event->content,
                        'reference'=> $event->reference,
                        'comment'  => $event->comment,
                        'id'       => $event->id,
                    ];
                });
            });

        $todayTasks = Task::where('assigned_to', $user->id)
            ->whereDate('due_date', '>=', Carbon::today())
            ->get();

        $todayPosts = CalendarEvent::where('user_id', $user->id)
            ->whereDate('created_at', '>=', Carbon::today())
            ->get();

        $countTotal = Task::where('assigned_to', $user->id)->count();
        $countCompleted = Task::where('assigned_to', $user->id)
            ->where('status', 'Completed')
            ->count();
        $countInProgress = Task::where('assigned_to', $user->id)
            ->where('status', 'In_Progress')
            ->count();
        $countOverdue = Task::where('assigned_to', $user->id)
            ->whereDate('due_date', '<', Carbon::today())
            ->where('status', '!=', 'Completed')
            ->count();

        return view('user.dashboard', compact(
            'tasks',
            'projects',
            'meetings',
            'todayTasks',
            'todayPosts',
            'showCheckInModal',
            'showWorkflowModal',
            'countTotal',
            'countCompleted',
            'countInProgress',
            'countOverdue'
        ));
    }

     
    public function taskCounts()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        return response()->json([
            'total' => Task::where('assigned_to', $userId)->count(),

            'completed' => Task::where('assigned_to', $userId)
                ->where('status', 'Completed')->count(),

            'in_progress' => Task::where('assigned_to', $userId)
                ->where('status', 'In_Progress')->count(),

            'pending' => Task::where('assigned_to', $userId)
                ->where('status', 'Pending')->count(),

            'overdue' => Task::where('assigned_to', $userId)
                ->whereDate('due_date', '<', $today)
                ->where('status', '!=', 'Completed')
                ->count(),
        ]);
    }

    public function calendarEventsStore(Request $request)
    {
        $request->validate([
            'brand_name' => 'required|string|max:255',
            'posting_date' => 'required|date',
            'post_type' => 'required|string|max:255',
            'concept' => 'required|string|max:255',
            'content' => 'nullable|string|max:255',
            'reference' => 'nullable|url|max:255',
            'comment' => 'nullable|string',
            'status' => 'nullable|in:pending,completed',
        ]); 


        CalendarEvent::create([
            'user_id'      => Auth::id(),
            'brand_name'   => $request->brand_name,
            'posting_date' => $request->posting_date,
            'post_type'    => $request->post_type,
            'concept'      => $request->concept,
            'content'      => $request->content,
            'reference'    => $request->reference,
            'comment'      => $request->comment,
            'status'       => 'pending',
        ]);


        return redirect()->back()->with('success', 'Calendar event added successfully.');
    }


    public function updatePostStatus(Request $request, $id)
    {
            $request->validate([
            'status' => 'required|string'
        ]);

        $posts = CalendarEvent::findOrFail($id);

        $status = strtolower($request->status); // normalize

        if (!in_array($status, ['pending', 'completed'])) {
            return response()->json(['error' => 'Invalid status'], 422);
        }

        $posts->status = $status;
        $posts->save();

        
        // 🔔 SEND NOTIFICATION
        Auth::user()->notify(new PostCompletedNotification($posts));

        return response()->json([
            'success' => true,
            'status' => $posts->status
        ]);
    }
 

public function viewAttendance()
{
    $userId = Auth::id();

    $attendances = Attendance::where('user_id', $userId)->get();

    $events = [];

    $attendanceDates = [];

    foreach ($attendances as $attendance) {

        $attendanceDates[] = $attendance->date;

        // FORMAT TIME
        $checkIn = $attendance->check_in
            ? Carbon::parse($attendance->check_in)->format('h:i A')
            : '--';

        $checkOut = $attendance->check_out
            ? Carbon::parse($attendance->check_out)->format('h:i A')
            : 'Working';

        // EVENT
        $events[] = [
            'title' => $checkIn . ' - ' . $checkOut,
            'start' => $attendance->date,
            'classNames' => [$attendance->status],
        ];
    }

    // AUTO ABSENT UNTIL TODAY
    $start = Carbon::now()->startOfMonth();
    $today = Carbon::today();

    while ($start <= $today) {

        // SKIP SUNDAY
        if ($start->dayOfWeek != Carbon::SUNDAY) {

            if (!in_array($start->toDateString(), $attendanceDates)) {

                $events[] = [
                    'title' => 'Absent',
                    'start' => $start->toDateString(),
                    'classNames' => ['absent'],
                ];
            }
        }

        $start->addDay();
    }

    return view('user.view-attendance.index', compact('events'));
}


 public function updatePassword(Request $request)
    {
        // Validation
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        // Logged in user
        $user = Auth::user();

        // Check old password
        if (!Hash::check($request->old_password, $user->password)) {

            return back()->with('error', 'Old password is incorrect');
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Redirect according to role
        if ($user->role == 'super_admin') {

            return redirect()->route('super-admin.dashboard')
                ->with('success', 'Super Admin password updated successfully');

        } elseif ($user->role == 'admin') {

            return redirect()->route('admin.dashboard')
                ->with('success', 'Admin password updated successfully');

        } else {

            return redirect()->route('user.dashboard')
                ->with('success', 'User password updated successfully');
        }
    }

}
    