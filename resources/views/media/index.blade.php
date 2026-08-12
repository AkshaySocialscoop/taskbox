@extends('layout.index')
@section('title', 'Creator Dashboard')
@section('content')
<style>
.creator-card {
    border: none;
    border-radius: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.creator-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.nav-pills .nav-link {
    border-radius: 20px;
    color: #666;
    font-weight: 500;
    padding: 8px 20px;
}

.nav-pills .nav-link.active {
    background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
}

.btn-gradient {
    background: linear-gradient(45deg, #405de6, #5851db);
    color: white;
    border: none;
    border-radius: 10px;
    transition: opacity 0.3s;
}

.btn-gradient:hover {
    color: white;
    opacity: 0.9;
}

.stat-badge {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 2px 8px;
}

.reel-container {
    position: relative;
    width: 100%;
    max-width: 350px;
    /* Standard phone width */
    margin: 0 auto;
    aspect-ratio: 9/16;
    background: #000;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.reel-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}

/* Overlay for icons and info */
.reel-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 20px 15px;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
    color: white;
    pointer-events: none;
    /* Allows clicking the video through the text */
}

.reel-actions {
    position: absolute;
    right: 10px;
    bottom: 100px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    align-items: center;
    z-index: 10;
}

.action-item {
    color: white;
    text-align: center;
    text-decoration: none;
    font-size: 0.8rem;
}

.action-item i {
    font-size: 1.5rem;
    display: block;
    margin-bottom: 2px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
}

.caption-text {
    display: inline;
    cursor: pointer;
}

/* This class limits text to one line and adds "..." */
.text-truncate-custom {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 85%;
}

.caption-more {
    color: #b0b0b0;
    font-weight: bold;
    margin-left: 5px;
}

.modal-backdrop.show {
    opacity: 0.6;
}

body #editPostModal {
    backdrop-filter: blur(4px);
    background-color: #00000042;
}

#editPostModal .container,
#editPostModal .card,
#editPostModal table {
    filter: blur(2px);
    opacity: 0.6;
}

.insta-preview-box {
    width: 100%;
    aspect-ratio: 4/5;
    /* Instagram post ratio */
    overflow: hidden;
    border-radius: 12px;
    background: #000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.insta-preview-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    /* 🔥 This creates crop effect */
}
</style>
<div class="container-fluid py-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 px-3">
        <div>
            <ul class="nav nav-pills justify-content-center align-items-center" id="instaTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#photos">
                        <i class="fas fa-th me-2"></i>Image's </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#videos">
                        <i class="fas fa-play-circle me-2"></i>Video's </button>
                </li>
            </ul>
        </div>
        <div class="d-flex gap-2   mt-md-0">
            <button class="btn pi-indigo px-3 py-2 btn-sm  rounded-pill text-white" data-bs-toggle="modal"
                data-bs-target="#schedulelistmodal">
                <i class="fas fa-plus-circle me-1"></i> Schedule List </button>
        </div>
        <div class="d-flex gap-2   mt-md-0">
            <button class="btn btn-sm pi-indigo  text-white py-2 px-4 rounded-pill" data-bs-toggle="modal"
                data-bs-target="#instareelandpostuploadModal">
                <i class="fas fa-plus-circle me-1"></i> Upload </button>
        </div>
    </div>
    <div class="tab-content">
        <div class="tab-pane fade show active" id="photos" role="tabpanel">
            <div class="row g-4"> @foreach($media as $index => $mediaItem) @if(isset($mediaItem['type']) &&
                ($mediaItem['type'] == 'image' || $mediaItem['type'] == 'CAROUSEL_ALBUM')) <div
                    class="col-sm-6 col-md-4 col-lg-3">
                    <div class="position-relative border rounded-4">
                        <img src="{{ asset('storage/' . $mediaItem['file_path']) }}" data-index="{{ $index }}"
                            class="card-img-top rounded-4 preview-image" style="aspect-ratio: 1/1; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 m-2">
                            <span
                                class="stat-badge small shadow-sm {{ !$mediaItem->scheduledPost ? 'open-schedule-modal cursor-pointer' : '' }}"
                                data-id="{{ $mediaItem->id }}"
                                data-image="{{ asset('storage/' . $mediaItem['file_path']) }}">
                                @if($mediaItem->scheduledPost)
                                {{ \Carbon\Carbon::parse($mediaItem->scheduledPost->scheduled_at)->format('d l Y h:i A') }}
                                @else Schedule @endif </span>
                        </div>
                        <div class="position-absolute top-0 end-0 m-2"> @if(!$mediaItem->scheduledPost ||
                            $mediaItem->scheduledPost->is_posted == 1) <button
                                class="btn stat-badge small shadow-sm text-danger p-1 delete-media"
                                data-id="{{ $mediaItem->id }}">
                                <i class="lni lni-trash fs-6"></i>
                            </button> @endif </div>
                    </div>
                </div> @endif @endforeach </div>
        </div>
        <div class="tab-pane fade" id="videos">
            <div class="row g-4"> @foreach($media as $post)
                @if(isset($post['type']) && $post['type'] == 'video')

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="reel-container">

                        <video class="reel-video" loop muted playsinline>
                            <source src="{{ asset('storage/' . $post['file_path']) }}" type="video/mp4">
                        </video>

                        <div class="position-absolute bottom-0 start-0 m-2">
                            <span
                                class="stat-badge small shadow-sm {{ !$post->scheduledPost ? 'open-schedule-modal cursor-pointer' : '' }}"
                                data-id="{{ $post->id }}"
                                data-url="{{ asset('storage/' . $post['file_path']) }}"
                                data-type="{{ $post->type }}"
                            >

                                @if($post->scheduledPost)
                                {{ \Carbon\Carbon::parse($post->scheduledPost->scheduled_at)->format('d l Y h:i A') }}
                                @else
                                Schedule
                                @endif

                            </span>
                        </div>

                        <div class="position-absolute top-0 end-0 m-2">
                            @if(!$post->scheduledPost || $post->scheduledPost->is_posted == 1)
                            <button class="btn stat-badge small shadow-sm text-danger p-1 delete-media"
                                data-id="{{ $post->id }}">
                                <i class="lni lni-trash fs-6"></i>
                            </button>
                            @endif
                        </div>

                    </div>
                </div>

                @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
