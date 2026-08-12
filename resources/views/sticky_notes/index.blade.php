@extends('layout.index')

@section('title', 'Dashboard')

@section('content')

 
     <!-- Header -->
    <nav class="navbar navbar-dark header sticky-top">
        <div class="container-fluid px-4 ">
        
            <button class="btn btn-primary text-white px-4 py-2 rounded-3 ms-auto" type="button" data-bs-toggle="offcanvas" data-bs-target="#noteEditorOffcanvas">
                <i class="bi bi-plus-lg me-2"></i>Create New Note
            </button>
        </div>
    </nav>

    <!-- Notes Board -->
    <div class="container-fluid notes-board">
        <div class="row g-4" id="notesBoard">
            <!-- Notes will be rendered here -->
        </div>
    </div>

    <!-- Offcanvas Editor -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="noteEditorOffcanvas" data-bs-backdrop="true" data-bs-scroll="false">
        <div class="offcanvas-header text-white">
            <h2 class="offcanvas-title">✍️ Create Your Note</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>

        <!-- Toolbar -->
        <div class="toolbar p-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn" onclick="formatText('bold')" title="Bold (Ctrl+B)">
                        <strong>B</strong>
                    </button>
                    <button type="button" class="btn" onclick="formatText('italic')" title="Italic (Ctrl+I)">
                        <em>I</em>
                    </button>
                    <button type="button" class="btn" onclick="formatText('underline')" title="Underline (Ctrl+U)">
                        <u>U</u>
                    </button>
                    <button type="button" class="btn" onclick="formatText('strikeThrough')" title="Strikethrough">
                        <s>S</s>
                    </button>
                </div>

                <div class="btn-group" role="group">
                    <button type="button" class="btn" onclick="formatText('justifyLeft')" title="Align Left">
                        <i class="bi bi-text-left"></i>
                    </button>
                    <button type="button" class="btn" onclick="formatText('justifyCenter')" title="Center">
                        <i class="bi bi-text-center"></i>
                    </button>
                    <button type="button" class="btn" onclick="formatText('justifyRight')" title="Align Right">
                        <i class="bi bi-text-right"></i>
                    </button>
                </div>

                <select class="form-select form-select-sm" onchange="changeFontSize(this.value)" style="width: 120px;">
                    <option value="3">Small</option>
                    <option value="4" selected>Normal</option>
                    <option value="5">Large</option>
                    <option value="6">X-Large</option>
                </select>

                <div class="btn-group" role="group">
                    <button type="button" class="btn" onclick="formatText('insertUnorderedList')" title="Bullet List">
                        <i class="bi bi-list-ul"></i>
                    </button>
                    <button type="button" class="btn" onclick="formatText('insertOrderedList')" title="Numbered List">
                        <i class="bi bi-list-ol"></i>
                    </button>
                </div>

                <button type="button" class="btn" onclick="clearFormatting()" title="Clear Formatting">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>

        <!-- Editor Body -->
        <div class="offcanvas-body">
            <div class="editor-container rounded-3 p-3">
                <div id="noteContent" contenteditable="true" class="form-control rounded-3 p-3" data-placeholder="Write your note here..." oninput="updateCharCount()"></div>
                
                <div class="mt-4">
                    <div class="color-section rounded-3 p-3">
                        <h5 class="mb-3 text-dark">🎨 Choose Note Color</h5>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <input type="color" id="colorPicker" value="#ffeb3b" class="form-control form-control-color rounded-3" onchange="updateColorPreview()">
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="color-preset active" style="background: #ffeb3b" data-color="#ffeb3b" onclick="selectColor('#ffeb3b')"></div>
                                <div class="color-preset" style="background: #ffccbc" data-color="#ffccbc" onclick="selectColor('#ffccbc')"></div>
                                <div class="color-preset" style="background: #b2dfdb" data-color="#b2dfdb" onclick="selectColor('#b2dfdb')"></div>
                                <div class="color-preset" style="background: #c5cae9" data-color="#c5cae9" onclick="selectColor('#c5cae9')"></div>
                                <div class="color-preset" style="background: #f8bbd0" data-color="#f8bbd0" onclick="selectColor('#f8bbd0')"></div>
                                <div class="color-preset" style="background: #fff9c4" data-color="#fff9c4" onclick="selectColor('#fff9c4')"></div>
                                <div class="color-preset" style="background: #d7ccc8" data-color="#d7ccc8" onclick="selectColor('#d7ccc8')"></div>
                                <div class="color-preset" style="background: #b3e5fc" data-color="#b3e5fc" onclick="selectColor('#b3e5fc')"></div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-gradient text-white w-100 py-3 mt-4 rounded-3 fw-bold" onclick="addNote()">
                        <i class="bi bi-save me-2"></i>Save Note
                    </button>
                    <div class="text-center char-count mt-2" id="charCount">0 characters</div>
                </div>
            </div>
        </div>
    </div>


