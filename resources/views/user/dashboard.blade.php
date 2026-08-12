@extends('layout.index')

@section('title', 'Dashboard')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-2">
    <h3>Welcome, {{ Auth::user()->name }}!</h3> 
    <div class="d-flex gap-2"> 
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#workflowModal">
            Year
        </button> 
         
        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#workflowModal">
            Month
        </button>
    </div>
</div>
<!-- tabs start -->  
<div class="row row-cols-1 row-cols-md-4 g-3">
    <div class="col">
        <div class="card rounded-4" style=" border-bottom:4px solid #10b981;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark ">Completed</div>
                    <div class="fw-bold text-success" style="font-size:2rem;" id="countCompleted">{{ $countCompleted }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);">
                    <i class="fas fa-check-circle text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style=" border-bottom:4px solid #6F42C1;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark">Total Tasks</div>
                    <div class="fw-bold" style="font-size:2rem;color:#6F42C1;" id="countTotal">{{ $countTotal }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                    <i class="fas fa-clipboard-list" style="color: #6F42C1;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style=" border-bottom:4px solid #0E6EFC;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark"> In Progress</div>
                    <div class="fw-bold" style="font-size:2rem;color:#0E6EFC;" id="countInProgress">{{ $countInProgress }}</div>
                </div>
                <div class="stat-icon" style="background: #6e42c116; color: var(--warning);">
                    <i class="fas fa-spinner" style="color: #0E6EFC;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card rounded-4" style=" border-bottom:4px solid #ff8800;">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-dark"> OverDue</div>
                    <div class="fw-bold " style="font-size:2rem;color:#ff8800;" id="countOverdue">{{ $countOverdue }}</div>
                </div>
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1); color: var(--danger);">
                    <i class="fa-regular fa-calendar" style="color: #ff8800;"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- tabs end -->

