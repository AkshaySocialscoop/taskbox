@extends('layout.index')

@section('title', 'Google Chat')

@section('content')
<style>
    /* ==========================================
       CHAT APPLICATION STYLES
       ========================================== */
    * {
        box-sizing: border-box;
    }
    
    .chat-container {
        display: flex;
        height: calc(100vh - 80px);
        background: #f0f2f5;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    /* ===== SIDEBAR ===== */
    .chat-sidebar {
        width: 320px;
        background: white;
        border-right: 1px solid #e0e0e0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }
    
    .chat-sidebar-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e0e0e0;
        background: #f8f9fa;
    }
    
    .chat-sidebar-header h3 {
        margin: 0;
        font-size: 18px;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .chat-sidebar-header .badge {
        background: #e8e8e8;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
        color: #666;
    }
    
    .chat-space-list {
        flex: 1;
        overflow-y: auto;
        padding: 10px 0;
    }
    
    .chat-space-item {
        padding: 10px 16px;
        cursor: pointer;
        transition: all 0.2s;
        border-left: 3px solid transparent;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    
    .chat-space-item:hover {
        background: #f0f2f5;
    }
    
    .chat-space-item.active {
        background: #e3f2fd;
        border-left-color: #1a73e8;
    }
    
    .chat-space-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        color: white;
        overflow: hidden;
    }
    
    .chat-space-avatar.group {
        background: #1a73e8;
    }
    
    .chat-space-avatar.dm {
        background: #ea4335;
    }
    
    .chat-space-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .chat-space-info {
        flex: 1;
        min-width: 0;
    }
    
    .chat-space-name {
        font-size: 14px;
        font-weight: 500;
        color: #1a1a1a;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .chat-space-meta {
        font-size: 11px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 2px;
        flex-wrap: wrap;
    }
    
    .chat-space-type {
        font-size: 9px;
        background: #f0f0f0;
        padding: 1px 8px;
        border-radius: 10px;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .chat-space-type.group {
        background: #e3f2fd;
        color: #1a73e8;
    }
    
    .chat-space-type.dm {
        background: #fce4ec;
        color: #ea4335;
    }
    
    /* ===== MAIN CHAT AREA ===== */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f0f2f5;
    }
    
    .chat-header {
        background: white;
        padding: 12px 24px;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
    }
    
    .chat-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
        color: white;
        overflow: hidden;
    }
    
    .chat-header-avatar.group {
        background: #1a73e8;
    }
    
    .chat-header-avatar.dm {
        background: #ea4335;
    }
    
    .chat-header-avatar img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .chat-header-info h4 {
        margin: 0;
        font-size: 16px;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .chat-header-info span {
        font-size: 12px;
        color: #888;
    }
    
    .chat-header-info .member-count {
        font-size: 11px;
        color: #666;
        background: #f0f0f0;
        padding: 1px 10px;
        border-radius: 10px;
    }
    
    /* ===== MESSAGES ===== */
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .message-wrapper {
        display: flex;
        margin-bottom: 6px;
    }
    
    .message-wrapper.sent {
        justify-content: flex-end;
    }
    
    .message-wrapper.received {
        justify-content: flex-start;
    }
    
    .message-bubble {
        max-width: 70%;
        padding: 8px 14px;
        border-radius: 12px;
        position: relative;
        word-wrap: break-word;
    }
    
    .message-bubble.sent {
        background: #dcf8c6;
        border-bottom-right-radius: 4px;
    }
    
    .message-bubble.received {
        background: white;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.08);
    }
    
    .message-sender {
        font-size: 12px;
        font-weight: 600;
        color: #1a73e8;
        margin-bottom: 2px;
        display: block;
    }
    
    .message-text {
        font-size: 14px;
        color: #1a1a1a;
        line-height: 1.5;
    }
    
    .message-text a {
        color: #1a73e8;
        text-decoration: none;
    }
    
    .message-text a:hover {
        text-decoration: underline;
    }
    
    .message-time {
        font-size: 10px;
        color: #999;
        margin-top: 4px;
        display: block;
        text-align: right;
    }
    
    /* ===== ATTACHMENTS ===== */
    .message-attachment {
        margin-top: 8px;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        padding: 10px;
        background: #fafafa;
        max-width: 100%;
    }
    
    .message-attachment img {
        max-width: 100%;
        max-height: 250px;
        border-radius: 4px;
        display: block;
    }
    
    .message-attachment video {
        max-width: 100%;
        max-height: 250px;
        border-radius: 4px;
    }
    
    .attachment-info {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 0;
        font-size: 13px;
        flex-wrap: wrap;
    }
    
    .attachment-info .file-icon {
        font-size: 24px;
    }
    
    .attachment-info .file-name {
        flex: 1;
        font-weight: 500;
        color: #1a1a1a;
        min-width: 50px;
    }
    
    .attachment-info .file-size {
        font-size: 11px;
        color: #888;
    }
    
    .attachment-info .download-link {
        background: #1a73e8;
        color: white;
        padding: 4px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        transition: background 0.2s;
    }
    
    .attachment-info .download-link:hover {
        background: #1557b0;
    }
    
    /* ===== CARDS ===== */
    .message-card {
        margin-top: 8px;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        padding: 12px 16px;
        background: white;
    }
    
    .message-card .card-title {
        font-weight: 600;
        font-size: 15px;
        color: #1a1a1a;
        margin-bottom: 4px;
    }
    
    .message-card .card-subtitle {
        font-size: 13px;
        color: #666;
        margin-bottom: 8px;
    }
    
    .message-card .card-text {
        font-size: 13px;
        color: #333;
    }
    
    .message-card .card-button {
        display: inline-block;
        background: #1a73e8;
        color: white;
        padding: 6px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 12px;
        margin-top: 8px;
        transition: background 0.2s;
    }
    
    .message-card .card-button:hover {
        background: #1557b0;
    }
    
    /* ===== EMPTY STATE ===== */
    .chat-empty {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #999;
        text-align: center;
        padding: 40px;
    }
    
    .chat-empty .icon {
        font-size: 64px;
        margin-bottom: 16px;
    }
    
    .chat-empty h3 {
        color: #666;
        margin: 0 0 8px;
    }
    
    .chat-empty p {
        color: #999;
        font-size: 14px;
        max-width: 400px;
    }
    
    /* ===== SCROLLBAR ===== */
    .chat-space-list::-webkit-scrollbar,
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }
    
    .chat-space-list::-webkit-scrollbar-track,
    .chat-messages::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .chat-space-list::-webkit-scrollbar-thumb,
    .chat-messages::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    
    .chat-space-list::-webkit-scrollbar-thumb:hover,
    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }
    
    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .chat-sidebar {
            width: 100%;
            height: 40vh;
            border-right: none;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .chat-container {
            flex-direction: column;
            height: auto;
        }
        
        .chat-main {
            height: 60vh;
        }
        
        .message-bubble {
            max-width: 85%;
        }
        
        .chat-header {
            padding: 10px 16px;
        }
        
        .chat-messages {
            padding: 12px 16px;
        }
    }
