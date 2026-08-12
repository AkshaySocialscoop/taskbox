@extends('layout.index')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-3">

    <div>
        <h4 class="fw-bold mb-1">
            <i class="fas fa-comments text-success me-2"></i>
            Google Chat
        </h4>

        <p class="text-muted mb-0 small">
            Chat with your Google contacts directly inside TaskBox.
        </p>
    </div>

    <div class="d-flex align-items-center gap-2">

        <span class="badge bg-success-subtle text-success px-3 py-2">
            <i class="fas fa-circle me-1" style="font-size:7px;"></i>
            Connected
        </span>

        <a href="{{ route('integration') }}"
            class="btn btn-light btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Integrations
        </a>

    </div>

</div>


<div class="card border-0 shadow-sm overflow-hidden"
    style="height:calc(100vh - 180px);min-height:600px;">

    <div class="row g-0 h-100">


        {{-- =========================================================
             LEFT SIDEBAR
        ========================================================== --}}

        <div class="col-lg-3 col-md-4 border-end bg-white h-100">

            {{-- SIDEBAR HEADER --}}
            <div class="p-3 border-bottom">

                <div class="d-flex align-items-center justify-content-between mb-3">

                    <h5 class="fw-bold mb-0">
                        Conversations
                    </h5>

                    <button
                        type="button"
                        class="btn btn-primary btn-sm"
                        id="newChatBtn">

                        <i class="fas fa-plus"></i>

                    </button>

                </div>


                {{-- SEARCH --}}
                <div class="input-group">

                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>

                    <input
                        type="text"
                        id="chatSearch"
                        class="form-control bg-light border-0"
                        placeholder="Search conversations...">

                </div>

            </div>


            {{-- CONVERSATION LIST --}}
            <div
                id="chatSpaceList"
                class="overflow-auto"
                style="height:calc(100% - 125px);">

                @forelse($spaces as $space)

                @php

                /*
                |--------------------------------------------------------------------------
                | Google Chat space name
                |--------------------------------------------------------------------------
                */

                $spaceName = is_array($space)
                ? ($space['name'] ?? '')
                : ($space->name ?? '');

                /*
                |--------------------------------------------------------------------------
                | Display name
                |--------------------------------------------------------------------------
                */

                $displayName = is_array($space)
                ? ($space['displayName'] ?? null)
                : ($space->displayName ?? null);

                /*
                |--------------------------------------------------------------------------
                | Space type
                |--------------------------------------------------------------------------
                */

                $spaceType = is_array($space)
                ? ($space['spaceType'] ?? $space['type'] ?? null)
                : ($space->spaceType ?? $space->type ?? null);

                /*
                |--------------------------------------------------------------------------
                | Never show raw spaces/xxxx as primary title
                |--------------------------------------------------------------------------
                */

                if (!$displayName) {

                if ($spaceType === 'DIRECT_MESSAGE') {
                $displayName = 'Direct Message';
                } elseif ($spaceType === 'GROUP_CHAT') {
                $displayName = 'Group Chat';
                } else {
                $displayName = 'Google Chat';
                }
                }

                @endphp


                <button
                    type="button"
                    class="chat-space-item w-100 border-0 text-start bg-white p-3"
                    data-space="{{ $spaceName }}"
                    data-name="{{ $displayName }}">

                    <div class="d-flex align-items-center gap-3">

                        <div
                            class="chat-avatar rounded-circle bg-success-subtle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:44px;height:44px;">

                            <i class="fas fa-comments text-success"></i>

                        </div>


                        <div class="flex-grow-1 overflow-hidden">

                            <div class="fw-semibold text-truncate chat-space-name">
                                {{ $displayName }}
                            </div>

                            <div class="text-muted small text-truncate">

                                @if($spaceType === 'DIRECT_MESSAGE')
                                Direct message
                                @elseif($spaceType === 'GROUP_CHAT')
                                Group conversation
                                @else
                                Google Chat
                                @endif

                            </div>

                        </div>

                    </div>

                </button>

                @empty

                <div class="text-center text-muted p-4">

                    <div
                        class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3"
                        style="width:65px;height:65px;">

                        <i class="fas fa-comments fa-2x text-muted"></i>

                    </div>

                    <h6 class="fw-semibold">
                        No conversations
                    </h6>

                    <p class="small mb-0">
                        No Google Chat conversations were found.
                    </p>

                </div>

                @endforelse

            </div>

        </div>


        {{-- =========================================================
             RIGHT CHAT PANEL
        ========================================================== --}}

        <div class="col-lg-9 col-md-8 h-100 d-flex flex-column bg-light">


            {{-- CHAT HEADER --}}
            <div class="bg-white border-bottom p-3">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center me-3"
                        style="width:45px;height:45px;">

                        <i class="fas fa-comments text-success"></i>

                    </div>


                    <div class="flex-grow-1">

                        <h6
                            class="fw-bold mb-0"
                            id="activeChatName">

                            Select a conversation

                        </h6>

                        <small
                            class="text-muted"
                            id="activeChatStatus">

                            Choose a conversation from the left.

                        </small>

                    </div>


                    {{-- CHAT ACTIONS --}}
                    <div
                        id="chatActions"
                        class="d-none">

                        <button
                            type="button"
                            class="btn btn-light btn-sm"
                            title="Refresh messages"
                            id="refreshMessagesBtn">

                            <i class="fas fa-sync-alt"></i>

                        </button>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 MESSAGES
            ====================================================== --}}

            <div
                id="messagesContainer"
                class="flex-grow-1 overflow-auto p-4">

                {{-- EMPTY STATE --}}

                <div
                    id="emptyChatState"
                    class="h-100 d-flex align-items-center justify-content-center text-center">

                    <div>

                        <div
                            class="rounded-circle bg-success-subtle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width:75px;height:75px;">

                            <i class="fas fa-comments text-success fa-2x"></i>

                        </div>

                        <h5 class="fw-semibold">
                            Google Chat
                        </h5>

                        <p class="text-muted mb-0">
                            Select a conversation to view messages.
                        </p>

                    </div>

                </div>


                {{-- LOADING STATE --}}

                <div
                    id="messagesLoading"
                    class="d-none h-100 align-items-center justify-content-center">

                    <div class="text-center">

                        <div
                            class="spinner-border text-success mb-3"
                            role="status">
                        </div>

                        <div class="text-muted small">
                            Loading messages...
                        </div>

                    </div>

                </div>


                {{-- ERROR STATE --}}

                <div
                    id="messagesError"
                    class="d-none h-100 align-items-center justify-content-center">

                    <div class="text-center">

                        <i class="fas fa-exclamation-circle text-danger fa-2x mb-3"></i>

                        <h6 class="fw-semibold">
                            Unable to load messages
                        </h6>

                        <p
                            id="messagesErrorText"
                            class="text-muted small mb-3">
                        </p>

                        <button
                            type="button"
                            id="retryMessagesBtn"
                            class="btn btn-outline-primary btn-sm">

                            <i class="fas fa-redo me-1"></i>
                            Retry

                        </button>

                    </div>

                </div>


                {{-- ACTUAL MESSAGE LIST --}}

                <div
                    id="messageList"
                    class="d-none">
                </div>

            </div>


            {{-- =====================================================
                 MESSAGE COMPOSER
            ====================================================== --}}

            <div class="bg-white border-top p-3">

                <form
                    id="chatMessageForm">

                    @csrf

                    <input
                        type="hidden"
                        id="activeSpace"
                        name="space">


                    <div class="d-flex align-items-end gap-2">


                        {{-- ATTACHMENT BUTTON --}}
                        <button
                            type="button"
                            id="attachmentBtn"
                            class="btn btn-light"
                            title="Attach file"
                            disabled>

                            <i class="fas fa-paperclip"></i>

                        </button>


                        {{-- MESSAGE INPUT --}}
                        <div class="flex-grow-1">

                            <textarea
                                name="message"
                                id="chatMessage"
                                class="form-control"
                                rows="1"
                                maxlength="4000"
                                placeholder="Select a conversation..."
                                disabled></textarea>

                            <div class="d-flex justify-content-end mt-1">

                                <small
                                    id="messageCounter"
                                    class="text-muted">

                                    0 / 4000

                                </small>

                            </div>

                        </div>


                        {{-- SEND BUTTON --}}
                        <button
                            type="submit"
                            id="sendMessageBtn"
                            class="btn btn-primary"
                            disabled>

                            <i class="fas fa-paper-plane"></i>

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- ================================================================
     PAGE CSS
