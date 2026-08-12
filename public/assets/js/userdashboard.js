document.addEventListener('DOMContentLoaded', function () {

    const taskModal = document.getElementById('taskDetailModal');

    taskModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        // Get data from button
        const taskId = button.getAttribute('data-task-id');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const status = button.getAttribute('data-status');
        const priority = button.getAttribute('data-priority');
        const assigned = button.getAttribute('data-assigned');
        const due = button.getAttribute('data-due');
        const progress = button.getAttribute('data-progress');
        const comment = button.getAttribute('data-comment');

        // Fill modal fields
        document.getElementById('modalTaskId').value = taskId;
        document.getElementById('modalTaskTitle').textContent = title;
        document.getElementById('modalTaskDescription').textContent = description;
        document.getElementById('modalTaskStatus').value = status;
        document.getElementById('modalTaskPriority').textContent = priority;
        document.getElementById('modalTaskAssigned').textContent = assigned;
        document.getElementById('modalTaskDue').textContent = due;
        document.getElementById('modalTaskComment').value = comment || '';

        // Progress
        document.getElementById('modalTaskProgressText').textContent = progress + '%';
        document.getElementById('modalTaskProgressBar').style.width = progress + '%';
        document.getElementById('modalTaskProgressBar').innerText = progress + '%';
    });

}); 
 
 // Post status Update

function toggleStatus(id, currentStatus, badgeEl) {



    if (currentStatus === 'Completed') {

        Swal.fire({

            icon: 'info',

            title: 'Already Completed',

            text: 'This task is already marked as completed.',

            confirmButtonText: 'OK'

        });

        return;

    }



    Swal.fire({

        title: 'Mark as Completed?',

        text: 'Are you sure you want to mark this task as completed?',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonText: 'Yes, complete it!',

        cancelButtonText: 'Cancel',

        reverseButtons: true

    }).then((result) => {



        // ❌ If cancelled → STOP HERE

        if (!result.isConfirmed) return;



        // ✅ Only runs when YES is clicked

        const formData = new FormData();

        formData.append('status', 'completed');



        fetch(`/calendar/${id}/status`, {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content

            },

            body: JSON.stringify({

                status: 'Completed'

            })

        })

        .then(res => {

            if (!res.ok) throw new Error('Request failed');

            return res.json();

        })

        .then(() => {



            // ✅ Update UI

            badgeEl.classList.remove('bg-danger');

            badgeEl.classList.add('bg-success');

            badgeEl.innerText = 'Status - completed';



            // ✅ Update JS memory

            Object.keys(meetings).forEach(date => {

                meetings[date].forEach(m => {

                    if (m.id === id) {

                        m.status = 'completed';

                    }

                });

            });



            Swal.fire({

                icon: 'success',

                title: 'Completed!',

                text: 'Task marked as completed successfully.',

                timer: 1500,

                showConfirmButton: false

            });



        })

        .catch(err => {

            console.error(err);

            Swal.fire({

                icon: 'error',

                title: 'Failed',

                text: 'Failed to update status'

            });

        });

    });

}





// Filter Tasks by Status

document.addEventListener('DOMContentLoaded', function () {



    const buttons = document.querySelectorAll('[data-filter]');

    const tasks = document.querySelectorAll('.task-card');



    buttons.forEach(button => {

        button.addEventListener('click', function () {



            // Active button UI

            buttons.forEach(btn => btn.classList.remove('active'));

            this.classList.add('active');



            const filter = this.dataset.filter;



            tasks.forEach(task => {

                const status = task.dataset.status;



                if (filter === 'all') {

                    task.style.display = '';

                }

                else if (filter === 'pending') {

                    // Pending + In Progress

                    task.style.display =

                        (status === 'Pending' || status === 'In_Progress')

                        ? ''

                        : 'none';

                }

                else if (filter === 'completed') {

                    task.style.display =

                        (status === 'Completed')

                        ? ''

                        : 'none';

                }

            });

        });

    });



});