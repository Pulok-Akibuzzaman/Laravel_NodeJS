<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Task Manager | Laravel + Node.js</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Socket.io Client -->
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
    <style>
        :root {
            --bg-gradient: radial-gradient(circle at 50% 0%, #1e1b4b 0%, #0f172a 60%, #080d1a 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --card-hover-border: rgba(99, 102, 241, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-primary: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.25);
            --accent-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --success-color: #10b981;
            --success-bg: rgba(16, 185, 129, 0.15);
            --warning-color: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.15);
            --danger-color: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        body {
            background: var(--bg-gradient);
            color: var(--text-primary);
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .app-container {
            width: 100%;
            max-width: 800px;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Header Section */
        .header-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .header-info h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-info p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        /* Live Indicator Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.875rem;
            border-radius: 9999px;
            font-size: 0.8125rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(15, 23, 42, 0.6);
            transition: all 0.3s ease;
        }

        .status-badge.online {
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.3);
            background: var(--success-bg);
        }

        .status-badge.offline {
            color: var(--danger-color);
            border-color: rgba(239, 68, 68, 0.3);
            background: var(--danger-bg);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: currentColor;
            box-shadow: 0 0 10px currentColor;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.2); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* Task Statistics Banner */
        .stats-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .stats-count {
            font-size: 1.1rem;
            font-weight: 700;
            color: #a5b4fc;
        }

        .progress-bar-bg {
            width: 100%;
            height: 10px;
            background: rgba(15, 23, 42, 0.8);
            border-radius: 9999px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .progress-bar-fill {
            height: 100%;
            width: 0%;
            background: var(--accent-gradient);
            border-radius: 9999px;
            transition: width 0.5s ease-in-out;
            box-shadow: 0 0 12px var(--accent-glow);
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .form-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
        }

        .input-field {
            width: 100%;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--card-border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            color: var(--text-primary);
            font-size: 0.9375rem;
            outline: none;
            transition: all 0.2s ease;
        }

        .input-field:focus {
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: rgba(15, 23, 42, 0.9);
        }

        textarea.input-field {
            resize: vertical;
            min-height: 70px;
        }

        .btn-submit {
            align-self: flex-end;
            background: var(--accent-gradient);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-size: 0.9375rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 15px var(--accent-glow);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Task List Section */
        .tasks-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
        }

        .tasks-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .tasks-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 0.875rem;
        }

        /* Individual Task Card */
        .task-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            transition: all 0.25s ease;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .task-item:hover {
            border-color: var(--card-hover-border);
            background: rgba(15, 23, 42, 0.85);
        }

        .task-item.completed {
            opacity: 0.7;
            background: rgba(15, 23, 42, 0.35);
        }

        .task-left {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            flex: 1;
        }

        /* Checkbox Custom Toggle */
        .checkbox-container {
            position: relative;
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            margin-top: 2px;
        }

        .checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 22px;
            width: 22px;
            background-color: rgba(30, 41, 59, 0.9);
            border: 2px solid #475569;
            border-radius: 6px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .checkbox-container:hover input ~ .checkmark {
            border-color: var(--accent-primary);
        }

        .checkbox-container input:checked ~ .checkmark {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .checkmark:after {
            content: "";
            display: none;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .checkbox-container input:checked ~ .checkmark:after {
            display: block;
        }

        .task-details {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .task-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-primary);
            transition: color 0.2s ease;
        }

        .task-item.completed .task-title {
            text-decoration: line-through;
            color: var(--text-secondary);
        }

        .task-desc {
            font-size: 0.85rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        .task-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .badge {
            padding: 0.25rem 0.625rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-pending {
            background: var(--warning-bg);
            color: var(--warning-color);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-completed {
            background: var(--success-bg);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .btn-delete {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-delete:hover {
            color: var(--danger-color);
            background: var(--danger-bg);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: var(--text-secondary);
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            stroke: var(--text-secondary);
            margin-bottom: 0.75rem;
            opacity: 0.5;
        }

        /* Notification Toast */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--accent-primary);
            color: var(--text-primary);
            padding: 0.875rem 1.25rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            z-index: 100;
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }
    </style>
</head>
<body>

    <div class="app-container">
        <!-- App Header & WebSocket Status -->
        <header class="header-card">
            <div class="header-info">
                <h1>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#6366f1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    Real-Time Task Manager
                </h1>
                <p>Laravel API + Node.js Express + Socket.io WebSockets</p>
            </div>
            <div id="socket-status" class="status-badge offline">
                <span class="pulse-dot"></span>
                <span id="status-text">Disconnected</span>
            </div>
        </header>

        <!-- Task Statistics Banner -->
        <section class="stats-card">
            <div class="stats-header">
                <span class="stats-title">Task Completion Counter</span>
                <span id="stats-counter" class="stats-count">0 of 0 tasks completed</span>
            </div>
            <div class="progress-bar-bg">
                <div id="progress-fill" class="progress-bar-fill"></div>
            </div>
        </section>

        <!-- New Task Creation Form -->
        <section class="form-card">
            <h2 class="form-title">Create New Task</h2>
            <form id="task-form" onsubmit="handleCreateTask(event)">
                <div class="form-group">
                    <input type="text" id="task-title" class="input-field" placeholder="Task title (e.g. Build WebSockets demo)..." required />
                    <textarea id="task-desc" class="input-field" placeholder="Task description (optional)..."></textarea>
                    <button type="submit" id="btn-add" class="btn-submit">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Add Task
                    </button>
                </div>
            </form>
        </section>

        <!-- Task List Display -->
        <section class="tasks-card">
            <div class="tasks-header">
                <h2 class="tasks-title">Task Queue</h2>
                <span id="total-badge" style="font-size: 0.85rem; color: var(--text-secondary);">0 items</span>
            </div>
            <div id="task-list" class="task-list">
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                    <p>Loading tasks from Laravel REST API...</p>
                </div>
            </div>
        </section>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="toast">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        <span id="toast-message">Task updated live</span>
    </div>

    <script>
        const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const LARAVEL_API_URL = isLocal ? 'http://localhost:8000/api/tasks' : (window.location.origin + '/api/tasks');
        const SOCKET_SERVER_URL = isLocal ? 'http://localhost:3000' : '{{ env("NODE_SOCKET_URL", "https://laravel-nodejs.onrender.com") }}';


        let socket = null;
        let tasks = [];

        // DOM Elements
        const socketStatusEl = document.getElementById('socket-status');
        const statusTextEl = document.getElementById('status-text');
        const statsCounterEl = document.getElementById('stats-counter');
        const progressFillEl = document.getElementById('progress-fill');
        const taskListEl = document.getElementById('task-list');
        const totalBadgeEl = document.getElementById('total-badge');
        const toastEl = document.getElementById('toast');
        const toastMessageEl = document.getElementById('toast-message');

        // Initialize Application
        document.addEventListener('DOMContentLoaded', () => {
            initWebSocket();
            fetchTasks();
        });

        // Initialize Socket.io Connection
        function initWebSocket() {
            try {
                socket = io(SOCKET_SERVER_URL, {
                    transports: ['websocket', 'polling'],
                    reconnectionAttempts: 10
                });

                socket.on('connect', () => {
                    console.log('Connected to Socket.io Server:', socket.id);
                    socketStatusEl.className = 'status-badge online';
                    statusTextEl.textContent = 'Live Sync Active';
                });

                socket.on('disconnect', () => {
                    console.warn('Disconnected from Socket.io Server');
                    socketStatusEl.className = 'status-badge offline';
                    statusTextEl.textContent = 'Disconnected';
                });

                // Listen for real-time broadcasts from Node.js server
                socket.on('taskUpdated', (payload) => {
                    console.log('Real-time task update event received:', payload);
                    showToast(`Real-time update: ${payload.event || 'Task synced'}`);
                    fetchTasks(); // Refresh state from API
                });
            } catch (err) {
                console.error('Socket initialization error:', err);
            }
        }

        // Fetch Tasks from Laravel API
        async function fetchTasks() {
            try {
                const res = await fetch(LARAVEL_API_URL);
                if (!res.ok) throw new Error('API fetch failed');
                tasks = await res.json();
                renderTasks();
                updateStats();
            } catch (err) {
                console.error('Fetch error:', err);
                taskListEl.innerHTML = `
                    <div class="empty-state">
                        <p style="color: var(--danger-color);">Failed to load tasks from Laravel backend (${LARAVEL_API_URL}). Make sure artisan serve is running.</p>
                    </div>
                `;
            }
        }

        // Render Task Items in DOM
        function renderTasks() {
            if (!tasks || tasks.length === 0) {
                taskListEl.innerHTML = `
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <p>No tasks found. Add a task above to get started!</p>
                    </div>
                `;
                totalBadgeEl.textContent = '0 items';
                return;
            }

            totalBadgeEl.textContent = `${tasks.length} item${tasks.length > 1 ? 's' : ''}`;

            taskListEl.innerHTML = tasks.map(task => `
                <div class="task-item ${task.is_completed ? 'completed' : ''}" id="task-${task.id}">
                    <div class="task-left">
                        <label class="checkbox-container">
                            <input type="checkbox" ${task.is_completed ? 'checked' : ''} onchange="toggleTaskComplete(${task.id}, this.checked)">
                            <span class="checkmark"></span>
                        </label>
                        <div class="task-details">
                            <div class="task-title">${escapeHtml(task.title)}</div>
                            ${task.description ? `<div class="task-desc">${escapeHtml(task.description)}</div>` : ''}
                        </div>
                    </div>
                    <div class="task-meta">
                        <span class="badge ${task.is_completed ? 'badge-completed' : 'badge-pending'}">
                            ${task.is_completed ? 'Completed' : 'Pending'}
                        </span>
                        <button class="btn-delete" title="Delete Task" onclick="deleteTask(${task.id})">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                        </button>
                    </div>
                </div>
            `).join('');
        }

        // Update Counter Stats Header
        function updateStats() {
            const total = tasks.length;
            const completed = tasks.filter(t => t.is_completed).length;
            const percent = total > 0 ? Math.round((completed / total) * 100) : 0;

            statsCounterEl.textContent = `${completed} of ${total} tasks completed (${percent}%)`;
            progressFillEl.style.width = `${percent}%`;
        }

        // Create Task via Laravel API
        async function handleCreateTask(e) {
            e.preventDefault();
            const titleInput = document.getElementById('task-title');
            const descInput = document.getElementById('task-desc');
            const btn = document.getElementById('btn-add');

            const title = titleInput.value.trim();
            const description = descInput.value.trim();

            if (!title) return;

            btn.disabled = true;
            btn.style.opacity = '0.6';

            try {
                const res = await fetch(LARAVEL_API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ title, description })
                });

                if (res.ok) {
                    titleInput.value = '';
                    descInput.value = '';
                    fetchTasks();
                } else {
                    alert('Failed to create task');
                }
            } catch (err) {
                console.error('Create task error:', err);
            } finally {
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        }

        // Toggle Task Completion via Laravel API
        async function toggleTaskComplete(id, isCompleted) {
            try {
                const res = await fetch(`${LARAVEL_API_URL}/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ is_completed: isCompleted })
                });

                if (res.ok) {
                    fetchTasks();
                }
            } catch (err) {
                console.error('Toggle complete error:', err);
            }
        }

        // Delete Task via Laravel API
        async function deleteTask(id) {
            if (!confirm('Are you sure you want to delete this task?')) return;

            try {
                const res = await fetch(`${LARAVEL_API_URL}/${id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json' }
                });

                if (res.ok) {
                    fetchTasks();
                }
            } catch (err) {
                console.error('Delete task error:', err);
            }
        }

        // Utility: Escape HTML
        function escapeHtml(str) {
            return str ? str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;") : '';
        }

        // Toast Notification Helper
        function showToast(msg) {
            toastMessageEl.textContent = msg;
            toastEl.classList.add('show');
            setTimeout(() => {
                toastEl.classList.remove('show');
            }, 3000);
        }
    </script>
</body>
</html>