</style>

@if(Auth::check())
    @php $user = Auth::user(); @endphp

    {{-- ========================================== --}}
    {{-- HEADER                                    --}}
    {{-- ========================================== --}}
    <header style="background:#f8f9fa; padding:10px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #e0e0e0; flex-wrap:wrap; gap:10px;">
        <h2 style="margin:0; font-size:20px; color:#1a1a1a; display:flex; align-items:center; gap:10px;">
            💬 Google Chat
            @if($has_google && !$token_expired)
                <span style="background:#34a853; width:10px; height:10px; border-radius:50%; display:inline-block;"></span>
            @endif
        </h2>

        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            {{-- App User --}}
            <div style="display:flex; align-items:center; gap:8px; padding-right:12px; border-right:1px solid #e0e0e0;">
                @if($user->profile_photo_path)
                    <img src="{{ Storage::url($user->profile_photo_path) }}" alt="{{ $user->name }}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid #1a73e8;">
                @else
                    <div style="width:32px; height:32px; border-radius:50%; background:#1a73e8; color:white; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; border:2px solid #1a73e8;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <span style="font-size:10px; color:#888; display:block;">App</span>
                    <span style="font-size:13px; color:#1a1a1a; font-weight:500;">{{ $user->name }}</span>
                </div>
            </div>

            {{-- Google User --}}
            @if($has_google && $googleUser)
                <div style="display:flex; align-items:center; gap:8px; padding-right:12px; border-right:1px solid #e0e0e0;">
                    @if($googleUser['avatar'])
                        <img src="{{ $googleUser['avatar'] }}" alt="{{ $googleUser['name'] }}" style="width:32px; height:32px; border-radius:50%; object-fit:cover; border:2px solid #ea4335;">
                    @else
                        <div style="width:32px; height:32px; border-radius:50%; background:#ea4335; color:white; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; border:2px solid #ea4335;">
                            {{ strtoupper(substr($googleUser['name'] ?? 'G', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <span style="font-size:10px; color:#888; display:block;">Google</span>
                        <span style="font-size:13px; color:#1a1a1a; font-weight:500;">{{ $googleUser['name'] ?? 'Connected' }}</span>
                        <span style="font-size:10px; color:#888; display:block;">{{ $googleUser['email'] ?? '' }}</span>
                    </div>
                </div>

                <form action="{{ route('google.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:#f39c12; color:white; border:none; padding:5px 14px; border-radius:4px; cursor:pointer; font-size:12px; transition:background 0.2s;" onmouseover="this.style.background='#e67e22'" onmouseout="this.style.background='#f39c12'">
                        🔌 Disconnect
                    </button>
                </form>
            @else
                <a href="{{ route('google.login') }}" style="background:#1a73e8; color:white; padding:6px 16px; border-radius:4px; text-decoration:none; font-size:13px; transition:background 0.2s;" onmouseover="this.style.background='#1557b0'" onmouseout="this.style.background='#1a73e8'">
                    🔗 Connect Google
                </a>
            @endif

            {{-- Logout Button --}}
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" style="background:#ea4335; color:white; border:none; padding:6px 16px; border-radius:4px; cursor:pointer; font-size:13px; transition:background 0.2s;" onmouseover="this.style.background='#c62828'" onmouseout="this.style.background='#ea4335'">
                    🚪 Logout
                </button>
            </form>
        </div>
    </header>

    {{-- ========================================== --}}
    {{-- BODY CONTENT                             --}}
    {{-- ========================================== --}}
    @if(!$has_google)
        {{-- NOT CONNECTED --}}
        <div style="display:flex; justify-content:center; align-items:center; height:70vh; flex-direction:column; padding:40px; text-align:center;">
            <div style="font-size:64px; margin-bottom:16px;">🔗</div>
            <h3 style="color:#1a1a1a; margin:0 0 8px;">Connect Google Chat</h3>
            <p style="color:#666; max-width:400px; margin:0 0 20px;">Connect your Google account to start chatting with your teams and colleagues.</p>
            <a href="{{ route('google.login') }}" style="background:#1a73e8; color:white; padding:12px 28px; border-radius:6px; text-decoration:none; font-size:15px; transition:background 0.2s;" onmouseover="this.style.background='#1557b0'" onmouseout="this.style.background='#1a73e8'">
                Connect Google Chat
            </a>
        </div>
    @elseif($token_expired)
        {{-- TOKEN EXPIRED --}}
        <div style="display:flex; justify-content:center; align-items:center; height:70vh; flex-direction:column; padding:40px; text-align:center;">
            <div style="font-size:64px; margin-bottom:16px;">⚠️</div>
            <h3 style="color:#e67e22; margin:0 0 8px;">Session Expired</h3>
            <p style="color:#666; max-width:400px; margin:0 0 20px;">Your Google session has expired. Please reconnect to continue using Google Chat.</p>
            <a href="{{ route('google.login') }}" style="background:#1a73e8; color:white; padding:12px 28px; border-radius:6px; text-decoration:none; font-size:15px; transition:background 0.2s;" onmouseover="this.style.background='#1557b0'" onmouseout="this.style.background='#1a73e8'">
                Reconnect Google
            </a>
        </div>
    @else
        {{-- ========================================== --}}
        {{-- CHAT INTERFACE                          --}}
        {{-- ========================================== --}}
        <div class="chat-container">
            
            {{-- ===== SIDEBAR ===== --}}
            <div class="chat-sidebar">
                <div class="chat-sidebar-header">
                    <h3>
                        💬 Spaces
                        <span class="badge">{{ $total_spaces }}</span>
                    </h3>
                </div>
                
                <div class="chat-space-list">
                    @if(!empty($spaces))
                        @foreach($spaces as $space)
                            @php
                                // Get display name
                                $displayName = $space['displayName'] ?? '';
                                
                                // For DM without name, show fallback
                                if (empty($displayName) && $space['spaceType'] === 'DIRECT_MESSAGE') {
                                    $displayName = '💬 Direct Message';
                                }
                                if (empty($displayName)) {
                                    $displayName = '📁 Unnamed Space';
                                }
                                
                                $isActive = ($selectedSpace && $selectedSpace['name'] == $space['name']);
                                $isGroup = $space['spaceType'] === 'SPACE';
                                $memberAvatar = $space['memberAvatar'] ?? null;
                                $memberCount = $space['membershipCount']['joinedDirectHumanUserCount'] ?? 0;
                                
                                // Get last active time
                                $lastActive = isset($space['lastActiveTime']) && $space['lastActiveTime'] !== '1970-01-01T00:00:00Z'
                                    ? \Carbon\Carbon::parse($space['lastActiveTime'])->diffForHumans()
                                    : null;
                                
                                $avatarBg = $isGroup ? '#1a73e8' : '#ea4335';
                                $avatarText = strtoupper(substr($displayName, 0, 1));
                            @endphp

                            <a href="{{ route('googlechat.index', ['space' => $space['name']]) }}" 
                               class="chat-space-item {{ $isActive ? 'active' : '' }}">
                                
                                {{-- Avatar --}}
                                @if($memberAvatar)
                                    <div class="chat-space-avatar" style="background:{{ $avatarBg }}; padding:0;">
                                        <img src="{{ $memberAvatar }}" alt="{{ $displayName }}">
                                    </div>
                                @else
                                    <div class="chat-space-avatar {{ $isGroup ? 'group' : 'dm' }}">
                                        {{ $avatarText }}
                                    </div>
                                @endif
                                
                                {{-- Info --}}
                                <div class="chat-space-info">
                                    <div class="chat-space-name">{{ $displayName }}</div>
                                    <div class="chat-space-meta">
                                        <span class="chat-space-type {{ $isGroup ? 'group' : 'dm' }}">
                                            {{ $isGroup ? 'Group' : 'DM' }}
                                        </span>
                                        @if($isGroup)
                                            <span>👥 {{ $memberCount }}</span>
                                        @endif
                                        @if($lastActive)
                                            <span>● {{ $lastActive }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div style="padding:20px; text-align:center; color:#999;">
                            <p style="font-size:14px; margin:0;">No spaces found</p>
                            <p style="font-size:12px; margin:4px 0 0;">You might not be a member of any Google Chat spaces.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===== MAIN CHAT AREA ===== --}}
            <div class="chat-main">
                
                @if($selectedSpace)
                    @php
                        $headerName = $selectedSpace['displayName'] ?? 'Direct Message';
                        if (empty($selectedSpace['displayName']) && $selectedSpace['spaceType'] === 'DIRECT_MESSAGE') {
                            $headerName = '💬 Direct Message';
                        }
                        $isGroup = $selectedSpace['spaceType'] === 'SPACE';
                        $headerAvatar = $selectedSpace['memberAvatar'] ?? null;
                        $memberCount = $selectedSpace['membershipCount']['joinedDirectHumanUserCount'] ?? 0;
                        $avatarBg = $isGroup ? '#1a73e8' : '#ea4335';
                        $avatarText = strtoupper(substr($headerName, 0, 1));
                    @endphp

                    {{-- Chat Header --}}
                    <div class="chat-header">
                        @if($headerAvatar)
                            <div class="chat-header-avatar" style="background:{{ $avatarBg }}; padding:0;">
                                <img src="{{ $headerAvatar }}" alt="{{ $headerName }}">
                            </div>
                        @else
                            <div class="chat-header-avatar {{ $isGroup ? 'group' : 'dm' }}">
                                {{ $avatarText }}
                            </div>
                        @endif
                        
                        <div class="chat-header-info">
                            <h4>
                                {{ $headerName }}
                                @if($isGroup)
                                    <span class="member-count">👥 {{ $memberCount }}</span>
                                @endif
                            </h4>
                            <span>{{ $selectedSpace['name'] }}</span>
                            @if(!$isGroup && !empty($selectedSpace['memberEmail']))
                                <span style="margin-left:8px; color:#888;">{{ $selectedSpace['memberEmail'] }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- ===== MESSAGES AREA ===== --}}
                    <div class="chat-messages">
                        @if(!empty($messages))
                            @foreach($messages as $message)
                                @php
                                    // Get sender info
                                    $sender = $message['sender']['displayName'] ?? 'Unknown';
                                    if ($sender === 'Unknown' && !empty($message['sender']['name'])) {
                                        $sender = str_replace('users/', '', $message['sender']['name']);
                                        $sender = substr($sender, 0, 20) . '...';
                                    }
                                    
                                    $text = $message['text'] ?? '';
                                    $time = isset($message['createTime']) 
                                        ? \Carbon\Carbon::parse($message['createTime'])->format('M d, Y h:i A') 
                                        : 'N/A';
                                    $isMe = ($message['sender']['name'] ?? '') === 'users/' . $user->google_id;
                                    
                                    // Check attachments
                                    $attachments = $message['attachments'] ?? [];
                                    $hasAttachments = !empty($attachments);
                                    
                                    // Check cards
                                    $cards = $message['cards'] ?? [];
                                    $hasCards = !empty($cards);
                                    
                                    // Check thread
                                    $thread = $message['thread'] ?? null;
                                    $hasThread = !empty($thread);
                                    
                                    // Convert URLs to clickable links
                                    if (!empty($text)) {
                                        $text = preg_replace(
                                            '/(https?:\/\/[^\s]+)/',
                                            '<a href="$1" target="_blank">$1</a>',
                                            e($text)
                                        );
                                    }
                                @endphp

                                <div class="message-wrapper {{ $isMe ? 'sent' : 'received' }}">
                                    <div class="message-bubble {{ $isMe ? 'sent' : 'received' }}">
                                        
                                        {{-- Sender Name --}}
                                        @if(!$isMe)
                                            <span class="message-sender">{{ $sender }}</span>
                                        @endif

                                        {{-- Text --}}
                                        @if(!empty($text))
                                            <div class="message-text">
                                                {!! $text !!}
                                            </div>
                                        @endif

                                        {{-- Attachments --}}
                                        @if($hasAttachments)
                                            @foreach($attachments as $attachment)
                                                @php
                                                    $attachmentName = $attachment['name'] ?? 'Attachment';
                                                    $attachmentType = $attachment['mimeType'] ?? '';
                                                    $attachmentUrl = $attachment['uri'] ?? '';
                                                    $isImage = strpos($attachmentType, 'image/') === 0;
                                                    $isVideo = strpos($attachmentType, 'video/') === 0;
                                                    $isPDF = $attachmentType === 'application/pdf';
                                                @endphp

                                                <div class="message-attachment">
                                                    @if($isImage && $attachmentUrl)
                                                        <img src="{{ $attachmentUrl }}" alt="{{ $attachmentName }}" loading="lazy">
                                                        <div class="attachment-info">
                                                            <span class="file-icon">🖼️</span>
                                                            <span class="file-name">{{ $attachmentName }}</span>
                                                            <a href="{{ $attachmentUrl }}" target="_blank" class="download-link">View</a>
                                                        </div>
                                                    @elseif($isVideo && $attachmentUrl)
                                                        <video controls>
                                                            <source src="{{ $attachmentUrl }}" type="{{ $attachmentType }}">
                                                        </video>
                                                        <div class="attachment-info">
                                                            <span class="file-icon">🎥</span>
                                                            <span class="file-name">{{ $attachmentName }}</span>
                                                            <a href="{{ $attachmentUrl }}" target="_blank" class="download-link">Download</a>
                                                        </div>
                                                    @elseif($attachmentUrl)
                                                        <div class="attachment-info">
                                                            <span class="file-icon">📎</span>
                                                            <span class="file-name">{{ $attachmentName }}</span>
                                                            <span class="file-size">{{ $attachmentType ?: 'File' }}</span>
                                                            <a href="{{ $attachmentUrl }}" target="_blank" class="download-link">Download</a>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- Cards --}}
                                        @if($hasCards)
                                            @foreach($cards as $card)
                                                <div class="message-card">
                                                    @if(!empty($card['header']['title']))
                                                        <div class="card-title">{{ $card['header']['title'] }}</div>
                                                    @endif
                                                    @if(!empty($card['header']['subtitle']))
                                                        <div class="card-subtitle">{{ $card['header']['subtitle'] }}</div>
                                                    @endif
                                                    @if(!empty($card['sections']))
                                                        @foreach($card['sections'] as $section)
                                                            @if(!empty($section['widgets']))
                                                                @foreach($section['widgets'] as $widget)
                                                                    @php
                                                                        $textParagraph = $widget['textParagraph']['text'] ?? '';
                                                                        $image = $widget['image'] ?? null;
                                                                        $button = $widget['buttons'][0] ?? null;
                                                                    @endphp
                                                                    @if(!empty($textParagraph))
                                                                        <div class="card-text">{!! $textParagraph !!}</div>
                                                                    @endif
                                                                    @if(!empty($image))
                                                                        <img src="{{ $image['imageUrl'] ?? '' }}" alt="{{ $image['altText'] ?? 'Image' }}" style="max-width:100%; max-height:200px; border-radius:4px; margin:8px 0;">
                                                                    @endif
                                                                    @if(!empty($button))
                                                                        <a href="{{ $button['url'] ?? '#' }}" target="_blank" class="card-button">{{ $button['text'] ?? 'Click' }}</a>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif

                                        {{-- Thread Info --}}
                                        @if($hasThread)
                                            <div style="font-size:10px; color:#888; margin-top:4px;">🔗 In thread</div>
                                        @endif

                                        {{-- Time --}}
                                        <span class="message-time">{{ $time }}</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="chat-empty">
                                <div class="icon">💬</div>
                                <h3>No Messages Yet</h3>
                                <p>This space doesn't have any messages yet.</p>
                            </div>
                        @endif
                    </div>

                @else
                    {{-- No space selected --}}
                    <div class="chat-empty" style="height:100%;">
                        <div class="icon">💬</div>
                        <h3>Select a Space</h3>
                        <p>Choose a space from the left sidebar to start viewing messages.</p>
                    </div>
                @endif
            </div>
        </div>
    @endif

@else
    {{-- NOT LOGGED IN --}}
    <div style="display:flex; justify-content:center; align-items:center; height:70vh; flex-direction:column; padding:40px; text-align:center;">
        <div style="font-size:64px; margin-bottom:16px;">🔐</div>
        <h3 style="color:#1a1a1a; margin:0 0 8px;">Please Login</h3>
        <p style="color:#666; max-width:400px; margin:0 0 20px;">You need to be logged in to access Google Chat.</p>
        <a href="{{ route('login') }}" style="background:#1a73e8; color:white; padding:12px 28px; border-radius:6px; text-decoration:none; font-size:15px; transition:background 0.2s;" onmouseover="this.style.background='#1557b0'" onmouseout="this.style.background='#1a73e8'">
            Login to TaskBox
        </a>
    </div>
@endif
@endsection