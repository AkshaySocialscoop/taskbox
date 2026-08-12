@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
 
<div class="row">
    <div class="col-12 col-xl-12">
        <div class="card border-top border-3 border-danger rounded-0">
            <div class="card-header py-3 px-4">
                <h5 class="mb-0 text-danger">Assign New Task</h5>
            </div> 
            <div class="card-body p-4">
                <form class="row g-3" method="POST"
                action="{{ route('tasks.store') }}"
                enctype="multipart/form-data"> 
                @csrf
                   <!-- Task Title -->
                    <div class="col-md-6">
                        <label class="form-label">Task Title</label>
                        <input type="text" name="title" class="form-control rounded-0" required>
                    </div>

                    <!-- Assign To -->
                    <div class="col-md-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-select rounded-0" required>
                            <option value="">Select employee</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div> 

                    <!-- Priority -->
                    <div class="col-md-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select rounded-0" required>
                            <option value="" disabled>Select priority</option>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="col-md-12">
                        <label class="form-label">Task Description</label>
                        <textarea name="description" class="form-control rounded-0" rows="4"></textarea>
                    </div>

                    <!-- Due Date -->
                    <div class="col-md-4">
                        <label class="form-label">Due Date</label>
                        <input type="date" name="due_date" class="form-control rounded-0" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Attachment -->
                    <div class="col-md-4">
                        <label class="form-label">Attachment</label>
                        <input type="file" name="attachment" class="form-control rounded-0">
                    </div> 

                    <!-- Buttons -->
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-danger px-4 rounded-0">Assign Task</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Tasks Assigned</h5>
    </div>

    <div class="card-body">
        @if($tasks->count())
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Task</th>
                        <th>Assigned To</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Assign Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $task)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $task->title }}</td>

                        <td>
                            {{ $task->assignedUser->name ?? 'N/A' }}
                        </td>

                        <td>
                            <span class="badge bg-{{ 
                                $task->priority == 'high' ? 'danger' :
                                ($task->priority == 'medium' ? 'warning' : 'secondary')
                            }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>

                        <td>
                            @switch($task->status)

                                @case('Pending')
                                    <span class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">
                                        Pending <i class="bi bi-x-lg ms-2"></i>
                                    </span>
                                    @break

                                @case('In_Progress')
                                    <span class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">
                                        In Progress <i class="bi bi-info-circle ms-2"></i>
                                    </span>
                                    @break

                                @case('Completed')
                                    <span class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">
                                        Completed <i class="bi bi-check2 ms-2"></i>
                                    </span>
                                    @break

                                @default
                                    <span class="lable-table bg-secondary-subtle text-secondary rounded border font-text2 fw-bold">
                                        {{ ucfirst(str_replace('_',' ', $task->status)) }}
                                    </span>

                            @endswitch
                        </td>


                        <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('j-M') : '-' }}</td>
                        <td> {{ $task->created_at ? \Carbon\Carbon::parse($task->created_at)->format('j-M, h:i A')   : '-'  }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
            <p class="text-muted">No tasks assigned yet.</p>
        @endif
    </div>
</div>

@endsection

