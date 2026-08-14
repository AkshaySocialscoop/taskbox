@extends('layout.index')

@section('title', 'Google Chat')

@section('content')
@if(session()->has('google_email'))
    {{-- Header --}}
    <header style="background:#f5f5f5; padding:15px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #ddd;">
        <div style="display:flex; align-items:center;">
            <img src="{{ $avatar ?? '/default-avatar.png' }}" alt="Avatar" 
                 style="border-radius:50%; width:50px; height:50px; object-fit:cover; margin-right:15px;">
            <div>
                <strong style="font-size:16px;">{{ $name ?? 'Unknown User' }}</strong><br>
                <span style="color:#555; font-size:14px;">{{ $email ?? '' }}</span>
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

    {{-- Main layout: spaces + messages --}}
    <div style="display:flex; height:80vh;">
        {{-- Left panel: Spaces --}}
        <aside style="width:25%; border-right:1px solid #ddd; padding:15px; overflow-y:auto;">
            <h3>Your Chat Spaces</h3>
            @if(!empty($spaces))
                <ul style="list-style:none; padding:0;">
                    @foreach($spaces as $space)
                        <li style="margin-bottom:8px;">
                            <form method="GET" action="{{ route('googlechat.index') }}">
                                <input type="hidden" name="space" value="{{ $space['name'] }}">
                                <button type="submit" 
                                        style="width:100%; text-align:left; background:#4285F4; color:#fff; border:none; padding:8px; border-radius:4px; cursor:pointer;">
                                    {{ $space['displayName'] ?? $space['name'] }}
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            @else
                <p>No spaces found.</p>
            @endif
        </aside>

        {{-- Right panel: Messages --}}
        <main style="flex:1; padding:15px; overflow-y:auto;">
            <h3>Messages</h3>
            @if(!empty($messages))
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @foreach($messages as $msg)
                        @php
                            $isMe = ($msg['sender']['name'] ?? '') === 'users/' . session('google_id');
                            $senderName  = $msg['sender']['displayName'] ?? 'Unknown';
                            $senderEmail = $msg['sender']['email'] ?? '';
                            $senderAvatar = $msg['sender']['avatar'] ?? '/default-avatar.png';
                        @endphp

                        <div style="display:flex; {{ $isMe ? 'justify-content:flex-end;' : 'justify-content:flex-start;' }}">
                            <div style="display:flex; align-items:flex-end; gap:8px; max-width:70%;">
                                {{-- Show avatar only for other users --}}
                                @if(!$isMe)
                                    <img src="{{ $senderAvatar }}" alt="Avatar" 
                                         style="width:32px; height:32px; border-radius:50%;">
                                @endif

                                <div style="padding:10px; border-radius:8px;
                                            background:{{ $isMe ? '#DCF8C6' : '#f1f1f1' }};
                                            color:#333;">
                                    <strong>{{ $senderName }}</strong>
                                    @if($senderEmail)
                                        <small style="color:#555;">({{ $senderEmail }})</small>
                                    @endif
                                    <p style="margin:5px 0;">{{ $msg['text'] ?? '[no text]' }}</p>
                                    <small style="color:#777;">{{ $msg['createTime'] ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>Select a space to view messages.</p>
            @endif
        </main>
    </div>
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