<!-- new starts  -->
<div class="row g-3">
    <!-- Task Management Section -->
    <div class="col-lg-7 d-flex flex-column gap-3">
        <div class="section-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold">Recent Tasks</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active" data-filter="all">
                        All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-filter="pending">
                        Pending
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-filter="completed">
                        Completed
                    </button>
                </div>

            </div>

            @foreach($tasks as $task)

            @php
            // Progress based on status
            $progress = match($task->status) {
            'Completed' => 100,
            'In_Progress' => 75,
            'Pending' => 40,
            default => 0,
            };

            // Priority color (ONLY for priority badge)
            $priorityClass = match($task->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            default => 'success',
            };

            // Status color (ONLY for status badge & progress bar)
            $statusClass = match($task->status) {
            'Completed' => 'success',
            'In_Progress' => 'primary',
            'Pending' => 'danger',
            default => 'secondary', // NOT STARTED (same color for all)
            };

            $statusLabel = ucfirst(str_replace('_',' ', $task->status));
            @endphp

            <div class="task-card priority-{{ $task->priority }} mb-4" id="task-card-{{ $task->id }}"
                data-status="{{ $task->status }}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="flex-grow-1">

                        <!-- Title -->
                        <h6 class="mb-2 fw-bold">{{ $task->title }}</h6>

                        <!-- Description -->
                        <p class="text-muted mb-2 small">
                            {{ $task->description ?? 'No description available' }}
                        </p>

                        <!-- Meta -->
                        <div class="d-flex align-items-center gap-3 flex-wrap">

                            <!-- Assigned User -->
                            <div class="d-flex align-items-center">
                                <div class="avatar" style="background:#e0e7ff;color:#4338ca;">
                                    {{ strtoupper(substr($task->creator->name,0,2)) }}
                                </div>
                                <span class="ms-2 small text-muted">
                                    {{ $task->creator->name }}
                                </span>
                            </div>

                            <!-- Priority -->
                            <span class="badge bg-{{ $priorityClass }}-subtle text-{{ $priorityClass }}">
                                {{ ucfirst($task->priority) }} Priority
                            </span>

                            <!-- Due Date -->
                            @if($task->due_date)
                            <span class="small text-muted">
                                <i class="far fa-calendar me-1"></i>
                                Due: {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}
                            </span>
                            @endif

                        </div>
                    </div>

                    <!-- STATUS BADGE (FIXED COLOR LOGIC) -->
                    <span class="badge-status bg-{{ $statusClass }}-subtle text-{{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>

                <!-- Progress -->
                <div class="progress">
                    <div class="progress-bar bg-{{ $statusClass }}" style="width: {{ $progress }}%">
                    </div>
                </div>

                <!-- Footer -->
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">{{ $progress }}% Complete</small>

                    <div>
                        <button type="button" class="btn btn-sm btn-link text-success" data-bs-toggle="modal"
                            data-bs-target="#taskDetailModal" data-task-id="{{ $task->id }}"
                            data-title="{{ $task->title }}"
                            data-description="{{ $task->description ?? 'No description' }}"
                            data-status="{{ $task->status }}" data-priority="{{ ucfirst($task->priority) }}"
                            data-progress="{{ $progress }}" data-assigned="{{ $task->creator->name }}"
                            data-due="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'N/A' }}"
                            data-comment='@json($task->comment)'>
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </div>

            </div>

            @endforeach

            @if($tasks->isEmpty())
            <p class="text-muted text-center">No tasks assigned yet.</p>
            @endif

        </div>
    </div>
    <div class="col-lg-5 d-flex flex-column gap-3">
        @if(auth()->check() && auth()->user()->department_id === 1)
        <div class="luxury-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="fas fa-folder icon-soft"></i> Website</div>
                <div class="text-muted-custom btn btn-outline-primary" data-bs-toggle="modal"
                    data-bs-target="#projectModal"><i class="fas fa-plus ms-1"></i> Add Project</div>
            </div>
            <div class="row g-2">
                @foreach($projects as $project)
                <div class="col-12 col-md-6 ">
                    <div class="project-box border-start border-end border-2" onmouseover="this.classList.add(
                                '{{ $project->status == 'pending' ? 'border-danger' :
                                    ($project->status == 'in_progress' ? 'border-warning' :
                                    ($project->status == 'completed' ? 'border-success' : 'border-secondary')) }}'
                            )" onmouseout="this.classList.remove(
                                '{{ $project->status == 'pending' ? 'border-danger' :
                                    ($project->status == 'in_progress' ? 'border-warning' :
                                    ($project->status == 'completed' ? 'border-success' : 'border-secondary')) }}'
                            )">
                        <div class="p-icon pi-indigo">
                            {{ strtoupper(substr($project->brand_name,0,2)) }}
                        </div>
                        <div class="row w-100">
                            <div class="col-9 editProjectDetailBtn" style="cursor: pointer;"
                                data-id="{{ $project->id }}" data-name="{{ $project->brand_name }}"
                                data-format="{{ $project->format }}" data-link="{{ $project->link }}"
                                data-requirement="{{ $project->requirement }}" data-comments="{{ $project->comments }}"
                                data-status="{{ $project->status }}">
                                <div class="fw-bold-custom text-nowrap">{{ $project->brand_name }}</div>
                                <div class="text-muted-custom" style="font-size:0.7rem">{{ $project->format }}</div>
                            </div>
                            <div class="col-3 text-end">
                                <a href="{{ $project->link }}" class="btn btn-sm text-success">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif


        @if(auth()->check() && auth()->user()->department_id === 2 || auth()->user()->department_id === 4)
        <div class="luxury-card">
            <div class="card-header-custom">
                <div class="card-title">
                    <i class="far fa-calendar-alt icon-soft"></i> Calendar
                </div>
                <div class="text-muted-custom me-4" id="monthLabel"></div>
                <div class="text-muted-custom">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#calendarEventModal">Add Posting</button>
                </div>
            </div>

            <div class="calendar-strip align-items-center">
                <div class="btn" style="cursor: pointer;" id="prevDay"> <i class="fas fa-chevron-left text-muted"></i>
                </div>
                <div id="calendarDays" class="d-flex gap-2"></div>
                <div class="btn" style="cursor: pointer;" id="nextDay"><i class="fas fa-chevron-right text-muted"></i>
                </div>
            </div>

            <div id="meetingContainer"></div>
            <div class="text-center mt-3 d-flex justify-content-center gap-3">
                <div class="text-muted-custom d-flex align-items-center">
                    <p class="mb-0">Current Date &nbsp;</p> <span class="legend-box today"></span>
                </div>
                <div class="text-muted-custom d-flex align-items-center">
                    <p class="mb-0"> Selected Date &nbsp;</p> <span class="legend-box selected"></span>
                </div>
            </div>
        </div>


        <!-- <div class="luxury-card">
            <div class="card-header-custom">
                <div class="card-title"><i class="far fa-clock icon-soft"></i> Reminders</div>
            </div>
            
            <div class="mb-3 fw-bold-custom d-flex align-items-center">
                    <i class="fas fa-chevron-up me-2"></i> Today <span class="ms-1 text-muted fw-normal">2</span>
            </div>

            <div class="reminder-item">
                <div class="d-flex gap-2">
                    <input class="form-check-input" type="checkbox">
                    <label class="fw-bold-custom" style="font-size: 0.85rem; line-height: 1.4;">Assess any new risks identified in the morning meeting.</label>
                </div>
                <div class="text-muted-custom ms-2"><i class="fas fa-thumbtack"></i></div>
            </div>

            <div class="reminder-item mb-0">
                <div class="d-flex gap-2">
                    <input class="form-check-input" type="checkbox">
                    <label class="fw-bold-custom" style="font-size: 0.85rem; line-height: 1.4;">Outline key points for tomorrow's stand-up meeting.</label>
                </div>
                <div class="text-muted-custom ms-2"><i class="fas fa-thumbtack text-primary"></i></div>
            </div>
        </div> -->
        @endif
    </div>
