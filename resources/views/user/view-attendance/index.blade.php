@extends('layout.index')

@section('title', 'View Attendance')

@section('content')

<style>

body{
    background:#f5f5f5;
}
.main-wrapper .main-content {
    padding: 0rem !important;
}
.attendance-wrapper{
    padding:20px;
}

.attendance-card{
    background:#fff;
    border-radius:30px;
    padding:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

.calendar-header{
    display:flex;
    flex-direction: row;
    justify-content:space-between;
    align-items:center;
    margin-bottom:30px;
}

.calendar-header h2{
    font-size:34px;
    font-weight:700;
    margin:0;
}

.nav-btn{
    border:none;
    background:none;
    font-size:30px;
    color:#0d6efd;
    cursor:pointer;
}

.calendar-days{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    text-align:center;
    font-weight:700;
    margin-bottom:20px;
    color:#555;
}

.calendar-dates{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:15px;
}

.day-box{
    min-height:110px;
    text-align:center;
}

.day-circle{
    width:55px;
    height:55px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:auto;
    font-size:22px;
    font-weight:700;
    color:#fff;
}

.present{
    background:#1f8f3d;
}

.absent{
    background:#ff0019;
}

.paid_leave{
    background:#6f2cff;
}

.future{
    border:2px solid #7d7dff;
    color:#7d7dff;
    background:#fff;
}

.time-text{
    margin-top:8px;
    font-size:13px;
    line-height:18px;
    color:#555;
    font-weight:600;
}

.abs-text{
    margin-top:8px;
    color:#ff0019;
    font-weight:700;
    font-size:16px;
}

.legend{
    margin-top:40px;
    border-top:1px solid #ddd;
    padding-top:20px;
    display:flex;
    gap:25px;
    flex-wrap:wrap;
}

.legend div{
    display:flex;
    align-items:center;
    gap:10px;
    font-weight:600;
}

.legend-box{
    width:28px;
    height:28px;
    border-radius:8px;
}

@media(max-width:768px){

    .calendar-header h2{
        font-size:24px;
    }

    .calendar-dates{
        gap:10px;
    }

    .day-box{
        min-height:90px;
    }

    .day-circle{
        width:35px;
        height:35px;
        font-size:14px;
    }
    .abs-text{ 
        font-size:12px;
    }


    .time-text{
        font-size:9px;
        line-height:15px;
    }
}

</style>


<div class="attendance-wrapper">

    <div class="attendance-card">

        <div class="calendar-header">

            <button id="prevMonth" class="nav-btn">
                ❮
            </button>

            <h2 id="monthYear"></h2>

            <button id="nextMonth" class="nav-btn">
                ❯
            </button>

        </div>

        <div class="calendar-days">
            <div>S</div>
            <div>M</div>
            <div>T</div>
            <div>W</div>
            <div>T</div>
            <div>F</div>
            <div>S</div>
        </div>

        <div id="calendarDates" class="calendar-dates"></div>

        <div class="legend">

            <div>
                <span class="legend-box present"></span>
                Present
            </div>

            <div>
                <span class="legend-box absent"></span>
                Absent
            </div>

            <div>
                <span class="legend-box paid_leave"></span>
                Leave
            </div>

        </div>

    </div>

</div>


<script>

const attendanceData = @json($events);

let currentDate = new Date();

function renderCalendar(){

    const monthYear = document.getElementById('monthYear');
    const calendarDates = document.getElementById('calendarDates');

    calendarDates.innerHTML = '';

    let year = currentDate.getFullYear();
    let month = currentDate.getMonth();

    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    monthYear.innerHTML = currentDate.toLocaleString('default',{
        month:'long',
        year:'numeric'
    });

    for(let i=0; i<firstDay; i++){
        calendarDates.innerHTML += `<div></div>`;
    }

    for(let day=1; day<=totalDays; day++){

        let fullDate =
            year + '-' +
            String(month + 1).padStart(2,'0') + '-' +
            String(day).padStart(2,'0');

        let attendance = attendanceData.find(e => e.start === fullDate);

        let statusClass = 'future';
        let content = '';

        if(attendance){

            statusClass = attendance.classNames[0];

            if(statusClass === 'late'){
                statusClass = 'present';
            }

            if(statusClass === 'half_day'){ 
                statusClass = 'present';
            }

            if(statusClass === 'paid_leave'){
                statusClass = 'present';
            }

            if(statusClass === 'absent'){

                content = `
                    <div class="abs-text">
                        ABS
                    </div>
                `;

            }else{

                content = `
                    <div class="time-text">
                        ${attendance.title}
                    </div>
                `;
            }
        }

        calendarDates.innerHTML += `
            <div class="day-box">

                <div class="day-circle ${statusClass}">
                    ${day}
                </div>

                ${content}

            </div>
        `;
    }
}

renderCalendar();

document.getElementById('prevMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
};

document.getElementById('nextMonth').onclick = () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
};

</script>

@endsection