<script>
    let notes = [];
    let draggedElement = null;
    let offcanvas;

    document.addEventListener('DOMContentLoaded', function () {
        offcanvas = new bootstrap.Offcanvas(
            document.getElementById('noteEditorOffcanvas')
        );
        loadNotes();
    });

    function closeEditor() {
        offcanvas.hide();
        document.getElementById('noteContent').innerHTML = '';
        document.getElementById('colorPicker').value = '#ffeb3b';
        updateCharCount();
    }

    /* ===========================
       TEXT FORMATTING
    ============================ */
    function formatText(command) {
        document.execCommand(command, false, null);
        document.getElementById('noteContent').focus();
        updateToolbarState();
    }

    function changeFontSize(size) {
        document.execCommand('fontSize', false, size);
        document.getElementById('noteContent').focus();
    }

    function updateToolbarState() {
        document.querySelectorAll('.toolbar .btn')
            .forEach(btn => btn.classList.remove('active'));

        if (document.queryCommandState('bold'))
            document.querySelector('[onclick*="bold"]').classList.add('active');

        if (document.queryCommandState('italic'))
            document.querySelector('[onclick*="italic"]').classList.add('active');

        if (document.queryCommandState('underline'))
            document.querySelector('[onclick*="underline"]').classList.add('active');
    }

    function clearFormatting() {
        const editor = document.getElementById('noteContent');
        editor.innerHTML = editor.innerText;
        editor.focus();
    }

    function updateCharCount() {
        const text = document.getElementById('noteContent').innerText.length;
        document.getElementById('charCount').textContent =
            `${text} character${text !== 1 ? 's' : ''}`;
        updateToolbarState();
    }

    /* ===========================
       STORAGE
    ============================ */
    function loadNotes() {
        const saved = localStorage.getItem('crmNotes');
        notes = saved ? JSON.parse(saved) : [];
        renderNotes();
    }

    function saveNotes() {
        localStorage.setItem('crmNotes', JSON.stringify(notes));
    }

    function addNote() {
        const editor = document.getElementById('noteContent');
        const color = document.getElementById('colorPicker').value;

        if (editor.innerText.trim() === '') {
            Swal.fire('Empty Note', 'Please write something.', 'warning');
            return;
        }

        notes.push({
            id: Date.now(),
            content: editor.innerHTML,
            color: color,
            timestamp: new Date().toLocaleString()
        });

        saveNotes();
        renderNotes();
        closeEditor();
    }

    /* ===========================
       ACTIONS
    ============================ */
    // function copyNoteContent(id) {
    //     const note = notes.find(n => n.id === id);
    //     navigator.clipboard.writeText(
    //         note.content.replace(/<[^>]+>/g, '')
    //     );
    //     Swal.fire('Copied!', 'Note copied to clipboard.', 'success');
    // }

    function copyNoteContent(id) {
    const note = notes.find(n => n.id === id);
    if (!note) return;

    const temp = document.createElement('div');
    temp.innerHTML = note.content;

    navigator.clipboard.writeText(temp.innerText).then(() => {
        Swal.fire('Copied!', 'Note copied to clipboard.', 'success');
    });
}


    function deleteNote(id) {
        Swal.fire({
            title: 'Delete Note?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then(res => {
            if (res.isConfirmed) {
                notes = notes.filter(n => n.id !== id);
                saveNotes();
                renderNotes();
            }
        });
    }

    /* ===========================
       RENDER (NO COLOR OPTION ON CARDS)
    ============================ */
    function renderNotes() {
        const board = document.getElementById('notesBoard');

        if (!notes.length) {
            board.innerHTML = `
                <div class="col-12 empty-state text-center">
                    <div class="empty-state-icon">📝</div>
                    <h3>No notes yet</h3>
                </div>`;
            return;
        }

        board.innerHTML = notes.map(note => `
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card note-card h-100"
                     style="background:${note.color}"
                     draggable="true">
                    <div class="card-body">
                        <div class="d-flex justify-content-end gap-2 mb-2">
                            <button class="btn btn-sm btn-light"
                                onclick="copyNoteContent(${note.id})">
                                <i class="bi bi-clipboard"></i>
                            </button>
                            <button class="btn btn-sm btn-light"
                                onclick="deleteNote(${note.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>

                        <div class="note-content">
                            ${note.content}
                        </div>

                        <div class="text-end mt-3">
                            <small class="timestamp">${note.timestamp}</small>
                        </div>
                    </div>
                </div>
            </div>
        `).join('');

        addDragAndDrop();
    }

    /* ===========================
       DRAG & DROP
    ============================ */
    function addDragAndDrop() {
        document.querySelectorAll('.note-card').forEach(card => {
            card.addEventListener('dragstart', function () {
                draggedElement = this;
                this.style.opacity = '0.5';
            });
            card.addEventListener('dragend', function () {
                this.style.opacity = '1';
            });
            card.addEventListener('dragover', e => e.preventDefault());
        });
    }

    /* ===========================
       SHORTCUTS
    ============================ */
    document.getElementById('noteContent').addEventListener('keydown', e => {
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'b') { e.preventDefault(); formatText('bold'); }
            if (e.key === 'i') { e.preventDefault(); formatText('italic'); }
            if (e.key === 'u') { e.preventDefault(); formatText('underline'); }
        }
    });

    document.getElementById('noteContent').addEventListener('mouseup', updateToolbarState);
    document.getElementById('noteContent').addEventListener('keyup', updateToolbarState);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeEditor();
    });
</script>



@endsection