================================================================ --}}

<style>
    .chat-space-item {
        transition: background-color .15s ease;
    }

    .chat-space-item:hover {
        background: #f8f9fa !important;
    }

    .chat-space-item.active {
        background: #eaf7ef !important;
    }

    .chat-space-item.active .chat-space-name {
        color: #198754;
    }

    #messagesContainer {
        scroll-behavior: smooth;
    }

    .chat-message {
        max-width: 75%;
    }

    .chat-message.me {
        margin-left: auto;
    }

    .chat-message-content {
        padding: 10px 14px;
        border-radius: 14px;
        word-break: break-word;
    }

    .chat-message:not(.me) .chat-message-content {
        background: #ffffff;
        border: 1px solid #e9ecef;
    }

    .chat-message.me .chat-message-content {
        background: #198754;
        color: #ffffff;
    }

    .chat-message-time {
        font-size: 11px;
        margin-top: 4px;
    }

    #chatMessage {
        resize: none;
        max-height: 120px;
    }
</style>


{{-- ================================================================
     PAGE JAVASCRIPT
================================================================ --}}

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | Elements
        |--------------------------------------------------------------------------
        */

        const spaceItems = document.querySelectorAll('.chat-space-item');

        const activeSpaceInput =
            document.getElementById('activeSpace');

        const activeChatName =
            document.getElementById('activeChatName');

        const activeChatStatus =
            document.getElementById('activeChatStatus');

        const messagesContainer =
            document.getElementById('messagesContainer');

        const emptyChatState =
            document.getElementById('emptyChatState');

        const messagesLoading =
            document.getElementById('messagesLoading');

        const messagesError =
            document.getElementById('messagesError');

        const messagesErrorText =
            document.getElementById('messagesErrorText');

        const messageList =
            document.getElementById('messageList');

        const chatMessage =
            document.getElementById('chatMessage');

        const sendMessageBtn =
            document.getElementById('sendMessageBtn');

        const attachmentBtn =
            document.getElementById('attachmentBtn');

        const chatMessageForm =
            document.getElementById('chatMessageForm');

        const chatSearch =
            document.getElementById('chatSearch');

        const refreshMessagesBtn =
            document.getElementById('refreshMessagesBtn');

        const retryMessagesBtn =
            document.getElementById('retryMessagesBtn');

        const chatActions =
            document.getElementById('chatActions');

        const messageCounter =
            document.getElementById('messageCounter');


        /*
        |--------------------------------------------------------------------------
        | Current conversation
        |--------------------------------------------------------------------------
        */

        let currentSpace = null;
        let currentSpaceName = null;


        /*
        |--------------------------------------------------------------------------
        | CSRF
        |--------------------------------------------------------------------------
        */

        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value;


        /*
        |--------------------------------------------------------------------------
        | Utility
        |--------------------------------------------------------------------------
        */

        function escapeHtml(value) {

            if (value === null || value === undefined) {
                return '';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }


        /*
        |--------------------------------------------------------------------------
        | Loading state
        |--------------------------------------------------------------------------
        */

        function showLoading() {

            emptyChatState.classList.add('d-none');

            messagesError.classList.add('d-none');

            messageList.classList.add('d-none');

            messagesLoading.classList.remove('d-none');

            messagesLoading.classList.add('d-flex');

        }


        function hideLoading() {

            messagesLoading.classList.add('d-none');

            messagesLoading.classList.remove('d-flex');

        }


        /*
        |--------------------------------------------------------------------------
        | Error state
        |--------------------------------------------------------------------------
        */

        function showError(message) {

            hideLoading();

            emptyChatState.classList.add('d-none');

            messageList.classList.add('d-none');

            messagesError.classList.remove('d-none');

            messagesError.classList.add('d-flex');

            messagesErrorText.textContent =
                message || 'Something went wrong while loading messages.';

        }


        /*
        |--------------------------------------------------------------------------
        | Empty state
        |--------------------------------------------------------------------------
        */

        function showEmptyState() {

            hideLoading();

            messagesError.classList.add('d-none');

            messagesError.classList.remove('d-flex');

            messageList.classList.add('d-none');

            emptyChatState.classList.remove('d-none');

        }


        /*
        |--------------------------------------------------------------------------
        | Format message
        |--------------------------------------------------------------------------
        */

        function getMessageText(message) {

            if (!message) {
                return '';
            }

            if (message.text) {
                return message.text;
            }

            if (message.argumentText) {
                return message.argumentText;
            }

            return '';

        }


        /*
        |--------------------------------------------------------------------------
        | Render messages
        |--------------------------------------------------------------------------
        */

        function renderMessages(messages) {

            messageList.innerHTML = '';

            if (!messages || messages.length === 0) {

                messageList.innerHTML = `

                <div class="text-center text-muted py-5">

                    <i class="fas fa-comments fa-2x mb-3 opacity-50"></i>

                    <p class="mb-0">
                        No messages yet.
                    </p>

                </div>

            `;

                messageList.classList.remove('d-none');

                return;
            }


            messages.forEach(function(message) {

                const text =
                    getMessageText(message);

                if (!text) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Google Chat sender
                |--------------------------------------------------------------------------
                */

                const senderName =
                    message.sender?.displayName ||
                    message.sender?.name ||
                    'Google User';


                /*
                |--------------------------------------------------------------------------
                | Current user's message
                |--------------------------------------------------------------------------
                */

                const isMine =
                    message.sender?.type === 'HUMAN' &&
                    message.isMine === true;


                const messageClass =
                    isMine ? 'me' : '';


                const time =
                    message.createTime ?
                    new Date(message.createTime).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit'
                    }) :
                    '';


                const html = `

                <div class="chat-message ${messageClass} mb-3">

                    <div class="small text-muted mb-1">
                        ${escapeHtml(senderName)}
                    </div>

                    <div class="chat-message-content">
                        ${escapeHtml(text)}
                    </div>

                    <div class="chat-message-time text-muted">
                        ${escapeHtml(time)}
                    </div>

                </div>

            `;

                messageList.insertAdjacentHTML(
                    'beforeend',
                    html
                );

            });


            messageList.classList.remove('d-none');

            scrollMessagesToBottom();

        }


        /*
        |--------------------------------------------------------------------------
        | Scroll messages
        |--------------------------------------------------------------------------
        */

        function scrollMessagesToBottom() {

            setTimeout(function() {

                messagesContainer.scrollTop =
                    messagesContainer.scrollHeight;

            }, 50);

        }


        /*
        |--------------------------------------------------------------------------
        | Load messages
        |--------------------------------------------------------------------------
        */

        async function loadMessages(space) {

            if (!space) {
                return;
            }

            showLoading();


            try {

                const url =
                    `{{ url('/google/chat/messages') }}?space=${encodeURIComponent(space)}`;


                const response =
                    await fetch(url, {

                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }

                    });


                const data =
                    await response.json();


                if (!response.ok || data.success === false) {

                    throw new Error(
                        data.error ||
                        'Unable to load messages.'
                    );

                }


                hideLoading();

                messagesError.classList.add('d-none');

                messagesError.classList.remove('d-flex');

                renderMessages(
                    data.messages || []
                );


                activeChatStatus.textContent =
                    `${data.messages?.length || 0} messages`;


            } catch (error) {

                console.error(
                    'Google Chat message loading error:',
                    error
                );

                showError(
                    error.message ||
                    'Unable to load messages.'
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Select conversation
        |--------------------------------------------------------------------------
        */

        spaceItems.forEach(function(item) {

            item.addEventListener('click', function() {

                spaceItems.forEach(function(spaceItem) {

                    spaceItem.classList.remove('active');

                });


                item.classList.add('active');


                currentSpace =
                    item.dataset.space;

                currentSpaceName =
                    item.dataset.name;


                activeSpaceInput.value =
                    currentSpace;


                activeChatName.textContent =
                    currentSpaceName ||
                    'Google Chat';


                activeChatStatus.textContent =
                    'Loading messages...';


                chatMessage.disabled = false;

                sendMessageBtn.disabled = false;

                attachmentBtn.disabled = false;

                chatMessage.placeholder =
                    'Type a message...';


                chatActions.classList.remove('d-none');


                loadMessages(
                    currentSpace
                );

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Search conversations
        |--------------------------------------------------------------------------
        */

        chatSearch.addEventListener('input', function() {

            const search =
                this.value.trim().toLowerCase();


            spaceItems.forEach(function(item) {

                const name =
                    (
                        item.dataset.name ||
                        ''
                    ).toLowerCase();


                if (name.includes(search)) {

                    item.classList.remove('d-none');

                } else {

                    item.classList.add('d-none');

                }

            });

        });


        /*
        |--------------------------------------------------------------------------
        | Refresh messages
        |--------------------------------------------------------------------------
        */

        refreshMessagesBtn?.addEventListener(
            'click',
            function() {

                if (currentSpace) {

                    loadMessages(
                        currentSpace
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Retry
        |--------------------------------------------------------------------------
        */

        retryMessagesBtn.addEventListener(
            'click',
            function() {

                if (currentSpace) {

                    loadMessages(
                        currentSpace
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Message counter
        |--------------------------------------------------------------------------
        */

        chatMessage.addEventListener(
            'input',
            function() {

                messageCounter.textContent =
                    `${this.value.length} / 4000`;


                /*
                |--------------------------------------------------------------------------
                | Auto resize
                |--------------------------------------------------------------------------
                */

                this.style.height =
                    'auto';

                this.style.height =
                    Math.min(
                        this.scrollHeight,
                        120
                    ) + 'px';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Enter to send
        |--------------------------------------------------------------------------
        */

        chatMessage.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Enter' &&
                    !event.shiftKey
                ) {

                    event.preventDefault();

                    chatMessageForm.requestSubmit();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SEND MESSAGE
        |--------------------------------------------------------------------------
        */

        chatMessageForm.addEventListener(
            'submit',
            async function(event) {

                event.preventDefault();


                if (!currentSpace) {

                    alert(
                        'Please select a conversation first.'
                    );

                    return;
                }


                const message =
                    chatMessage.value.trim();


                if (!message) {
                    return;
                }


                sendMessageBtn.disabled = true;

                chatMessage.disabled = true;


                try {

                    const response =
                        await fetch(
                            `{{ url('/google/chat/send') }}`, {

                                method: 'POST',

                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },

                                body: JSON.stringify({

                                    space: currentSpace,

                                    message: message

                                })

                            }
                        );


                    const data =
                        await response.json();


                    if (!response.ok || data.success === false) {

                        throw new Error(
                            data.error ||
                            'Unable to send message.'
                        );

                    }


                    chatMessage.value = '';

                    chatMessage.style.height =
                        'auto';

                    messageCounter.textContent =
                        '0 / 4000';


                    /*
                    |--------------------------------------------------------------------------
                    | Reload messages
                    |--------------------------------------------------------------------------
                    */

                    await loadMessages(
                        currentSpace
                    );


                } catch (error) {

                    console.error(
                        'Google Chat send error:',
                        error
                    );


                    alert(
                        error.message ||
                        'Unable to send message.'
                    );

                } finally {

                    sendMessageBtn.disabled = false;

                    chatMessage.disabled = false;

                    chatMessage.focus();

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | New Chat
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('newChatBtn')
            ?.addEventListener(
                'click',
                function() {

                    /*
                    |--------------------------------------------------------------------------
                    | We will implement Google Chat user/space creation
                    | after the basic chat flow is stable.
                    |--------------------------------------------------------------------------
                    */

                    alert(
                        'New Chat will be added in the next step.'
                    );

                }
            );

    });
</script>

@endsection