 <!--start sidebar-->

 <aside class="sidebar-wrapper">
   <div class="sidebar-header ">
     <div class="logo-icon mx-auto">
       <img src="{{ asset('assets/images/task-box-logo-white.png') }}" class="logo-img" alt="" style="width: 150px;">
     </div>
     <!-- <div class="logo-name flex-grow-1">
        <h5 class="mb-0">Metoxi</h5>
      </div> -->
     <div class="sidebar-close">
       <span class="material-icons-outlined">close</span>
     </div>
   </div>
   <div class="sidebar-nav" data-simplebar="true">

     <!--navigation-->
     <ul class="metismenu" id="sidenav">
       @php $role = auth()->user()->role ?? null; @endphp

       @if($role === 'super_admin')
       <li>
         <a href="{{ route('super-admin.dashboard') }}">
           <div class="parent-icon"><i class="material-icons-outlined">home</i>
           </div>
           <div class="menu-title">Dashboard</div>
         </a>
       </li>
       <li>
         <a class="has-arrow" href="javascript:;">
           <div class="parent-icon"><i class="material-icons-outlined">person</i>
           </div>
           <div class="menu-title">Employees</div>
         </a>
         <ul>
           <li><a href="{{ route('users.index') }}"><i class="material-icons-outlined">arrow_right</i>Add Employee</a>
           </li>
           <li><a href="{{ route('roles.index') }}"><i class="material-icons-outlined">arrow_right</i>Add Role</a>
           </li>
           <li><a href="{{ route('departments.index') }}"><i class="material-icons-outlined">arrow_right</i>Add Department</a>
           </li>
           <li><a href="{{ route('shifts.index') }}"><i class="material-icons-outlined">arrow_right</i>Add Shift</a>
           </li>
           <li><a href="{{ route('attendance.index') }}"><i class="material-icons-outlined">arrow_right</i>Attendance</a>
           </li>
           <li><a href="{{ route('attendance.viewattendance') }}"><i class="material-icons-outlined">arrow_right</i>View Attendance</a>
           </li>
           <li><a href="{{ route('super-admin.leave.requests') }}"><i class="material-icons-outlined">arrow_right</i>Leave Requests</a>
           </li>
           <li><a href="app-invoice.html"><i class="material-icons-outlined">arrow_right</i>Reports</a>
           </li>
         </ul>
       </li>
       <li>
         <a href="{{ route('tasks.index') }}">
           <div class="parent-icon"><i class="material-icons-outlined">description</i>
           </div>
           <div class="menu-title">Task</div>
         </a>
       </li>
       <li>
         <a class="has-arrow" href="javascript:;">
           <div class="parent-icon"><i class="material-icons-outlined">apps</i>
           </div>
           <div class="menu-title">Departments</div>
         </a>
         <ul>
           <li><a href="{{ route('departments.IT') }}"><i class="material-icons-outlined">arrow_right</i>IT</a>
           </li>
           <li><a href="{{ route('departments.social') }}"><i class="material-icons-outlined">arrow_right</i>Social Media</a>
           </li>
           <li><a href="app-invoice.html"><i class="material-icons-outlined">arrow_right</i>Marketing</a>
           </li>
           <li><a href="app-invoice.html"><i class="material-icons-outlined">arrow_right</i>Video Editing</a>
           </li>
         </ul>
       </li>
       <li>
         <a href="/media">
           <div class="parent-icon"><i class="material-icons-outlined">image</i>
           </div>
           <div class="menu-title">Media</div>
         </a>
       </li>
       <li>
         <a href="/sticky-notes">
           <div class="parent-icon"><i class="material-icons-outlined">edit</i>
           </div>
           <div class="menu-title">Sticky Notes</div>
         </a>
       </li>
       <li>
         <a href="{{ route('audit-logs.index') }}">
           <div class="parent-icon">
             <i class="material-icons-outlined">history</i>
           </div>
           <div class="menu-title">Audit Logs</div>
         </a>
       </li>
       @endif

       @if($role === 'admin')
       <li>
         <a href="{{ route('admin.dashboard') }}">
           <div class="parent-icon"><i class="material-icons-outlined">home</i>
           </div>
           <div class="menu-title">Dashboard</div>
         </a>
       </li>
       <li>
         <a href="{{ route('tasks.index') }}">
           <div class="parent-icon"><i class="material-icons-outlined">description</i>
           </div>
           <div class="menu-title">Task</div>
         </a>
       </li>
       <li>
         <a href="{{ route('sticky-notes.index') }}">
           <div class="parent-icon"><i class="material-icons-outlined">edit</i>
           </div>
           <div class="menu-title">Sticky Notes</div>
         </a>
       </li>
       @endif

       @if($role === 'user')
       <li>
         <a href="{{ route('user.dashboard') }}">
           <div class="parent-icon"><i class="material-icons-outlined">home</i>
           </div>
           <div class="menu-title">Dashboard</div>
         </a>
       </li>
       <li>
         <a href="{{ route('tasks.index') }}">
           <div class="parent-icon"><i class="material-icons-outlined">description</i>
           </div>
           <div class="menu-title">Task</div>
         </a>
       </li>
       <li>
         <a href="{{ route('view-attendance') }}">
           <div class="parent-icon"><i class="material-icons-outlined">description</i>
           </div>
           <div class="menu-title">View Attendance</div>
         </a>
       </li>
       <li>
         <a href="{{ route('leave.apply') }}">
           <div class="parent-icon"><i class="material-icons-outlined">beach_access</i>
           </div>
           <div class="menu-title">Apply Leave</div>
         </a>
       </li>
       <li>
         <a href="/media">
           <div class="parent-icon"><i class="material-icons-outlined">image</i>
           </div>
           <div class="menu-title">Media</div>
         </a>
       </li>
       <li>
         <a href="{{ route('sticky-notes.index') }}">
           <div class="parent-icon"><i class="material-icons-outlined">edit</i>
           </div>
           <div class="menu-title">Sticky Notes</div>
         </a>
       </li>
       @endif

       
      @php
    $user = Auth::user();
    $hasGoogle = $user && $user->google_id;
@endphp

@if($hasGoogle)
    <li>
        <a href="{{ route('googlechat.index') }}" style="display:flex; align-items:center; gap:8px;">
            <span>💬</span> Google Chat
            <span style="background:#2ecc71; width:8px; height:8px; border-radius:50%; display:inline-block;"></span>
        </a>
    </li>
@else
    <li>
        <a href="{{ route('google.login') }}" style="display:flex; align-items:center; gap:8px;">
            <span>🔗</span> Connect Google Chat
        </a>
    </li>
@endif

     </ul>
     <!--end navigation-->
   </div>
   <div class="sidebar-bottom gap-4 pi-indigo">
     <div class="dropdown dropup-center dropup dropdown-help">
       <a class="dropdown-item d-flex align-items-center gap-2 py-2 logout-btn" href="#">
         <i class="material-icons-outlined text-white">power_settings_new</i>
         <h5 class="mb-0 text-white">Logout</h5>
       </a>
       <form id="logout-form"
         action="{{ route('logout') }}"
         method="POST"
         class="d-none">
         @csrf
       </form>
     </div>
   </div>
 </aside>
 <!--end sidebar-->