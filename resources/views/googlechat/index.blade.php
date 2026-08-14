@extends('layout.index')

@section('title', 'Google Chat')

@section('content')
    @if(session()->has('google_email'))
        {{-- Logged in state --}}
        <header style="background:#f5f5f5; padding:15px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #ddd;">
            <div style="display:flex; align-items:center;">
                <img src="{{ $avatar }}" alt="Avatar" 
                     style="border-radius:50%; width:50px; height:50px; object-fit:cover; margin-right:15px;">
                <div>
                    <strong style="font-size:16px;">{{ $name }}</strong><br>
                    <span style="color:#555; font-size:14px;">{{ $email }}</span>
                </div>
            </div>

            <form action="{{ route('google.logout') }}" method="POST">
                @csrf
                <button type="submit" 
                        style="background:#e74c3c; color:#fff; border:none; padding:8px 15px; border-radius:4px; cursor:pointer;">
                    Logout
                </button>
            </form>
        </header>

        <main style="padding:20px;">
            <h1 style="margin-bottom:10px;">Welcome, {{ $name }}!</h1>
            <p style="color:#333;">Your Google ID: <strong>{{ $id }}</strong></p>
        </main>
    @else
        {{-- Logged out state --}}
        <div style="display:flex; justify-content:center; align-items:center; height:70vh;">
            <a href="{{ route('google.login') }}" 
               style="background:#4285F4; color:#fff; padding:12px 20px; border-radius:4px; text-decoration:none; font-size:16px;">
                Login with Google
            </a>
        </div>
    @endif
@endsection
