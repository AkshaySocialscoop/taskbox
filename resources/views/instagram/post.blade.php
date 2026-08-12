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

        box-shadow: 0 4px 15px rgba(0,0,0,0.05);

    }

    .creator-card:hover {

        transform: translateY(-5px);

        box-shadow: 0 8px 25px rgba(0,0,0,0.1);

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

        max-width: 350px; /* Standard phone width */

        margin: 0 auto;

        aspect-ratio: 9/16;

        background: #000;

        border-radius: 15px;

        overflow: hidden;

        box-shadow: 0 10px 30px rgba(0,0,0,0.3);

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

        background: linear-gradient(transparent, rgba(0,0,0,0.8));

        color: white;

        pointer-events: none; /* Allows clicking the video through the text */

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

        text-shadow: 0 2px 4px rgba(0,0,0,0.5);

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

    .media-grid {

        column-count: 3;

        column-gap: 10px;

    }

    

    .media-item {

        break-inside: avoid;

        margin-bottom: 10px;

    }

</style> 

<div class="container-fluid py-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 px-3">

        <div>

            <p class="text-muted mb-0">UserName</p>

            <h2 class="fw-bold mb-0">{{$account->instagram_username}}</h2>

        </div>

        <div>

            <ul class="nav nav-pills justify-content-center align-items-center" id="instaTab" role="tablist">

        <li class="nav-item" role="presentation">

            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#posts"><i class="fas fa-th me-2"></i>Posts</button>

        </li>

        <li class="nav-item" role="presentation">

            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#reels"><i class="fas fa-play-circle me-2"></i>Reels</button>

        </li> 

    </ul>

        </div>

        <div class="d-flex gap-2   mt-md-0"> 

            <button class="btn btn-sm btn-gradient px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#instareelandpostuploadModal">

                 <i class="fas fa-plus-circle me-1"></i> Upload

            </button>

        </div>

    </div> 



    <div class="tab-content" id="instaTabContent">

        <div class="tab-pane fade show active" id="posts" role="tabpanel">

            <div class="row g-4"> 
                @foreach($posts as $post)

                @if(isset($post['media_type']) && ($post['media_type'] == 'IMAGE' || $post['media_type'] == 'CAROUSEL_ALBUM'))

                <div class="col-sm-6 col-md-4 col-lg-3">

                    <div class="card creator-card h-100">

                        <div class="position-relative">

                            <img src="{{ $post['media_url'] ?? '' }}" class="card-img-top" style="aspect-ratio: 1/1; object-fit: cover;">

                            <div class="position-absolute bottom-0 start-0 m-2">

                                <span class="stat-badge small shadow-sm">

                                    ❤️ {{ $post['like_count'] ?? 0 }} 💬 {{ $post['comments_count'] ?? 0 }}

                                </span>

                            </div>

                        </div>
                           
                        <div class="row p-3">
                            <div class="col-3">
                               <button 
                                    class="btn btn-sm btn-danger delete-post-btn"
                                    data-id="{{ $post['id'] ?? '' }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <div class="col-9"> <a href="{{ $post['permalink'] ?? '#' }}" target="_blank" class="btn btn-sm btn-light w-100 rounded-3 text-muted">View on Instagram</a></div>
                             
                            

                        </div>

                    </div>

                </div>

                @endif

                @endforeach

            </div>

        </div>



        <div class="tab-pane fade" id="reels">

            <div class="row g-4">

                @foreach($posts as $post)

                @if(isset($post['media_type']) && $post['media_type'] == 'VIDEO')

                <div class="col-lg-3 col-md-6 mb-4">

                    <div class="reel-container">

                        <video class="reel-video" loop muted playsinline>

                            <source src="{{ $post['media_url'] ?? '' }}" type="video/mp4">

                        </video>

        

                        <div class="reel-actions">

                            <a href="#" class="action-item">

                                <i class="fas fa-heart" style="color:red;"></i>

                                <span>{{ $post['like_count'] ?? 0 }}</span>

                            </a>

                            <a href="#" class="action-item">

                                <i class="fas fa-comment"></i>

                                <span>{{ $post['comments_count'] ?? 0 }}</span>

                            </a>

                            <a href="#" class="action-item">

                                <i class="fas fa-paper-plane"></i>

                            </a>

                        </div>

        

                        <div class="reel-overlay" style="pointer-events: auto;">

                            <h6 class="mb-1 fw-bold">{{ $post['username'] ?? 'YourUsername' }}</h6>

                            

                            <div class="caption-container d-flex align-items-end">

                                <p class="small mb-0 caption-text text-truncate-custom" onclick="toggleCaption(this)">

                                    {{ $post['caption'] ?? 'No caption provided for this reel.' }}

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

                @endif

                @endforeach

            </div>

        </div> 

    </div>

</div>

<!-- Insta Reel and Post Upload Modal -->

<div class="modal fade" id="instareelandpostuploadModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">

            <div class="modal-header border-bottom-0 pb-0">

                <h5 class="modal-title fw-bold" id="uploadModalLabel">Create New Content</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

        

            <div class="modal-body">

                <ul class="nav nav-pills nav-fill mb-4 bg-light p-1" id="uploadTab" role="tablist" style="border-radius: 12px;">

                    <li class="nav-item" role="presentation">

                        <button class="nav-link active rounded-pill" id="post-tab" data-bs-toggle="pill" data-bs-target="#tab-post" type="button">

                            <i class="fas fa-image me-1"></i> Post

                        </button>

                    </li>

                    <li class="nav-item" role="presentation">

                        <button class="nav-link rounded-pill" id="reel-tab" data-bs-toggle="pill" data-bs-target="#tab-reel" type="button">

                            <i class="fas fa-video me-1"></i> Reel

                        </button>

                    </li>

                    <li class="nav-item" role="presentation">

                        <button class="nav-link rounded-pill" id="story-tab" data-bs-toggle="pill" data-bs-target="#tab-story" type="button">

                            <i class="fas fa-history me-1"></i> Story

                        </button>

                    </li>

                </ul>

        

                <div class="tab-content" id="uploadTabContent">

                    

                    <div class="tab-pane fade show active" id="tab-post" role="tabpanel">

                        <form method="POST" action="/instagram/publish/{{ $account->id }}" enctype="multipart/form-data">

                            @csrf

                            <div class="row">

                                <div class="col-9">

                                    <input type="hidden" name="type" value="image">

                                    <div class="mb-3">

                                        <label class="form-label small fw-bold">Select Media</label>



                                        <input type="hidden" name="media_id" id="selectedMediaId">



                                        <button type="button" 

                                            class="btn btn-outline-primary w-100 open-media-modal" 

                                            data-type="image" >

                                            Select Image

                                        </button>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label small fw-bold">Caption</label>

                                        <textarea name="caption" rows="3" placeholder="Write a caption..." class="form-control rounded-3"></textarea>

                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">Publish Post</button>

                                </div>

                                <div class="col-3 border rounded-4 d-flex justify-content-center align-items-center"> 

                                    <!-- Preview -->

                                    <input type="hidden" name="media_id" id="postMediaId">

                                    <div id="postPreviewBox" class="d-none text-center">

                                        <img id="postPreviewImage" class="img-fluid rounded mb-2" style="max-height:150px;">

                                        <video id="postPreviewVideo" class="w-100 rounded mb-2 d-none" controls style="max-height:150px;"></video>

                                        <p id="postFileName" class="small text-muted mb-0"></p>

                                    </div>

                                </div>

                            </div>

                            

                        </form>

                    </div>

        

                    <div class="tab-pane fade" id="tab-reel" role="tabpanel">

                        <form method="POST" action="/instagram/publish/{{ $account->id }}" enctype="multipart/form-data">

                            @csrf

                            <div class="row">

                                <div class="col-9">

                                    <input type="hidden" name="type" value="video">

                                    <div class="mb-3">

                                        <label class="form-label small fw-bold">Select Media</label>



                                        <input type="hidden" name="media_id" id="selectedMediaId">



                                        <button type="button" 

                                            class="btn btn-outline-primary w-100 open-media-modal" 

                                            data-type="video">

                                            Select Video

                                        </button>

                                    </div>

                                    <div class="mb-3">

                                        <label class="form-label small fw-bold">Reel Description</label>

                                        <textarea name="caption" rows="3" placeholder="Share what's happening in this reel..." class="form-control rounded-3"></textarea>

                                    </div>

                                    <button type="submit" class="btn btn-dark w-100 rounded-pill py-2 shadow-sm">

                                        <i class="fas fa-play me-1"></i> Share Reel

                                    </button>

                                </div>

                                <div class="col-3 border rounded-4 d-flex justify-content-center align-items-center"> 

                                    <!-- Preview -->

                                    <input type="hidden" name="media_id" id="reelMediaId">

                                    <div id="reelPreviewBox" class="d-none text-center">

                                        <img id="reelPreviewImage" class="img-fluid rounded mb-2 d-none" style="max-height:150px;">

                                        <video id="reelPreviewVideo" class="w-100 rounded mb-2 d-none" controls style="max-height:150px;"></video>

                                        <p id="reelFileName" class="small text-muted mb-0"></p>

                                    </div>

                                </div>

                            </div> 

                        </form>

                    </div>

        

                    <div class="tab-pane fade" id="tab-story" role="tabpanel">

                        <form method="POST" action="/instagram/publish/{{ $account->id }}" enctype="multipart/form-data">

                            @csrf

                            <input type="hidden" name="type" value="story">

                            <div class="mb-3">

                                <label class="form-label small fw-bold">Upload Media (Image or Video)</label>

                                <input type="text" name="media_url" placeholder="https://..." class="form-control rounded-3">

                            </div>

                            <div class="alert alert-info py-2 small" style="border-radius: 10px;">

                                <i class="fas fa-info-circle me-1"></i> Stories last for 24 hours.

                            </div>

                            <button type="submit" class="btn btn-danger w-100 rounded-pill py-2 shadow-sm" style="background: linear-gradient(45deg, #f09433, #bc1888);">

                                Upload to Story

                            </button>

                        </form>

                    </div>

        

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Media Library Modal -->

<div class="modal fade" id="mediaLibraryModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            

            <div class="modal-header">

                <h5 class="modal-title">Select Media</h5>

                <button class="btn-close" data-bs-dismiss="modal"></button>

            </div>



            <div class="modal-body">

                <div class="media-grid">



                    @foreach($media as $item)

                        <div class="media-item mb-3 text-center  media-item" data-type="{{ $item->type }}">

                            

                            @if($item->type == 'image')

                                <img src="{{ asset('storage/'.$item->file_path) }}"

                                     class="img-fluid rounded select-media"

                                     data-id="{{ $item->id }}"

                                     data-src="{{ asset('storage/'.$item->file_path) }}"

                                     data-name="{{ $item->file_name }}"

                                     style="cursor:pointer;">

                            @else

                                <video class="w-100 rounded select-media"

                                       data-id="{{ $item->id }}"

                                       data-src="{{ asset('storage/'.$item->file_path) }}"

                                       data-name="{{ $item->file_name }}"

                                       style="cursor:pointer;">

                                    <source src="{{ asset('storage/'.$item->file_path) }}">

                                </video>

                            @endif



                        </div>

                    @endforeach



                </div>

            </div>



        </div>

    </div>

</div>

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

    }, { threshold: 0.7 }); // Plays when 70% of video is visible



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

    function toggleCaption(element) {

        // If it's currently truncated, show full text. Otherwise, truncate it back.

        if (element.classList.contains('text-truncate-custom')) {

            element.classList.remove('text-truncate-custom');

            element.style.webkitLineClamp = 'unset'; // Shows all lines

        } else {

            element.classList.add('text-truncate-custom');

            element.style.webkitLineClamp = '1'; // Back to one line

        }

    }

