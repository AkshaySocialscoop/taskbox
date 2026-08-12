 

$(function () {
  "use strict"; 
 

  /* toggle button */

  $(".btn-toggle").click(function () {
    $("body").hasClass("toggled") ? ($("body").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($("body").addClass("toggled"), $(".sidebar-wrapper").hover(function () {
      $("body").addClass("sidebar-hovered")
    }, function () {
      $("body").removeClass("sidebar-hovered")
    }))
  })

 

  /* menu */

  $(function () {
    $('#sidenav').metisMenu();
  });

  $(".sidebar-close").on("click", function () {
    $("body").removeClass("toggled")
  })
 
  /* dark mode button */

  $(".dark-mode i").click(function () {
    $(this).text(function (i, v) {
      return v === 'dark_mode' ? 'light_mode' : 'dark_mode'
    })
  });


  $(".dark-mode").click(function () {
    $("html").attr("data-bs-theme", function (i, v) {
      return v === 'dark' ? 'light' : 'dark';
    })
  })



  /* switcher */

  $("#LightTheme").on("click", function () {
    $("html").attr("data-bs-theme", "light")
  }),

    $("#DarkTheme").on("click", function () {
      $("html").attr("data-bs-theme", "dark")
    }),

    $("#SemiDarkTheme").on("click", function () {
      $("html").attr("data-bs-theme", "semi-dark")
    }),

    $("#BoderedTheme").on("click", function () {
      $("html").attr("data-bs-theme", "bodered-theme")
    })



  /* search control */

  $(".search-control").click(function () {
    $(".search-popup").addClass("d-block");
    $(".search-close").addClass("d-block");
  });


  $(".search-close").click(function () {
    $(".search-popup").removeClass("d-block");
    $(".search-close").removeClass("d-block");
  });


  $(".mobile-search-btn").click(function () {
    $(".search-popup").addClass("d-block");
  });


  $(".mobile-search-close").click(function () {
    $(".search-popup").removeClass("d-block");
  });
 
  /* menu active */

  $(function () {
    for (var e = window.location, o = $(".metismenu li a").filter(function () {
        

      return this.href == e
    }).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
  });
 

});


document.addEventListener('DOMContentLoaded', function () {

    const successMsg = document.querySelector('meta[name="swal-success"]');
    const errorMsg   = document.querySelector('meta[name="swal-error"]');

    if (successMsg) {
        Swal.fire({
            icon: 'success',
            title: 'Success',
            text: successMsg.getAttribute('content'),
            timer: 2000,
            showConfirmButton: false
        });
    }

    if (errorMsg) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMsg.getAttribute('content'),
        });
    }

    
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "This user will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});

// Edit User Modal Population
document.addEventListener('DOMContentLoaded', function () {

    const editModal = new bootstrap.Modal(
        document.getElementById('editUserModal')
    );

    document.querySelectorAll('.editUserBtn').forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_name').value  = this.dataset.name;
            document.getElementById('edit_email').value = this.dataset.email;
            document.getElementById('edit_role').value  = this.dataset.role;
            document.getElementById('edit_department').value  = this.dataset.department;

            const form = document.getElementById('editUserForm');
            form.action = `/super-admin/users/${this.dataset.id}`;

            editModal.show();
        });
    });

});
// Edit Roles Modal  
document.addEventListener('DOMContentLoaded', function () {

    const editModal = new bootstrap.Modal(
        document.getElementById('editRoleModal')
    );

    document.querySelectorAll('.editRoleBtn').forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_name').value  = this.dataset.name; 

            const form = document.getElementById('editRoleForm');
            form.action = `roles/${this.dataset.id}`;

            editModal.show();
        });
    });

});
// Edit Departments Modal  
document.addEventListener('DOMContentLoaded', function () {

    const editModal = new bootstrap.Modal(
        document.getElementById('editDepartmentModal')
    );

    document.querySelectorAll('.editDepartmentBtn').forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_name').value  = this.dataset.name; 

            const form = document.getElementById('editDepartmentForm');
            form.action = `departments/${this.dataset.id}`;

            editModal.show();
        });
    });

});
// Edit Shift Modal  
document.addEventListener('DOMContentLoaded', function () {

    const editModal = new bootstrap.Modal(
        document.getElementById('editShiftModal')
    );

    document.querySelectorAll('.editShiftBtn').forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_name').value  = this.dataset.name; 
            document.getElementById('edit_start_time').value = this.dataset.startTime;
            document.getElementById('edit_end_time').value = this.dataset.endTime;

            const form = document.getElementById('editShiftForm');
            form.action = `shifts/${this.dataset.id}`;

            editModal.show();
        });
    });

});
// Edit Attendance Modal  

