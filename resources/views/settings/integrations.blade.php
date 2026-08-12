@extends('layout.index')

@section('content')

<div class="mb-4">
    <h3 class="fw-bold mb-1">Integrations</h3>
    <p class="text-muted mb-0">
        Connect TaskBox with your favorite applications and services.
    </p>
</div>

{{-- GOOGLE ACCOUNT --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

            <div class="d-flex align-items-center gap-3">
                <div class="icon-box bg-light rounded-circle">
                    <i class="fab fa-google text-danger"></i>
                </div>

                <div>
                    <h5 class="fw-semibold mb-1">Google Account</h5>

                    <div class="text-muted small">
                        {{ $googleAccount?->google_email ?? 'No Google account connected' }}
                    </div>
                </div>
            </div>

            @if($googleAccount)
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success-subtle text-success px-3 py-2">
                        <i class="fas fa-circle me-1" style="font-size:7px;"></i>
                        Connected
                    </span>

                    <form action="{{ route('google.disconnect') }}" method="POST">
                        @csrf

                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            Disconnect
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('google.connect') }}" class="btn btn-primary">
                    <i class="fab fa-google me-2"></i>
                    Connect Google
                </a>
            @endif

        </div>
    </div>
</div>

{{-- GOOGLE SERVICES --}}
<div class="mb-3">
    <h5 class="fw-bold mb-1">Google Services</h5>
    <p class="text-muted small">
        Use Google services directly inside TaskBox.
    </p>
</div>

<div class="row g-4">

    {{-- GOOGLE CHAT --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="service-icon">
                        <i class="fas fa-comments text-primary"></i>
                    </div>

                    <span class="badge bg-success-subtle text-success">
                        Available
                    </span>
                </div>

                <h5 class="fw-semibold mb-2">Google Chat</h5>

                <p class="text-muted small mb-4">
                    Send and receive Google Chat messages directly from TaskBox.
                </p>

                @if($googleAccount)
                    <a href="{{ route('google.chat') }}"
                       class="btn btn-primary w-100">
                        <i class="fas fa-comments me-2"></i>
                        Open Google Chat
                    </a>
                @else
                    <a href="{{ route('google.connect') }}"
                       class="btn btn-outline-primary w-100">
                        Connect Google
                    </a>
                @endif

            </div>
        </div>
    </div>

    {{-- GOOGLE CALENDAR --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="service-icon">
                        <i class="fas fa-calendar-alt text-danger"></i>
                    </div>

                    <span class="badge bg-light text-muted">
                        Coming Soon
                    </span>
                </div>

                <h5 class="fw-semibold mb-2">Google Calendar</h5>

                <p class="text-muted small mb-4">
                    View and manage Google Calendar events inside TaskBox.
                </p>

                <button class="btn btn-light w-100" disabled>
                    Coming Soon
                </button>

            </div>
        </div>
    </div>

    {{-- GMAIL --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="service-icon">
                        <i class="fas fa-envelope text-danger"></i>
                    </div>

                    <span class="badge bg-light text-muted">
                        Coming Soon
                    </span>
                </div>

                <h5 class="fw-semibold mb-2">Gmail</h5>

                <p class="text-muted small mb-4">
                    Read, send and manage Gmail messages inside TaskBox.
                </p>

                <button class="btn btn-light w-100" disabled>
                    Coming Soon
                </button>

            </div>
        </div>
    </div>

    {{-- GOOGLE DRIVE --}}
    <div class="col-xl-4 col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="service-icon">
                        <i class="fab fa-google-drive text-warning"></i>
                    </div>

                    <span class="badge bg-light text-muted">
                        Coming Soon
                    </span>
                </div>

                <h5 class="fw-semibold mb-2">Google Drive</h5>

                <p class="text-muted small mb-4">
                    Access and manage Google Drive files inside TaskBox.
                </p>

                <button class="btn btn-light w-100" disabled>
                    Coming Soon
                </button>

            </div>
        </div>
    </div>

</div>

<style>
    .icon-box,
    .service-icon {
        width: 52px;
        height: 52px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .service-icon {
        background: #f8f9fa;
        border-radius: 12px;
        font-size: 20px;
    }
</style>

@endsection