<!--Schedule Modal-->
<div class="modal fade" id="scheduleModal">
    <div class="modal-dialog modal-xl">
        <form method="POST" action="{{ route('instagram.schedule') }}"> @csrf <input type="hidden" name="media_id"
                id="scheduleMediaId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Schedule Post</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-7">
                            <div id="scheduleImageError" class="text-danger text-end mt-2 fw-bold"></div>
                            <label for="input21" class="form-label">Role</label>
                            <select id="input21" class="form-select" name="account_id" required>
                                <option selected="">--Select--</option> @foreach($accounts as $account) <option
                                    value="{{ $account->id}}">{{ $account->instagram_username}}</option> @endforeach
                            </select>
                            <label class="mt-2">Date & Time</label>
                            <input type="datetime-local" name="schedule_time" class="form-control" required>
                            <label class="mt-2">Caption</label>
                            <textarea name="caption" class="form-control" rows="8"></textarea>
                        </div>
                        <div class="col-5">
                            <div class="text-center mb-3">
                                <div class="insta-preview-box">
                                    <img id="scheduleImagePreview" style="display:none; width:100%;" />
                                    <video id="scheduleVideoPreview" controls style="display:none; width:100%;"></video>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- Insta Reel and Post Upload Modal -->
<div class="modal fade" id="instareelandpostuploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="uploadModalLabel">Upload Media</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-pills nav-fill mb-4 bg-light p-1" id="uploadTab" role="tablist"
                    style="border-radius: 12px;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill" id="post-tab" data-bs-toggle="pill"
                            data-bs-target="#tab-photo" type="button">
                            <i class="fas fa-image me-1"></i> Image's </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill" id="reel-tab" data-bs-toggle="pill"
                            data-bs-target="#tab-video" type="button">
                            <i class="fas fa-video me-1"></i> Video's </button>
                    </li>
                </ul>
                <div class="tab-content" id="uploadTabContent">
                    <div class="tab-pane fade show active" id="tab-photo" role="tabpanel">
                        <form method="POST" action="/media/upload" enctype="multipart/form-data"> @csrf <input
                                type="hidden" name="type" value="image">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Upload Image </label>
                                <input type="file" name="media_file" accept="image/*" class="form-control">
                            </div>
                            <button type="submit"
                                class="btn btn-primary w-25 rounded-pill py-2 shadow-sm float-end">Upload Image</button>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="tab-video" role="tabpanel">
                        <form method="POST" action="/media/upload" enctype="multipart/form-data"> @csrf <input
                                type="hidden" name="type" value="video">
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Upload Video (MP4)</label>
                                <input type="file" name="media_file" accept="video/mp4" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-dark w-50 rounded-pill py-2 shadow-sm">
                                <i class="fas fa-play me-1"></i> Upload Video </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fullscreen Image Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-fullscreen">
        <div class="modal-content bg-dark border-0">
            <div class="modal-body text-center p-0 position-relative">
                <!-- Close -->
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 z-3"
                    data-bs-dismiss="modal"></button>
                <!-- Prev Button -->
                <button id="prevBtn"
                    class="btn btn-dark position-absolute top-50 start-0 translate-middle-y px-3 py-2 z-3">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <!-- Image -->
                <img id="previewImage" src="" class="img-fluid w-100 h-100" style="object-fit: contain;">
                <!-- Next Button -->
                <button id="nextBtn"
                    class="btn btn-dark position-absolute top-50 end-0 translate-middle-y px-3 py-2 z-3">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Schedule List Modal -->
