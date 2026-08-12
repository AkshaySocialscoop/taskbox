@extends('layout.index')

@section('title', 'Audit Logs')

@section('content')

<div id="filterSection" style="display:none;">

    <div class="card border">

        <div class="card-body">

            <form method="GET" action="{{ route('audit-logs.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-md-2">
                        <label class="form-label">Module</label>
                        <input type="text"
                            class="form-control"
                            name="module"
                            value="{{ request('module') }}"
                            placeholder="Module">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Action</label>
                        <input type="text"
                            class="form-control"
                            name="action"
                            value="{{ request('action') }}"
                            placeholder="Action">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">User</label>
                        <input type="text"
                            class="form-control"
                            name="user"
                            value="{{ request('user') }}"
                            placeholder="User">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <input type="date"
                            class="form-control"
                            name="date"
                            value="{{ request('date') }}">
                    </div>

                    <div class="col-md-3 d-flex gap-2">
                        <button class="btn btn-primary">
                            Search
                        </button>

                        <a href="{{ route('audit-logs.index') }}"
                            class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<div class="card">

    <div class="card-body">


        <div class="d-flex justify-content-between align-items-center mb-4">

            <h5 class="mb-0">Audit Logs List</h5>

            <x-filter-button target="filterSection" />

        </div>

        <div class="table-responsive">

            <table id="example" class="table table-striped table-bordered">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($auditLogs as $log)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>
                            {{ $log->user->name ?? '-' }}
                        </td>

                        <td>{{ $log->module }}</td>

                        <td>
                            <span class="badge bg-primary">
                                {{ $log->action }}
                            </span>
                        </td>

                        <td>{{ $log->event }}</td>

                        <td>{{ $log->description }}</td>

                        <td>{{ $log->ip_address }}</td>

                        <td>{{ $log->created_at->format('d M Y h:i A') }}</td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No Audit Logs Found
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection