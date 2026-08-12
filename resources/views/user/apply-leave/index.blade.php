@extends('layout.index')

@section('title', 'Apply Leave')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card rounded-4 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold">Apply for Leave</h4>
                    <p class="text-muted">Submit your leave request and track its approval status.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('leave.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control" value="{{ old('from_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control" value="{{ old('to_date') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Leave Type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="">Select Type</option>
                            <option value="paid_leave" {{ old('leave_type') === 'paid_leave' ? 'selected' : '' }}>Paid Leave</option>
                            <option value="sick_leave" {{ old('leave_type') === 'sick_leave' ? 'selected' : '' }}>Sick Leave</option>
                            <option value="casual_leave" {{ old('leave_type') === 'casual_leave' ? 'selected' : '' }}>Casual Leave</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason</label>
                        <textarea name="reason" class="form-control" rows="4" required>{{ old('reason') }}</textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Submit Leave Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card rounded-4 p-4">
            <h5 class="fw-bold mb-3">Your Leave Requests</h5>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $request)
                            <tr>
                                <td>{{ $request->from_date->format('d M Y') }} - {{ $request->to_date->format('d M Y') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $request->leave_type)) }}</td>
                                <td>{{ $request->reason }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($request->status === 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No leave requests submitted yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection