@extends('layout.index')

@section('title', 'Dashboard')

@section('content')
<style>
  .bg-present {
    background: #28a745 !important;
    /* Green */
    color: #fff;
  }

  .bg-absent {
    background: #dc3545 !important;
    /* Red */
    color: #fff;
  }

  .bg-late {
    background: #fd7e14 !important;
    /* Orange */
    color: #fff;
  }

  .bg-halfday {
    background: #ffc107 !important;
    /* Yellow */
    color: #000;
  }

  .bg-paid_leave {
    background: #0d6efd !important;
    /* Blue */
    color: #fff;
  }



  .bg-overtime {
    background: #6f42c1 !important;
    /* Purple */
    color: #fff;
  }
</style>
<!-- Attendance List -->
<div class="card">
  <div class="card-body">
    

    <div class="" id="filterSection" style="display:none;">
      <div class="mb-4 mt-2">

        <form method="GET" action="{{ route('attendance.index') }}">

          <div class="row g-3 align-items-end">

            <div class="col-md-4">
              <label class="form-label">Employee</label>
              <select class="form-select" name="user_id">
                <option value="">All Employees</option>

                @foreach($users as $user)
                <option value="{{ $user->id }}"
                  {{ request('user_id') == $user->id ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Date</label>
              <input type="date"
                class="form-control"
                name="date"
                value="{{ request('date') }}">
            </div>

            <div class="col-md-3">
              <label class="form-label">Status</label>

              <select class="form-select" name="status">
                <option value="">All Status</option>

                <option value="present" {{ request('status')=='present' ? 'selected' : '' }}>Present</option>

                <option value="late" {{ request('status')=='late' ? 'selected' : '' }}>Late</option>

                <option value="half_day" {{ request('status')=='half_day' ? 'selected' : '' }}>Half Day</option>

                <option value="absent" {{ request('status')=='absent' ? 'selected' : '' }}>Absent</option>

                <option value="paid_leave" {{ request('status')=='paid_leave' ? 'selected' : '' }}>Paid Leave</option>

                <option value="week_off" {{ request('status')=='week_off' ? 'selected' : '' }}>Week Off</option>
              </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
              <button class="btn  btn-primary">
                Search
              </button>

              <button type="button"
                class="btn btn-secondary "
                onclick="window.location.href='{{ route('attendance.index') }}'">
                Reset
              </button>
            </div>

          </div>

        </form>

      </div>
    </div>


    <div class="card">

        <div class="d-flex justify-content-between align-items-center mb-4">

          <h5 class="mb-0">Attendance List</h5>

          <div class="d-flex gap-2">
            <x-filter-button target="filterSection" />
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createAttendanceModal">Add Attendance</button>
          </div>

        </div>

        <div class="table-responsive">


          <table id="example" class="table table-striped table-bordered" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>User</th>
                <th>Date</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Working Hours</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>

            <tbody>
              @forelse($attendances as $attendance)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $attendance->user->name }}</td>
                <td>{{ $attendance->date }}</td>
                <td>{{ $attendance->check_in }}</td>
                <td> {{ $attendance->check_out }}</td>
                <td>
                  @if($attendance->check_in && $attendance->check_out)
                  @php
                  $checkIn = \Carbon\Carbon::parse($attendance->check_in);
                  $checkOut = \Carbon\Carbon::parse($attendance->check_out);
                  $workingHours = $checkOut->diff($checkIn)->format('%H:%I:%S');
                  @endphp
                  {{ $workingHours }}
                  @else
                  N/A
                  @endif
                </td>
                <td>
                  @php
                  $statusClass = match($attendance->status) {
                  'present' => 'bg-present',
                  'absent' => 'bg-absent',
                  'late' => 'bg-late',
                  'half_day' => 'bg-halfday',
                  'paid_leave' => 'bg-paid_leave',
                  'week_off' => 'bg-weekly_off',
                  };
                  @endphp

                  <span class="badge {{ $statusClass }}">
                    {{ ucwords(str_replace('_', ' ', $attendance->status)) }}
                  </span>
                </td>
                <td>
                  <a href="javascript:;"
                    class="btn btn-sm text-warning editAttendanceBtn"
                    data-id="{{ $attendance->id }}"
                    data-name="{{ $attendance->user->name }}"
                    data-date="{{ $attendance->date }}"
                    data-check-in="{{ $attendance->check_in }}"
                    data-check-out="{{ $attendance->check_out }}"
                    data-status="{{ $attendance->status }}">
                    <i class="lni lni-pencil-alt fs-6"></i>
                  </a>

                  <form action="{{ route('attendance.destroy', $attendance->id) }}"
                    method="POST"
                    class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                      class="btn btn-sm text-danger"> <i class="lni lni-trash fs-6 "></i>
                    </button>
                  </form>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center">No Attendance found</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

    </div>

    <!-- // Pagination -->
    <div class="mt-3">
      {{ $attendances->withQueryString()->links() }}
    </div>

  </div>
</div>
<!-- Create Attendance Modal -->
<div class="modal fade" id="createAttendanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="createAttendanceForm" method="POST" action="{{ route('attendance.store') }}">
        @csrf

        <div class="modal-header">
          <h5 class="modal-title">Add Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>Employee</label>
            <select name="user_id" class="form-control" required>
              <option value="">Select Employee</option>
              @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" class="form-control" required max="{{ date('Y-m-d') }}">
          </div>

          <div class="mb-3">
            <label>Check In Time</label>
            <input type="time" name="check_in" class="form-control" step="1">
          </div>

          <div class="mb-3">
            <label>Check Out Time</label>
            <input type="time" name="check_out" class="form-control" step="1">
          </div>

          <div class="mb-3">
            <label>Status</label>
            <select name="status" class="form-control" required>
              <option value="present" selected>Present</option>
              <option value="absent">Absent</option>
              <option value="late">Late</option>
              <option value="half_day">Half Day</option>
              <option value="paid_leave">Paid Leave</option>
              <option value="week_off">Week Off</option>
            </select>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
        </div>

      </form>
    </div>
  </div>
</div>
<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editAttendanceForm" method="POST">
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h5 class="modal-title">Edit Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <div class="mb-3">
            <label>User</label>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Date</label>
            <input type="date" name="date" id="edit_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Check In Time</label>
            <input type="time" name="check_in" id="edit_check_in" class="form-control" step="1">
          </div>
          <div class="mb-3">
            <label>Check Out Time</label>
            <input type="time" name="check_out" id="edit_check_out" class="form-control" step="1">
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="status" id="edit_status" class="form-control">
              <option value="present">Present</option>
              <option value="absent">Absent</option>
              <option value="late">Late</option>
              <option value="half_day">Half Day</option>
              <option value="paid_leave">Paid Leave</option>
              <option value="week_off">Week Off</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>

      </form>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function() {

    const editModal = new bootstrap.Modal(
      document.getElementById('editAttendanceModal')
    );

    document.querySelectorAll('.editAttendanceBtn').forEach(button => {

      button.addEventListener('click', function() {

        document.getElementById('edit_name').value = this.dataset.name;
        document.getElementById('edit_date').value = this.dataset.date;
        document.getElementById('edit_check_in').value = this.dataset.checkIn;
        document.getElementById('edit_check_out').value = this.dataset.checkOut;
        document.getElementById('edit_status').value = this.dataset.status;

        const form = document.getElementById('editAttendanceForm');

        form.action = `/attendance/${this.dataset.id}`;

        editModal.show();
      });

    });

  });
</script>
@endsection