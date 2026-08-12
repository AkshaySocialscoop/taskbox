 document.addEventListener('click', function (e) {

    const closeBtn = e.target.closest('.notify-close');
    if (!closeBtn) return;

    e.preventDefault();

    const notificationId = closeBtn.dataset.id;

    fetch(`/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Remove notification from UI
            closeBtn.closest('.dropdown-item').remove();

            // Update badge count
            const badge = document.querySelector('.badge-notify');
            if (badge) {
                let count = parseInt(badge.innerText) - 1;
                if (count <= 0) {
                    badge.remove();
                } else {
                    badge.innerText = count;
                }
            }
        }
    });
}); 