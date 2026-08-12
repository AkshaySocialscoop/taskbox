<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LeaveRequestController extends Controller
{
    public function create()
    {
        $leaveRequests = LeaveRequest::where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('user.apply-leave.index', compact('leaveRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date|after_or_equal:today',
            'to_date' => 'required|date|after_or_equal:from_date',
            'leave_type' => 'required|in:paid_leave,sick_leave,casual_leave',
            'reason' => 'required|string|max:1000',
        ]);

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'leave_type' => $request->leave_type,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return redirect()->route('leave.apply')->with('success', 'Leave request submitted successfully.');
    }

    public function adminIndex()
    {
        $leaveRequests = LeaveRequest::with('user')
            ->latest()
            ->get();

        return view('super-admin.leave-requests.index', compact('leaveRequests'));
    }

    public function approve($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave requests can be approved.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Leave request approved.');
    }

    public function reject($id)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending leave requests can be rejected.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Leave request rejected.');
    }
}