</div>
<!-- new ends  -->

<!-- Work Flow -->
@if($showWorkflowModal)
<!-- workflow For Video Editing -->
    @if(auth()->check() && in_array(auth()->user()->department_id, [2,4]))
    <div class="modal fade" id="workflowModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📅 Today’s Workflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-6">
                            <h6>Tasks</h6>
                        </div>
                        <div class="col-6 text-end">
                            <span class="fw-bold" style="font-size:12px;">Due Date</span>
                        </div>
                    </div>
                    @forelse($todayTasks as $task)
                    <div class="row mb-2">
                        <div class="col-6">
                            <span>{{ $loop->iteration }}. {{ $task->title }}</span>
                        </div>
                        <div class="col-6 text-end">
                            <span class="badge bg-{{ $task->status === 'Completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($task->status) }}
                            </span>
                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('j-M') : '-' }}
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No tasks for today</p>
                    @endforelse

                    <hr>

                    <div class="row">
                        <div class="col-6">
                            <h6>Posts</h6>
                        </div>
                        <div class="col-3 text-start">
                            <span class="fw-bold" style="font-size:12px;">Post Type</span>
                        </div>
                        <div class="col-3 text-end">
                            <span class="fw-bold" style="font-size:12px;">Due Date</span>
                        </div>
                    </div>
                    @forelse($todayPosts as $post)
                    <div class="row mb-2">
                        <div class="col-6">
                            <span>{{ $loop->iteration }}. Brand : {{ $post->brand_name }}</span>
                        </div>
                        <div class="col-3 text-start">
                            <span>{{ $post->post_type }}</span>
                        </div>
                        <div class="col-3 text-end">
                            <span class="badge bg-{{ $post->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($post->status) }}
                            </span>
                            {{ $post->posting_date ? \Carbon\Carbon::parse($post->posting_date)->format('j-M') : '-' }}
                        </div>
                    </div>
                    @empty
                    <p class="text-muted">No posts for today</p>
                    @endforelse
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal">
                        Start Working 🚀
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- WorkFlow For IT -->
    @if(auth()->check() && auth()->user()->department_id === 1)
    <div class="modal fade" id="workflowModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">📅 Today’s Workflow</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <h6>Tasks</h6>
                    @forelse($todayTasks as $task)
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ $loop->iteration }}. {{ $task->title }}</span>
                        <span class="badge bg-{{ $task->status === 'Completed' ? 'success' : 'warning' }}">
                            {{ ucfirst($task->status) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-muted">No tasks for today</p>
                    @endforelse


                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal">
                        Start Working 🚀
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif

<!-- Check in Modal -->
<div class="modal fade" id="checkInModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Check In</h5>
            </div>
            <div class="modal-body text-center">
                <div id="map" style="height: 300px; width:100%;"></div>
                <p id="status"></p>
                <button class="btn btn-success" onclick="checkIn()">Check In</button> 
            </div>
        </div>
    </div>
</div>
 <!-- Task Detail Modal -->
 <div class="modal fade" id="taskDetailModal" tabindex="-1">
     <div class="modal-dialog modal-xl modal-dialog-centered">
         <div class="modal-content rounded-4 overflow-hidden">
             <form method="POST" action="{{ route('tasks.updateStatus') }}">
                 @csrf
                 <input type="hidden" name="task_id" id="modalTaskId">

                 <!-- HEADER -->
                 <div class="modal-header">
                     <div>
                         <h5 class="modal-title fw-semibold mb-0">Task details</h5>
                     </div>
                     <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal"></button>
                 </div>

                 <!-- BODY -->
                 <div class="modal-body p-4">
                     <!--  -->
                     <h5 class="modal-title fw-semibold mb-0" id="modalTaskTitle"></h5>

                     <!-- Description -->
                     <p class="text-muted mb-4" id="modalTaskDescription"></p>

                     <div class="mb-4">
                         <label for="modalTaskComment" class="form-label fw-semibold">Note</label>
                         <textarea class="form-control" name="comment" id="modalTaskComment" rows="3" placeholder="Add a note"></textarea>
                     </div>

                     <!-- META INFO -->
                     <div class="row g-3 mb-4">

                         <div class="col-md-3">
                             <div class="meta-box">
                                 <span class="label">Status</span>

                                 <select class="form-select form-select-sm mt-1" name="status" id="modalTaskStatus">
                                     <option value="Pending">Pending</option>
                                     <option value="In_Progress">In Progress</option>
                                     <option value="Completed">Completed</option>
                                 </select>

                             </div>
                         </div>


                         <div class="col-md-3">
                             <div class="meta-box">
                                 <span class="label">Priority</span>
                                 <span class="value badge bg-warning-subtle text-warning" id="modalTaskPriority"></span>
                             </div>
                         </div>

                         <div class="col-md-3">
                             <div class="meta-box">
                                 <span class="label">Assigned To</span>
                                 <span class="value fw-semibold" id="modalTaskAssigned"></span>
                             </div>
                         </div>

                         <div class="col-md-3">
                             <div class="meta-box">
                                 <span class="label">Due Date</span>
                                 <span class="value" id="modalTaskDue"></span>
                             </div>
                         </div>
                        
                     </div>

                     <!-- PROGRESS -->
                     <div>
                         <div class="d-flex justify-content-between mb-1">
                             <span class="fw-semibold">Progress</span>
                             <span class="text-muted small" id="modalTaskProgressText"></span>
                         </div>

                         <div class="progress rounded-pill" style="height: 18px;">
                             <div class="progress-bar rounded-pill" id="modalTaskProgressBar"></div>
                         </div>
                     </div>

                 </div>

                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-primary">Save Status</button>
                 </div>
             </form>
         </div>
     </div>
 </div>
<script>
let map;

function checkIn() {

    const btn = document.querySelector('#checkInModal button');
    btn.disabled = true;
    btn.innerText = "Checking...";

    if (!navigator.geolocation) {
        alert("Geolocation not supported");
        btn.disabled = false;
        btn.innerText = "Check In";
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {

        let userLat = position.coords.latitude;
        let userLng = position.coords.longitude;

        let officeLat = 19.093426;
        let officeLng = 72.916283;

        let distance = getDistance(userLat, userLng, officeLat, officeLng);

        // Add marker
        L.marker([userLat, userLng]).addTo(map)
            .bindPopup("You are here")
            .openPopup();

        document.getElementById('status').innerText =
            `Distance: ${Math.round(distance)} meters`;

        if (distance > 100) {
            document.getElementById('status').innerText += " ❌ Out of range";
            btn.disabled = false;
            btn.innerText = "Check In";
            return;
        }

        // ✅ SEND TO BACKEND
        fetch('/check-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                lat: userLat,
                lng: userLng
            })
        })
        .then(res => res.json())
        .then(data => {
            document.getElementById('status').innerText = "✅ Checked in successfully";
            setTimeout(() => location.reload(), 1000);
        })
        .catch(() => {
            document.getElementById('status').innerText = "❌ Server error";
            btn.disabled = false;
            btn.innerText = "Check In";
        });

    }, function(error) {
        alert(error.message);
        btn.disabled = false;
        btn.innerText = "Check In";
    }, {
        enableHighAccuracy: true
    });
}

 
function initMap() {

    let officeLat = 19.093426;
    let officeLng = 72.916283;

    if (map) {
        map.remove();
    }

    map = L.map('map').setView([officeLat, officeLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    // Office marker
    L.marker([officeLat, officeLng]).addTo(map)
        .bindPopup("Office Location")
        .openPopup();

    // Radius circle
    L.circle([officeLat, officeLng], {
        radius: 100,
        color: 'green'
    }).addTo(map);

    setTimeout(() => {
        map.invalidateSize();
    }, 300);
}
function getDistance(lat1, lon1, lat2, lon2) {
    let R = 6371e3;

    let φ1 = lat1 * Math.PI / 180;
    let φ2 = lat2 * Math.PI / 180;

    let Δφ = (lat2 - lat1) * Math.PI / 180;
    let Δλ = (lon2 - lon1) * Math.PI / 180;

    let a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
        Math.cos(φ1) * Math.cos(φ2) *
        Math.sin(Δλ / 2) * Math.sin(Δλ / 2);

    let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}
</script>
 
@if($showCheckInModal)
<script>
document.addEventListener('DOMContentLoaded', function () {

    let modalEl = document.getElementById('checkInModal');
    let modal = new bootstrap.Modal(modalEl);

    modal.show();

    modalEl.addEventListener('shown.bs.modal', function () {
        initMap(); // load map after modal opens
    });

});
</script>
@endif
 



@if($showWorkflowModal)
<script>
window.addEventListener('load', function() {
    const modalEl = document.getElementById('workflowModal');
    if (!modalEl) return;

    const modal = new bootstrap.Modal(modalEl, {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
});
</script>
@endif

<!--  -->

<script>
window.taskConfig = {
    updateUrl: "{{ route('tasks.updateStatus') }}",
    countsUrl: "{{ route('tasks.counts') }}"
};
</script>

<script>
// Calendar  and datarendering

let currentDate = new Date();
const meetings = @json($meetings);

const calendarDays = document.getElementById('calendarDays');
const meetingContainer = document.getElementById('meetingContainer');
const monthLabel = document.getElementById('monthLabel');

function renderCalendar() {
    calendarDays.innerHTML = '';
    monthLabel.innerText = currentDate.toLocaleString('default', {
        month: 'long',
        year: 'numeric'
    });

    for (let i = -2; i <= 2; i++) {
        const date = new Date(currentDate);
        date.setDate(currentDate.getDate() + i);

        const yyyyMmDd = date.toISOString().split('T')[0];
        const dayNum = date.getDate();
        const dayName = date.toLocaleString('default', {
            weekday: 'short'
        });

        const div = document.createElement('div');

        const today = new Date();
        if (
            date.getDate() === today.getDate() &&
            date.getMonth() === today.getMonth() &&
            date.getFullYear() === today.getFullYear()
        ) {
            div.classList.add('today');
        }

        div.classList.add('cal-day');
        // Auto-select first visible date
        const firstDay = calendarDays.querySelector('.cal-day');


        div.dataset.date = yyyyMmDd;
        div.innerHTML = `
            <div class=" ">${dayNum}</div>
            <div class="fw-bold-custom">${dayName}</div>
        `;

        div.onclick = () => {
            document.querySelectorAll('.cal-day').forEach(d => d.classList.remove('active'));
            div.classList.add('active');
            loadMeetings(yyyyMmDd);
        };
        calendarDays.appendChild(div);
    }

    loadMeetings(currentDate.toISOString().split('T')[0]);
}


function loadMeetings(date) {
    meetingContainer.innerHTML = '';

    if (!meetings[date]) {
        meetingContainer.innerHTML = `<p class="text-muted-custom text-center mt-5">Task Not Created Yet..!!</p> `;
        return;
    }

    meetings[date].forEach(m => {
        meetingContainer.innerHTML += ` 
             <div class="meeting-box mb-3">
                <div class="row">
                    <div class="col-7">
                        <div class="fw-bold-custom">Brand Name - ${m.title}</div>
                        <div class="text-muted-custom mb-2">Post Type - ${m.postType}</div> 
                    </div>
                    <div class="col-5">
                       <div class="text-end" >
                        <span 
                            class="badge cursor-pointer ${
                                m.status === 'Pending' ? 'bg-danger' :
                                m.status === 'Completed' ? 'bg-success' :
                                'bg-secondary'
                            }"
                            onclick="toggleStatus(${m.id}, '${m.status}', this)"
                        >
                            Status - ${m.status.charAt(0).toUpperCase() + m.status.slice(1)}
                        </span>
                        </div>
                </div>
                <div >
                       <div class="text-muted-custom">Concept - ${m.concept}</div>
                    ${m.content ? `<div class="text-muted-custom">Content - ${m.content}</div>` : ''}
                    ${m.reference ? `<div class="text-muted-custom">Reference - ${m.reference}</div>` : ''}
                    ${m.comment ? `<div class="text-muted-custom">Comment - ${m.comment}</div>` : ''}
                </div>
            </div>
        `;
    });
}

document.getElementById('prevDay').onclick = () => {
    currentDate.setDate(currentDate.getDate() - 5);
    renderCalendar();
};

document.getElementById('nextDay').onclick = () => {
    currentDate.setDate(currentDate.getDate() + 5);
    renderCalendar();
};

renderCalendar();
</script>



@endsection