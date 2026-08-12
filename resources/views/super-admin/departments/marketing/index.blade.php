@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
    <h1>Social Media</h1>
    <p>Welcome to Social Media Department</p>

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Campaigns</p>
                        <h3 class="mb-0 fw-bold">32</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Active Campaigns</p>
                        <h3 class="mb-0 fw-bold">12</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: var(--warning);">
                        <i class="fas fa-play-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Completed Campaigns</p>
                        <h3 class="mb-0 fw-bold">20</h3>
                    </div>
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>  

    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Campaign Overview</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Campaign Name</th>
                                <th>Start Date</th>
                                <th>End Date</th>       
                                <th>Status</th>
                                <th>Budget</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <!-- <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->start_date }}</td>
                                <td>{{ $project->end_date }}</td>
                                <td>{{ $project->status }}</td>
                                <td>${{ $project->budget }}</td>
                            </tr> -->
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>  

    <div class="row mb-4">  
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Engagement Over Time</h5>
                </div>
                <div class="card-body">
                    <canvas id="engagementChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Top Performing Posts</h5>
                </div>
                <div class="card-body">
                    <canvas id="topPostsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">  
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title   ">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">Posted new content on Facebook - "Summer Sale Launch"</li>
                        <li class="list-group-item">Responded to comments on Instagram</li>
                        <li class="list-group-item">Analyzed Twitter engagement metrics</li>
                        <li class="list-group-item">Scheduled posts for next week</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection 

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Engagement Over Time Chart
    var ctx = document.getElementById('engagementChart').getContext('2d');
    var engagementChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June'],
            datasets: [{
                label: 'Engagement',
                data: [120, 190, 300, 250, 400, 450],
                backgroundColor: 'rgba(54, 162, 235, 0  .2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }); 
    // Top Performing Posts Chart
    var ctx2 = document.getElementById('topPostsChart').getContext('2d');
    var topPostsChart = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Post 1', 'Post 2', 'Post 3', 'Post 4', 'Post 5'],
            datasets: [{
                label: 'Likes',
                data: [300, 500, 400, 600, 700],
                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                borderColor: 'rgba(255, 206, 86, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