// Logout Functionality
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.logout-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Logout?',
                text: 'Are you sure you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, logout'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });
    });

});



// calender and todo task js start here

(function(){
        const userId = document.querySelector('#todo-form').dataset.userId || 'guest';
        const storageKey = 'tasks_user_' + userId;

        // State
        let tasks = JSON.parse(localStorage.getItem(storageKey) || '[]');
        let viewYear = (new Date()).getFullYear();
        let viewMonth = (new Date()).getMonth(); // 0-indexed
        let activeFilterDate = null; // ISO date string YYYY-MM-DD or null

        // Elements
        const form = document.getElementById('todo-form');
        const titleInput = document.getElementById('todo-title');
        const dateInput = document.getElementById('todo-date');
        const priorityInput = document.getElementById('todo-priority');
        const listEl = document.getElementById('todo-list');
        const countEl = document.getElementById('todo-count');
        const clearDoneBtn = document.getElementById('clear-done');
        const clearAllBtn = document.getElementById('clear-all');

        const calTitle = document.getElementById('calendar-title');
        const calGrid = document.getElementById('calendar-grid');
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');

        // Helpers
        function save() {
            localStorage.setItem(storageKey, JSON.stringify(tasks));
            renderAll();
        }

        function formatDateIso(d) {
            const y = d.getFullYear();
            const m = String(d.getMonth()+1).padStart(2,'0');
            const day = String(d.getDate()).padStart(2,'0');
            return `${y}-${m}-${day}`;
        }

        function humanDate(iso) {
            if(!iso) return '';
            const d = new Date(iso + 'T00:00:00');
            return d.toLocaleDateString();
        }

        function addTask(title, date, priority='normal') {
            tasks.push({
                id: Date.now().toString(36),
                title: title,
                date: date || null,
                priority: priority || 'normal',
                done: false,
                created_at: new Date().toISOString()
            });
            save();
        }

        function toggleDone(id) {
            const t = tasks.find(x => x.id === id);
            if(t) { t.done = !t.done; save(); }
        }

        function removeTask(id) {
            tasks = tasks.filter(x => x.id !== id);
            save();
        }

        function clearDone() {
            tasks = tasks.filter(t => !t.done);
            save();
        }

        function clearAll() {
            tasks = [];
            save();
        }

        // Rendering
        function renderList() {
            listEl.innerHTML = '';
            let filtered = tasks.slice().sort((a,b) => {
                if(a.done !== b.done) return a.done?1:-1;
                if(a.date && b.date) return a.date.localeCompare(b.date);
                if(a.date) return -1;
                if(b.date) return 1;
                return a.created_at.localeCompare(b.created_at);
            });

            if(activeFilterDate) {
                filtered = filtered.filter(t => t.date === activeFilterDate);
            }

            if(filtered.length === 0) {
                const li = document.createElement('li');
                li.className = 'list-group-item text-muted';
                li.textContent = activeFilterDate ? 'No tasks for this date.' : 'No tasks yet.';
                listEl.appendChild(li);
                countEl.textContent = `${tasks.length} tasks`;
                return;
            }

            filtered.forEach(t => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex align-items-start gap-2';
                li.style.wordBreak = 'break-word';

                const left = document.createElement('div');
                left.className = 'form-check me-2';
                left.innerHTML = `
                    <input class="form-check-input mt-1" type="checkbox" ${t.done?'checked':''} id="ck-${t.id}">
                `;
                const body = document.createElement('div');
                body.className = 'flex-grow-1';

                const title = document.createElement('div');
                title.className = 'd-flex justify-content-between align-items-start';
                title.innerHTML = `<div>
                    <div class="${t.done ? 'text-decoration-line-through text-muted' : 'fw-semibold'}">${escapeHtml(t.title)}</div>
                    <div class="small text-muted">${t.date?humanDate(t.date):'No date'} • ${escapeHtml(t.priority)}</div>
                </div>
                <div class="ms-2 text-end">
                    <button class="btn btn-sm btn-outline-danger btn-remove" data-id="${t.id}" title="Delete">✕</button>
                </div>`;

                body.appendChild(title);
                li.appendChild(left);
                li.appendChild(body);
                listEl.appendChild(li);

                // events
                left.querySelector('input').addEventListener('change', () => toggleDone(t.id));
                li.querySelector('.btn-remove').addEventListener('click', () => removeTask(t.id));
            });

            countEl.textContent = `${tasks.length} tasks`;
        }

        function renderCalendar() {
            calGrid.innerHTML = '';
            const firstDay = new Date(viewYear, viewMonth, 1);
            const lastDay = new Date(viewYear, viewMonth + 1, 0);
            const startWeekday = firstDay.getDay();
            const daysInMonth = lastDay.getDate();

            const monthName = firstDay.toLocaleString(undefined, { month: 'long' });
            calTitle.textContent = `${monthName} ${viewYear}`;

            // fill blanks for previous month
            for(let i=0;i<startWeekday;i++){
                const cell = document.createElement('div');
                cell.className = 'p-2 border rounded-2 bg-light text-muted';
                cell.style.minHeight = '76px';
                calGrid.appendChild(cell);
            }

            for(let d=1; d<=daysInMonth; d++){
                const iso = `${viewYear}-${String(viewMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                const cell = document.createElement('div');
                cell.className = 'p-2 border rounded-2 position-relative';
                cell.style.minHeight = '76px';
                cell.style.cursor = 'pointer';

                const dayNum = document.createElement('div');
                dayNum.className = 'small fw-semibold';
                dayNum.textContent = d;
                cell.appendChild(dayNum);

                const dayTasks = tasks.filter(t => t.date === iso);
                if(dayTasks.length){
                    const wrapper = document.createElement('div');
                    wrapper.className = 'mt-1 d-flex flex-column gap-1';
                    dayTasks.slice(0,3).forEach(t => {
                        const badge = document.createElement('div');
                        badge.className = 'small text-truncate rounded px-1';
                        badge.style.background = t.done ? '#e9ecef' : (t.priority === 'high' ? '#fde2e2' : (t.priority === 'low' ? '#eef7ff' : '#f6f6f6'));
                        badge.style.fontSize = '0.75rem';
                        badge.textContent = (t.done ? '✓ ' : '') + t.title;
                        wrapper.appendChild(badge);
                    });
                    if(dayTasks.length > 3){
                        const more = document.createElement('div');
                        more.className = 'small text-muted';
                        more.textContent = `+${dayTasks.length-3} more`;
                        wrapper.appendChild(more);
                    }
                    cell.appendChild(wrapper);
                }

                cell.addEventListener('click', () => {
                    activeFilterDate = iso;
                    renderAll();
                    // scroll list into view
                    document.getElementById('todo-list').scrollIntoView({behavior:'smooth', block:'start'});
                });

                calGrid.appendChild(cell);
            }
        }

        function renderAll() {
            renderList();
            renderCalendar();
        }

        // Utilities
        function escapeHtml(unsafe) {
            return String(unsafe)
                .replaceAll('&','&amp;')
                .replaceAll('<','&lt;')
                .replaceAll('>','&gt;')
                .replaceAll('"','&quot;')
                .replaceAll("'",'&#039;');
        }

        // Events
        form.addEventListener('submit', function(e){
            e.preventDefault();
            const title = titleInput.value.trim();
            if(!title) return;
            const date = dateInput.value ? dateInput.value : null;
            const priority = priorityInput.value || 'normal';
            addTask(title, date, priority);
            titleInput.value = '';
            dateInput.value = '';
            priorityInput.value = 'normal';
        });

        clearDoneBtn.addEventListener('click', function(){
            if(!confirm('Remove all tasks marked as done?')) return;
            clearDone();
        });

        clearAllBtn.addEventListener('click', function(){
            if(!confirm('Remove all tasks? This cannot be undone.')) return;
            clearAll();
        });

        prevBtn.addEventListener('click', function(){
            viewMonth--;
            if(viewMonth < 0) { viewMonth = 11; viewYear--; }
            renderCalendar();
        });

        nextBtn.addEventListener('click', function(){
            viewMonth++;
            if(viewMonth > 11) { viewMonth = 0; viewYear++; }
            renderCalendar();
        });

        // Initialize: populate with a few sample tasks if empty (only for first-time users)
        if(tasks.length === 0 && !localStorage.getItem(storageKey + '_seeded')) {
            const today = new Date();
            addTask('Welcome — create your first task', formatDateIso(today), 'normal');
            const tmr = new Date(today); tmr.setDate(tmr.getDate()+2);
            addTask('Try assigning a date', formatDateIso(tmr), 'high');
            localStorage.setItem(storageKey + '_seeded','1');
        } else {
            renderAll();
        }

        // expose small helper to clear filter
        const clearFilterBtn = document.createElement('button');
        clearFilterBtn.className = 'btn btn-sm btn-outline-secondary ms-2';
        clearFilterBtn.textContent = 'Show all';
        clearFilterBtn.addEventListener('click', function(){
            activeFilterDate = null;
            renderAll();
        });
        document.querySelector('#calendar').querySelector('.card-body')?.appendChild(clearFilterBtn);
        // fallback append near calendar title
        if(!document.querySelector('#calendar').contains(clearFilterBtn)){
            document.getElementById('calendar-title').after(clearFilterBtn);
        }
    })();