</script>



<script>

    document.querySelectorAll('.select-media').forEach(item => {

        item.addEventListener('click', function() {



            let id = this.dataset.id;

            let src = this.dataset.src;

            let name = this.dataset.name;

            let type = this.closest('.media-item').dataset.type;



            if(activeType === 'image'){

                document.getElementById('postMediaId').value = id;



                document.getElementById('postPreviewImage').src = src;

                document.getElementById('postPreviewImage').classList.remove('d-none');



                document.getElementById('postPreviewVideo').classList.add('d-none');



                document.getElementById('postFileName').innerText = name;

                document.getElementById('postPreviewBox').classList.remove('d-none');



            } else if(activeType === 'video'){



                document.getElementById('reelMediaId').value = id;



                document.getElementById('reelPreviewVideo').src = src;

                document.getElementById('reelPreviewVideo').classList.remove('d-none');



                document.getElementById('reelPreviewImage').classList.add('d-none');



                document.getElementById('reelFileName').innerText = name;

                document.getElementById('reelPreviewBox').classList.remove('d-none');

            }



            // Close media modal

            bootstrap.Modal.getInstance(document.getElementById('mediaLibraryModal')).hide();



            // Reopen upload modal

            new bootstrap.Modal(document.getElementById('instareelandpostuploadModal')).show();

        });

    });

