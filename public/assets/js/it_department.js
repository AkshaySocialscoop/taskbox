// Edit User Modal Population
document.addEventListener('DOMContentLoaded', function () {

    const editModal = new bootstrap.Modal(
        document.getElementById('projectDetailsUpdateModal')
    );

    document.querySelectorAll('.editProjectDetailBtn').forEach(button => {
        button.addEventListener('click', function () {

            document.getElementById('edit_id').value           = this.dataset.id;
            document.getElementById('edit_name').value         = this.dataset.name;
            document.getElementById('edit_format').value       = this.dataset.format;
            document.getElementById('edit_link').value         = this.dataset.link;
            document.getElementById('edit_requirement').value  = this.dataset.requirement;
            document.getElementById('edit_comments').value     = this.dataset.comments;
            document.getElementById('edit_status').value       = this.dataset.status;
            const form = document.getElementById('editProjectDetailForm');
            form.action = `/projects/${this.dataset.id}`;

            editModal.show();
        });
    });

});

// Delete Confirmation
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.deleteProjectForm').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: 'This Site will be permanently deleted!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});