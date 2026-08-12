@extends('layout.index')

@section('title', 'Dashboard')

@section('content') 
<style>
    
    .employee-header th {
        font-size: 11px;
        text-align: center;
        padding: 0px 0;
        height: 24px !important;
        line-height: 15px;
        vertical-align: middle !important;
    }

    .employee-data td:first-child {
        font-size: 11px;
        text-align: center;
    }

    .calendar-table {
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }

    .calendar-table th,
    .calendar-table td {
        width: 28px;
        height: 24px !important;
        padding: 0 3px;
        line-height: 1.1;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .calendar-table th:first-child,
    .calendar-table td:first-child {
        width: 150px;
        min-width: 150px;
        text-align: left;
        padding-left: 8px;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 2;
    }

    .employee-header {
        background: #0d6efd;
        color: #fff;
        font-weight: bold;
    }

    .past-date {
        background: #e9ecef !important;
        color: #999;
    }

    .past-present {
        background: darkgreen !important;
        color: #fff;
    }

    .past-absent {
        background: darkred !important;
        color: #fff;
    }

    .past-halfday {
        background: #856404 !important;
        color: #fff;
    }

    .past-holiday {
        background: darkblue !important;
        color: #fff;
    }

    .past-overtime {
        background: #4b0082 !important;
        color: #fff;
    }

    .past-weekly_off {
        background: #6c757d !important;
        color: #fff;
    }

    .past-not_marked {
        background: #f8f9fa !important;
        color: #999;
    }

    .bg-present {
        background: #28a745 !important;
        color: #fff;
    }

    .bg-absent {
        background: #dc3545 !important;
        color: #fff;
    }

    .bg-halfday {
        background: #28a745 !important;
        color: #000;
    }
    .bg-late {
        background: #28a745 !important;   /* Orange */
        color: #fff;
    }
    .bg-holiday {
        background: #28a745 !important;   /* Teal */
    color: #fff;
    }

    .bg-overtime {
        background: #28a745 !important;
        color: #fff;
    }
    .bg-paid_leave {
    background: #0d6efd !important;   /* Blue */
    color: #fff;
}
    .bg-weekly_off {
        background: #6c757d !important;
        color: #fff;
    }

    .bg-not_joined {
        background: #f8f9fa !important;
        color: #6c757d;
    }

    .past-not_joined {
        background: #e9ecef !important;
        color: #6c757d;
    }

    .calendar-table td {
        border: 1px solid #d6dbdf;
        transition: 0.2s;
    }

    .legend-box {
        width: 18px;
        height: 12px;
        border-radius: 2px;
        display: inline-block;
    }
</style>
<!-- tabs start -->
<div class="row row-cols-1 row-cols-md-4 g-3">
 <!-- PRESENT -->
<div class="col">
    <div class="card rounded-4" style="border-bottom:4px solid #10b981;">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <div class="text-dark">Present</div>
                <div class="fw-bold text-success" style="font-size:2rem;" id="countPresent">0</div>
            </div>

            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                <i class="fas fa-user-check text-success"></i>
            </div>
        </div>
    </div>
</div>

<!-- ABSENT -->
<div class="col">
    <div class="card rounded-4" style="border-bottom:4px solid #ff0404;">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <div class="text-dark">Absent</div>
                <div class="fw-bold" style="font-size:2rem;color:#ff0404;" id="countAbsent">0</div>
            </div>

            <div class="stat-icon" style="background: rgba(253, 4, 4, 0.1);">
                <i class="fas fa-user-times" style="color:#ff0404;"></i>
            </div>
        </div>
    </div>
</div>

<!-- HALF DAY -->
<div class="col">
    <div class="card rounded-4" style="border-bottom:4px solid #ffc107;">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <div class="text-dark">Half Day</div>
                <div class="fw-bold" style="font-size:2rem;color:#ffc107;" id="countHalfDay">0</div>
            </div>

            <div class="stat-icon" style="background: rgba(253, 216, 4, 0.15);">
                <i class="fas fa-user-clock" style="color:#ffc107;"></i>
            </div>
        </div>
    </div>
</div>

<!-- INCOME -->
<div class="col">
    <div class="card rounded-4" style="border-bottom:4px solid #ff8800;">
        <div class="card-body d-flex align-items-center justify-content-between">
            <div>
                <div class="text-dark">Income</div>
                <div class="fw-bold" style="font-size:2rem;color:#ff8800;" id="countIncome">0</div>
            </div>

            <div class="stat-icon" style="background: rgba(255, 136, 0, 0.1);">
                <i class="fas fa-indian-rupee-sign" style="color:#ff8800;"></i>
            </div>
        </div>
    </div>
</div>
</div>

<!-- tabs end -->
<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            <h4>Employee Attendance Chart</h4>
        <p class="text-muted mb-0" style="font-size:12px;">Plz Click on an employee to view their attendance details</p>
        </div>
        <div class="d-flex" style="gap:10px;">
            <select id="yearSelect" class="form-control w-auto"></select>
            <select id="monthSelect" class="form-control">
                <option value="1">Jan</option>
                <option value="2">Feb</option>
                <option value="3">Mar</option>
                <option value="4">Apr</option>
                <option value="5">May</option>
                <option value="6">Jun</option>
                <option value="7">Jul</option>
                <option value="8">Aug</option>
                <option value="9">Sep</option>
                <option value="10">Oct</option>
                <option value="11">Nov</option>
                <option value="12">Dec</option>
            </select>
        </div>
    </div>


  
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered calendar-table">
                <thead>
                    <tr id="dateRow" class="employee-header">
                        <th>Date's</th>
                    </tr>
                    <tr id="dayRow" class="employee-header">
                        <th>Name</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($employees as $employee)
                    <tr style="cursor:pointer;" class="employee-data employee-name" data-id="{{ $employee->id }}" data-employee="{{ $employee->id }}" data-joining="{{ \Carbon\Carbon::parse($employee->created_at)->format('Y-m-d') }}">
                        <td>{{ $employee->name }} </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- color assign -->
        <div class="container">
            <div class="d-flex align-items-center justify-content-center flex-wrap" style="gap: 9px;">

                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-present" style="border:1px solid black"></span>
                    Present
                </div>

                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-absent" style="border:1px solid black"></span>
                    Absent
                </div>
                <!-- 
                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-halfday" style="border:1px solid black"></span>
                    Half Day
                </div>

                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-holiday" style="border:1px solid black"></span>
                    Holiday
                </div>

                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-overtime" style="border:1px solid black"></span>
                    Overtime
                </div>

                <div class="d-flex align-items-center" style="gap: 3px;">
                    <span class="legend-box bg-weekly_off" style="border:1px solid black"></span>
                    Weekly Off
                </div>  -->
            </div>
        </div>

    </div>  
{{-- ===== PASS DATA ===== --}}
<script> 
const attendanceData = @json($attendances);
    const currentYear = {{ $year ?? now()->year }};
    const currentMonth = {{ $month ?? now()->month }}; 
</script>

<script>
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const yearSelect = document.getElementById('yearSelect');
const monthSelect = document.getElementById('monthSelect');

// ================= INIT =================
populateYears();

yearSelect.value = currentYear;
monthSelect.value = currentMonth;

yearSelect.addEventListener('change', redirectToNewMonth);
monthSelect.addEventListener('change', redirectToNewMonth);

generateCalendar();

// ================= FUNCTIONS =================

function populateYears() {
    let current = new Date().getFullYear();
    for (let y = current - 5; y <= current + 5; y++) {
        yearSelect.add(new Option(y, y));
    }
} 
function generateCalendar() {
    const year = parseInt(yearSelect.value);
    const month = parseInt(monthSelect.value);

    const dateRow = document.getElementById('dateRow');
    const dayRow = document.getElementById('dayRow');

    dateRow.innerHTML = '<th>Employee</th>';
    dayRow.innerHTML = '<th>Name</th>';

    let days = new Date(year, month, 0).getDate();

    for (let d = 1; d <= days; d++) {
        let date = new Date(year, month - 1, d);

        dateRow.innerHTML += `<th>${d}</th>`;
        dayRow.innerHTML += `<th>${dayNames[date.getDay()]}</th>`;
    }

    updateRows(days, year, month);
}
function updateRows(days, year, month) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    document.querySelectorAll('.employee-data').forEach(row => {
        let employeeId = row.dataset.employee;
        let joiningDate = new Date(row.dataset.joining);

        joiningDate.setHours(0, 0, 0, 0);

        let nameCell = row.querySelector('td').textContent;

        row.innerHTML = `<td>${nameCell}</td>`;

        for (let d = 1; d <= days; d++) {

            let dateStr = `${year}-${String(month).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            let currentDate = new Date(dateStr);
            currentDate.setHours(0, 0, 0, 0);

            let attendance = getAttendanceStatus(employeeId, dateStr);
            let status = attendance.status;
            let className = '';

            // ✅ BEFORE JOINING (created_at)
            if (currentDate < joiningDate) {
                status = 'not_joined';
            }
            // ✅ FUTURE DATE
            else if (currentDate > today) {
                status = 'future';
            }

            // ✅ CLASS LOGIC
            if (status === 'future') {
                className = ''; // blank
            } 
            else if (status === 'not_joined') {
                className = 'bg-not_joined'; // black
            }
            else if (status === 'absent') {
                className = 'bg-absent'; // always red
            }
            else if (status === 'half_day') {
                className = 'bg-halfday'; // always red
            }
            else if (status === 'paid_leave') {
                className = 'bg-paid_leave'; // always red
            }
            else if (status === 'week_off') {
                className = 'bg-weekly_off'; // always red
            }
            else if (status === 'overtime') {
                className = 'bg-overtime'; // always red
            }
            else {
                className = `bg-${status}`; // present, halfday, etc.
            }

            const tooltipParts = [];
            const formattedStatus = status.replace('_', ' ');

            if (['present', 'late', 'half_day'].includes(attendance.status) && attendance.check_in) {
                tooltipParts.push(`Check In: ${attendance.check_in}`);
            }

            if (['present', 'late', 'half_day'].includes(attendance.status) && attendance.check_out) {
                tooltipParts.push(`Check Out: ${attendance.check_out}`);
            }

            const titleText = tooltipParts.length > 0
                ? `${formattedStatus} (${tooltipParts.join(' • ')})`
                : formattedStatus;

            // ✅ NO TEXT (only color boxes)
            row.innerHTML += `
                <td class="${className}" title="${titleText}"></td>
            `;
        }
    });
}

// Redirect on change
function redirectToNewMonth() {
    const url = new URL(window.location);
    url.searchParams.set('year', yearSelect.value);
    url.searchParams.set('month', monthSelect.value);
    window.location.href = url;
} 

function getAttendanceStatus(employeeId, date) {

    // ❗ If employee has no records at all → Absent
    if (!attendanceData[employeeId]) {
        return {
            status: 'absent',
            check_in: null,
            check_out: null
        };
    }

    // Find record for this date
    let record = attendanceData[employeeId].find(item => item.formatted_date === date);

    // ❗ If no entry for that date → Absent
    if (!record) {
        return {
            status: 'absent',
            check_in: null,
            check_out: null
        };
    }

    return {
        status: record.status.toLowerCase(),
        check_in: record.check_in || null,
        check_out: record.check_out || null
    };
}
</script>
<script>

document.addEventListener('DOMContentLoaded', function () {

    // Default all counts
    updateEmployeeStats(null);

    // Click event on employee name
    document.querySelectorAll('.employee-name').forEach(item => {

        item.addEventListener('click', function () {

            // active effect
            document.querySelectorAll('.employee-name').forEach(el => {
                el.style.color = '';
            });

            this.style.color = '#0d6efd';

            let employeeId = this.dataset.id;

            updateEmployeeStats(employeeId);
        });

    });

});


document.addEventListener('DOMContentLoaded', function () {

    // default reset
    updateEmployeeStats(null);

    // click employee name
    document.querySelectorAll('.employee-name').forEach(item => {

        item.addEventListener('click', function () {

            // active color
            document.querySelectorAll('.employee-name').forEach(el => {
                el.style.color = '';
            });

            this.style.color = '#0d6efd';

            let employeeId = this.dataset.id;

            updateEmployeeStats(employeeId);
        });

    });

});

function updateEmployeeStats(employeeId) {

    let present = 0;
    let absent = 0;
    let halfday = 0;
    let ignored = 0;

    // no employee selected
    if (!employeeId) {

        document.getElementById('countPresent').innerText = 0;
        document.getElementById('countAbsent').innerText = 0;
        document.getElementById('countHalfDay').innerText = 0;

        return;
    }

    const selectedYear = parseInt(yearSelect.value);
    const selectedMonth = parseInt(monthSelect.value);

    const today = new Date();

    let totalDays;

    // current month → count till today
    if (
        selectedYear === today.getFullYear() &&
        selectedMonth === (today.getMonth() + 1)
    ) {
        totalDays = today.getDate();
    }
    else {

        // old month → full month
        totalDays = new Date(selectedYear, selectedMonth, 0).getDate();
    }

    let ignoreStatuses = [
        'holiday',
        'week_off',
        'paid_leave',
        'overtime'
    ];

    // attendance records
    (attendanceData[employeeId] || []).forEach(record => {

        let status = record.status.toLowerCase();

        let recordDate = new Date(record.formatted_date);

        // only selected month/year
        if (
            recordDate.getFullYear() !== selectedYear ||
            (recordDate.getMonth() + 1) !== selectedMonth
        ) {
            return;
        }

        if (status === 'present' || status === 'late' || status === 'half_day') {
            present++;
        }
        else if (status === 'half_day') {
            halfday++;
        }
        else if (ignoreStatuses.includes(status)) {
            ignored++;
        }

    });

    // calculate absent
    absent = totalDays - (present + halfday + ignored);

    if (absent < 0) {
        absent = 0;
    }

    // update UI
    document.getElementById('countPresent').innerText = present;
    document.getElementById('countAbsent').innerText = absent;
    document.getElementById('countHalfDay').innerText = halfday;
}

</script>
@endsection