<div class="modal fade" id="schedulelistmodal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="uploadModalLabel">Schedule List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <hr class="mb-0">
            <div class="modal-body">
                <div class="row row-cols-1 row-cols-md-4 g-3">
                    <!-- Scheduled -->
                    <div class="col">
                        <div class="card rounded-4" style="border-bottom:4px solid #6F42C1;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-dark">Scheduled</div>
                                    <div class="fw-bold" style="font-size:2rem; color:#6F42C1;">
                                        {{ $totalScheduled }}
                                    </div>
                                </div>
                                <div class="stat-icon" style="background: rgba(111, 66, 193, 0.1);">
                                    <i class="fas fa-clock" style="color:#6F42C1;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Posted -->
                    <div class="col">
                        <div class="card rounded-4" style="border-bottom:4px solid #10b981;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-dark">Posted</div>
                                    <div class="fw-bold text-success" style="font-size:2rem;">
                                        {{ $totalPosted }}
                                    </div>
                                </div>
                                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Reels -->
                    <div class="col">
                        <div class="card rounded-4" style="border-bottom:4px solid #000;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-dark">Reels</div>
                                    <div class="fw-bold" style="font-size:2rem; color:#000;">
                                        {{ $totalReels }}
                                    </div>
                                </div>
                                <div class="stat-icon" style="background: rgba(0,0,0,0.08);">
                                    <i class="fas fa-video" style="color:#000;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Posts -->
                    <div class="col">
                        <div class="card rounded-4" style="border-bottom:4px solid #ff8800;">
                            <div class="card-body d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-dark">Posts</div>
                                    <div class="fw-bold" style="font-size:2rem; color:#ff8800;">
                                        {{ $totalPosts }}
                                    </div>
                                </div>
                                <div class="stat-icon" style="background: rgba(255, 136, 0, 0.1);">
                                    <i class="fas fa-image" style="color:#ff8800;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Sr</th>
                                <th class="text-center">Account</th>
                                <th class="text-center">Type</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Scheduled Date</th>
                                <th class="text-center">Created At</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledPosts as $post)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ $post->account->instagram_username ?? 'N/A' }}</td>
                                <td class="text-center">
                                    @if(optional($post->media)->type === 'video')
                                    <span class="badge bg-dark">REEL</span>
                                    @elseif(optional($post->media)->type === 'image')
                                    <span class="badge bg-warning text-dark">POST</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($post->is_posted)
                                    <span class="text-success">Posted</span>
                                    @else
                                    <span class="text-warning">Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($post->scheduled_at)->format('d l Y h:i A') }}
                                </td>

                                <td class="text-center">
                                    {{ \Carbon\Carbon::parse($post->created_at)->format('d l Y h:i A') }}
                                </td>
                                <td class="text-center">...</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{ $scheduledPosts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<!--Schedule List Edit Modal-->
<div class="modal fade" id="editPostModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Scheduled Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <form id="editPostForm"> @csrf <input type="hidden" id="post_id">
                    <!-- Caption -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Caption</label>
                        <textarea class="form-control rounded-3" id="caption" rows="3"></textarea>
                    </div>
                    <!-- Schedule -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Schedule Time</label>
                        <input type="datetime-local" class="form-control rounded-3" id="scheduled_at">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-3"> Update Post </button>
                </form>
            </div>
        </div>
    </div>
</div> 
<!-- Delete Media Confirmation -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.delete-media').forEach(button => {
        button.addEventListener('click', function() {
            let mediaId = this.dataset.id;
            let card = this.closest('.col-lg-3, .col-md-4, .col-md-6, .col-sm-6');
            Swal.fire({
                title: 'Delete this media?',
                text: "This will permanently delete file from server!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e3342f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/media/${mediaId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(data => {
                        Swal.fire('Deleted!', 'Your media has been deleted.',
                            'success');
                        // Remove card from UI
                        if (card) card.remove();
                    }).catch(err => {
                        Swal.fire('Error!', 'Something went wrong.', 'error');
                    });
                }
            });
        });
    });
});
</script>
<script>
// 1. Auto-play/pause logic when scrolling
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        const video = entry.target;
        if (entry.isIntersecting) {
            video.play().catch(error => console.log("Auto-play blocked"));
        } else {
            video.pause();
        }
    });
}, {
    threshold: 0.7
}); // Plays when 70% of video is visible
document.querySelectorAll('.reel-video').forEach(video => {
    observer.observe(video);
    // 2. Manual Play/Pause on click
    video.addEventListener('click', () => {
        if (video.paused) {
            video.play();
        } else {
            video.pause();
        }
    });
});
</script>
<script>
document.querySelectorAll('.open-schedule-modal').forEach(btn => {
    btn.addEventListener('click', function () {

        let mediaId = this.dataset.id;
        let url = this.dataset.url || this.dataset.image;
        let type = this.dataset.type || 'image';

        document.getElementById('scheduleMediaId').value = mediaId;

        let imgPreview = document.getElementById('scheduleImagePreview');
        let videoPreview = document.getElementById('scheduleVideoPreview');
        let container = document.querySelector('.insta-preview-box');
        let errorBox = document.getElementById('scheduleImageError');

        errorBox.innerText = "";

        // reset
        imgPreview.style.display = "none";
        videoPreview.style.display = "none";

        // 🎥 VIDEO (REEL)
        if (type === 'video') {
            container.style.aspectRatio = "9/16";

            videoPreview.style.display = "block";
            videoPreview.src = url;
            videoPreview.load();
            videoPreview.play();
        }

        // 🖼️ IMAGE
        else {
            container.style.aspectRatio = "1/1";

            const img = new Image();
            img.src = url;

            img.onload = function () {
                const ratio = img.width / img.height;

                if (ratio < 0.8 || ratio > 1.91) {
                    errorBox.innerText = "❌ Invalid size! Image may crop.";
                }

                imgPreview.style.display = "block";
                imgPreview.src = url;
            };
        }

        new bootstrap.Modal(document.getElementById('scheduleModal')).show();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔥 OPEN MODAL & LOAD DATA
    document.querySelectorAll('.edit-post').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            fetch(`/scheduled-post/${id}`).then(res => res.json()).then(data => {
                document.getElementById('post_id').value = data.id;
                document.getElementById('caption').value = data.caption ?? '';
                // format datetime for input
                let dt = new Date(data.scheduled_at);
                let formatted = dt.toISOString().slice(0, 16);
                document.getElementById('scheduled_at').value = formatted;
                let modal = new bootstrap.Modal(document.getElementById(
                    'editPostModal'));
                modal.show();
            });
        });
    });
    // 🔥 UPDATE DATA
    document.getElementById('editPostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let id = document.getElementById('post_id').value;
        fetch(`/scheduled-post/update/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                caption: document.getElementById('caption').value,
                scheduled_at: document.getElementById('scheduled_at').value
            })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            }
        });
    });
    // ✅ DELETE
    document.querySelectorAll('.delete-post').forEach(btn => {
        btn.addEventListener('click', function() {
            let id = this.dataset.id;
            let row = this.closest('tr'); // for removing row without reload
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/scheduled-post/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            // ✅ Success Alert
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Your post has been deleted.',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            // ✅ Remove row without reload (smooth UX)
                            row.remove();
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire('Error!', 'Something went wrong.',
                                'error');
                        }
                    });
                }
            });
        });
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.has('page')) {
        let modal = new bootstrap.Modal(document.getElementById('schedulelistmodal'));
        modal.show();
    }
});
</script>
<script>
$(document).on('click', '#schedule-table .pagination a', function(e) {
    e.preventDefault();

    let url = $(this).attr('href');

    $.ajax({
        url: url,
        type: "GET",
        success: function(data) {
            $('#schedule-table').html(data);
        }
    });
});
</script>
@endsection