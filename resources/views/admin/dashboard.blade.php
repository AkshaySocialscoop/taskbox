@extends('layout.index')

@section('title', 'Dashboard')

@section('content')


<div class="row mb-4">
    <div class="col-md-9">
        <h1>Welcome, {{ Auth::user()->name }} !</h1>
    </div>
    <div class="col-md-3 text-md-end">
        <select class="form-select" id="single-select-field" data-placeholder="Choose one thing">

            <option disabled selected>--Select Employee--</option>
            <option></option>
        </select>
    </div>
</div>



<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Total Tasks</p>
                    <h3 class="mb-0 fw-bold">124</h3>
                </div>
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">In Progress</p>
                    <h3 class="mb-0 fw-bold">48</h3>
                </div>
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                    <i class="fas fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Completed</p>
                    <h3 class="mb-0 fw-bold">68</h3>
                </div>
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1">Overdue</p>
                    <h3 class="mb-0 fw-bold">8</h3>
                </div>
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                    <i class="fa-regular fa-calendar"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Task Management Section -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold">Recent Tasks</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">All</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Pending</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Completed</button>
                </div>
            </div>

            <!-- Task Card 1 -->
            <div class="task-card priority-high">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 fw-bold">Website Redesign - Homepage</h6>
                        <p class="text-muted mb-2 small">Update the homepage layout according to new brand guidelines
                            and improve responsiveness.</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar" style="background: #fef3c7; color: #f59e0b;">JS</div>
                                <span class="ms-2 small text-muted">John Smith</span>
                            </div>
                            <span class="badge bg-danger-subtle text-danger">High Priority</span>
                            <span class="small text-muted"><i class="far fa-calendar me-1"></i>Due: Jan 15</span>
                        </div>
                    </div>
                    <span class="badge-status bg-warning-subtle text-warning">In Progress</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-warning" style="width: 65%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">65% Complete</small>
                    <div>
                        <button class="btn btn-sm btn-link text-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-link text-success"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Task Card 2 -->
            <div class="task-card priority-medium">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 fw-bold">Database Optimization</h6>
                        <p class="text-muted mb-2 small">Optimize database queries and improve indexing for better
                            performance.</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar" style="background: #dbeafe; color: #3b82f6;">MC</div>
                                <span class="ms-2 small text-muted">Maria Chen</span>
                            </div>
                            <span class="badge bg-warning-subtle text-warning">Medium Priority</span>
                            <span class="small text-muted"><i class="far fa-calendar me-1"></i>Due: Jan 18</span>
                        </div>
                    </div>
                    <span class="badge-status bg-primary-subtle text-primary">Not Started</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-primary" style="width: 0%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">0% Complete</small>
                    <div>
                        <button class="btn btn-sm btn-link text-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-link text-success"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>

            <!-- Task Card 3 -->
            <div class="task-card priority-low">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">
                        <h6 class="mb-2 fw-bold">Documentation Update</h6>
                        <p class="text-muted mb-2 small">Update API documentation with new endpoints and examples.</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center">
                                <div class="avatar" style="background: #dcfce7; color: #10b981;">RP</div>
                                <span class="ms-2 small text-muted">Robert Park</span>
                            </div>
                            <span class="badge bg-success-subtle text-success">Low Priority</span>
                            <span class="small text-muted"><i class="far fa-calendar me-1"></i>Due: Jan 20</span>
                        </div>
                    </div>
                    <span class="badge-status bg-success-subtle text-success">Completed</span>
                </div>
                <div class="progress">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">100% Complete</small>
                    <div>
                        <button class="btn btn-sm btn-link text-primary"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-link text-success"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-link text-danger"><i class="fas fa-trash"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="section-card">
            <h5 class="fw-bold mb-3">Assign Task</h5>

            <select class="form-select mb-2">
                <option>Select Department</option>
                <option>IT</option>
                <option>HR</option>
                <option>Marketing</option>
            </select>

            <input type="text" class="form-control mb-2" placeholder="Task Title">

            <button class="btn btn-primary w-100">Assign Task</button>
        </div>
        <div class="section-card">
            <h5 class="fw-bold mb-3">Departments Overview</h5>

            <div class="d-flex justify-content-between mb-2">
                <span>IT Department</span>
                <span class="badge bg-success">On Track</span>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span>HR Department</span>
                <span class="badge bg-warning">Delayed</span>
            </div>

            <div class="d-flex justify-content-between">
                <span>Finance</span>
                <span class="badge bg-danger">Critical</span>
            </div>
        </div>

        <div class="section-card">
            <h5 class="fw-bold mb-3">Department Workload</h5>

            <p class="mb-1">IT</p>
            <div class="progress mb-2">
                <div class="progress-bar bg-success" style="width: 60%">60%</div>
            </div>

            <p class="mb-1">HR</p>
            <div class="progress mb-2">
                <div class="progress-bar bg-warning" style="width: 80%">80%</div>
            </div>

            <p class="mb-1">Finance</p>
            <div class="progress">
                <div class="progress-bar bg-danger" style="width: 95%">95%</div>
            </div>
        </div>
    </div>



</div>

@endsection