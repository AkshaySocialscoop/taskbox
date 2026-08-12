@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
<!--breadcrumb-->
<div class="row mb-4">
    <div class="col-md-9">
        <h3>Welcome, {{ Auth::user()->name ?? 'User' }} !</h3>
       
    </div>
</div>
<!--end breadcrumb-->

<!-- Stats Cards -->
<div class="row row-cols-1 row-cols-md-4 g-3">

    <div class="col">
        <div class="card rounded-4" style="border-bottom:4px solid #10b981;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark">Tasks Completed</div>
                    <div class="fw-bold text-success" style="font-size:2rem;">
                        {{ $completedtasks ?? 0 }}
                    </div>
                </div>

                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style="border-bottom:4px solid #6F42C1;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark">Total Tasks</div>
                    <div class="fw-bold" style="font-size:2rem;color:#6F42C1;">
                        {{ $totaltasks ?? 0 }}
                    </div>
                </div>

                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1);">
                    <i class="fas fa-clipboard-list" style="color:#6F42C1;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style="border-bottom:4px solid #0E6EFC;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark">Task In Progress</div>
                    <div class="fw-bold" style="font-size:2rem;color:#0E6EFC;">
                        {{ $inprogress ?? 0 }}
                    </div>
                </div>

                <div class="stat-icon" style="background:#6e42c116;">
                    <i class="fas fa-spinner" style="color:#0E6EFC;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style="border-bottom:4px solid #ff8800;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark">OverDue</div>
                    <div class="fw-bold" style="font-size:2rem;color:#ff8800;">
                        {{ $overdue ?? 0 }}
                    </div>
                </div>

                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">
                    <i class="fa-regular fa-calendar" style="color:#ff8800;"></i>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End Stats Cards -->

<!-- Task Management Section -->
<div class="row mt-4">

    <!-- Left Section -->
    <div class="col-12 col-xl-9">

        <div class="row">

            <!-- Website Section -->
            <div class="col-12 col-xl-6 mb-4">

                <div class="card w-100 rounded-4">
                    <div class="card-body">

                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Website</h5>
                        </div>

                        <div class="d-flex flex-column gap-4">

                            @forelse($projects ?? [] as $project)

                            @php
                            $statusProgress = [
                            'pending' => 30,
                            'in_progress' => 70,
                            'completed' => 100,
                            ];

                            $statusColor = [
                            'pending' => 'bg-warning',
                            'in_progress' => 'bg-primary',
                            'completed' => 'bg-success',
                            ];

                            $progress = $statusProgress[$project->status ?? 'pending'] ?? 0;
                            $color = $statusColor[$project->status ?? 'pending'] ?? 'bg-secondary';
                            @endphp

                            <div class="d-flex align-items-center gap-4">

                                <div class="d-flex align-items-center gap-3 flex-grow-1 flex-shrink-0">

                                    <div class="wh-48 d-flex align-items-center justify-content-center rounded-3 border p-icon pi-indigo">
                                        {{ strtoupper(substr($project->brand_name ?? '',0,2)) }}
                                    </div>

                                    <div>
                                        <h6 class="mb-0 fw-bold">
                                            {{ $project->brand_name ?? 'N/A' }}
                                        </h6>

                                        <p class="mb-0">
                                            {{ $project->format ?? 'N/A' }}
                                        </p>
                                    </div>

                                </div>

                                <div class="progress w-25" style="height:5px;">
                                    <div class="progress-bar {{ $color }}"
                                        style="width:{{ $progress }}%">
                                    </div>
                                </div>

                                <div>
                                    <p class="mb-0 fs-6">{{ $progress }}%</p>
                                </div>

                            </div>

                            @empty

                            <div class="text-muted text-center py-3">
                                No Projects Found
                            </div>

                            @endforelse

                        </div>

                    </div>
                </div>

            </div>

            <!-- Pending Post Section -->
            <div class="col-12 col-xl-6 mb-4">

                <div class="card rounded-4 w-100">
                    <div class="card-body">

                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <h5 class="mb-0 fw-bold">Pending Post</h5>
                        </div>

                        <div class="payments-list">
                            <div class="d-flex flex-column gap-4">

                                @forelse($posts ?? [] as $post)

                                <div class="d-flex align-items-center gap-3">

                                    <div class="wh-48 d-flex align-items-center justify-content-center bg-danger rounded-circle p-icon pi-indigo">
                                        {{ strtoupper(substr($post->brand_name ?? '',0,2)) }}
                                    </div>

                                    <div class="flex-grow-1">

                                        <h6 class="mb-0 fw-bold">
                                            {{ $post->brand_name ?? 'N/A' }}
                                        </h6>

                                        <p class="mb-0">
                                            {{ $post->post_type ?? 'N/A' }}
                                        </p>

                                    </div>

                                    <div class="d-flex align-items-center">

                                        <h6 class="mb-0 fw-bold">
                                            {{ $post->created_at?->format('d M , h:i A') ?? 'N/A' }}
                                        </h6>

                                    </div>

                                </div>

                                @empty

                                <div class="text-muted text-center py-3">
                                    No Pending Posts
                                </div>

                                @endforelse

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Right Section -->
    <div class="col-12 col-xl-3">

        <div class="col mb-4">

            <div class="card rounded-4" style="border-bottom:4px solid #10b981;">

                <div class="card-body d-flex align-items-center justify-content-between">

                    <div>
                        <div class="text-dark">Website Completed</div>

                        <div class="fw-bold text-success" style="font-size:2rem;">
                            {{ $websitecompleted ?? 0 }}
                        </div>
                    </div>

                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="col">

            <div class="card rounded-4" style="border-bottom:4px solid rgb(13 202 240);">

                <div class="card-body d-flex align-items-center justify-content-between">

                    <div>

                        <div class="text-dark">Post Uploaded</div>

                        <div class="fw-bold text-info" style="font-size:2rem;">
                            {{ $uploaded_posts ?? 0 }}
                        </div>

                    </div>

                    <div class="stat-icon" style="background: rgba(13, 202, 240, 0.23);">
                        <i class="fas fa-check-circle text-info"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<!-- End Task Management Section -->

@endsection