</script> 

<script>

    let activeType = null;



    document.querySelectorAll('.open-media-modal').forEach(btn => {

        btn.addEventListener('click', function () {

            activeType = this.dataset.type; // image or video



            let uploadModal = bootstrap.Modal.getInstance(document.getElementById('instareelandpostuploadModal'));

            uploadModal.hide();



            document.querySelectorAll('.media-item').forEach(item => {

                item.style.display = (item.dataset.type === activeType) ? 'block' : 'none';

            });



            new bootstrap.Modal(document.getElementById('mediaLibraryModal')).show();

        });

    });

</script>

<script>

    document.querySelectorAll('.select-media').forEach(item => {

        item.addEventListener('click', function() {



            let id = this.dataset.id;

            let src = this.dataset.src;

            let name = this.dataset.name;



            document.getElementById('selectedMediaId').value = id;



            document.getElementById('selectedPreview').src = src;

            document.getElementById('selectedFileName').innerText = name;

            document.getElementById('mediaPreviewBox').classList.remove('d-none');



            // Close media modal

            let mediaModalEl = document.getElementById('mediaLibraryModal');

            let mediaModal = bootstrap.Modal.getInstance(mediaModalEl);

            mediaModal.hide();



            // Reopen upload modal

            let uploadModal = new bootstrap.Modal(document.getElementById('instareelandpostuploadModal'));

            uploadModal.show();

        });

    });

</script>
<script>
document.querySelectorAll('.delete-post-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault(); // ✅ important

        let id = this.dataset.id;

        if (!confirm('Are you sure to delete this post?')) return;

        fetch(`/instagram/delete/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message);
            if (data.status) {
                location.reload();
            }
        });
    });
});
</script>
@endsection