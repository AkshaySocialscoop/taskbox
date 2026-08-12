@extends('layout.index')

@section('title', 'Leave Requests')

@section('content')

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card rounded-4 p-4">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h4 class="fw-bold">Leave Requests</h4>
                    <p class="text-muted">Review and approve or reject leave requests from users.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Leave Dates</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaveRequests as $request)
                            <tr>
                                <td>{{ $request->user->name ?? 'Unknown' }}</td>
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
                                <td>{{ $request->created_at->diffForHumans() }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                        <div class="d-flex gap-2">
                                            <form method="POST" action="{{ route('super-admin.leave.approve', $request->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('super-admin.leave.reject', $request->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No leave requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
