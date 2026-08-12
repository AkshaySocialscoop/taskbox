<!doctype html>
<html lang="en" data-bs-theme="light">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'TaskBoxOffice')</title> 
    <meta name="csrf-token" content="{{ csrf_token() }}"> 
    @if ($errors->any())
    <meta name="swal-error" content="{{ $errors->first() }}">
    @endif
<script>
    window.taskConfig = {
        updateUrl: "{{ route('tasks.updateStatus') }}"
        countsUrl: "{{ route('tasks.counts') }}"
    };
</script> 


<!-- Favicon -->
<link rel="icon" href="{{ asset('assets/images/task-box-logo-white.png') }}" type="image/png">

<!-- Plugins CSS -->
<link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/metismenu/metisMenu.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/metismenu/mm-vertical.css') }}" rel="stylesheet">
<link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet">

<!-- Bootstrap CSS -->
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

<!-- Google Fonts (NO asset needed) -->
  <link rel="stylesheet" href="{{ asset('assets/css/extra-icons.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">

<!-- Main CSS -->
<link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">

<link href="{{ asset('sass/main.css') }}" rel="stylesheet">
<link href="{{ asset('sass/dark-theme.css') }}" rel="stylesheet">
<link href="{{ asset('sass/semi-dark.css') }}" rel="stylesheet">
<link href="{{ asset('sass/bordered-theme.css') }}" rel="stylesheet">
<link href="{{ asset('sass/responsive.css') }}" rel="stylesheet">
<!-- font awesome -->
 <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

 <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

</head>

<body>

    {{-- Header --}}
    @include('layout.header') 

    {{-- Sidebar --}}
    <div>
        @include('layout.sidebar')
    </div>

    {{-- Main Content --}} 
    <main class="main-wrapper">
        <div class="main-content">
            @yield('content') 
        </div>
    </main>
       

    {{-- Footer --}}
    
    @include('layout.footer')  
 
    @if(session()->has('success'))
        <meta name="swal-success" content="{{ session('success') }}">
    @endif

    @if(session()->has('error'))
        <meta name="swal-error" content="{{ session('error') }}">
    @endif

    
   
</body>

</html>