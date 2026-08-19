# 🚀 Real-Time Task Management System
### *Built with Laravel 11 + Node.js (Socket.io) + Vanilla HTML/CSS/JS*

Welcome! This beginner-friendly guide will help you understand, set up, and present this project step by step.

---

## 🧠 How It Works (In Easy Words)

Imagine a restaurant:

1. **Laravel (The Head Chef / Manager)** 🍳
   - Laravel handles all the main business: saving tasks, deleting tasks, updating tasks, and managing the SQLite database.
   - It is fast, secure, and smart with data.

2. **Node.js (The Megaphone / Broadcaster)** 📢
   - Every time Laravel saves or changes a task, Laravel sends a quick notification (Webhook) to Node.js.
   - Node.js instantly broadcasts a WebSockets message (via Socket.io) to **every connected browser window**:
     > *"Attention! A task was just created/updated!"*

3. **Frontend (The Customer Display)** 💻
   - The webpage (`http://localhost:8000`) listens to Node.js.
   - The moment Node.js shouts an update, the webpage updates the screen instantly—**without needing to refresh the page (F5)**!

---

## 🎨 Visual Architecture

```text
┌────────────────────────────────────────────────────────┐
│               Browser Window 1 & 2                     │
│             (http://localhost:8000)                    │
└───────────────┬────────────────────────▲───────────────┘
                │                        │
  1. Add/Update Task (REST API)     3. Instant Push (WebSockets)
                │                        │
                ▼                        │
┌────────────────────────┐      ┌────────────────────────┐
│     Laravel 11 API     │      │   Node.js WebSockets   │
│  (DB: SQLite - :8000)  │      │     Server (:3000)     │
└───────────────┬────────┘      └────────▲───────────────┘
                │                        │
                └────── 2. Webhook ──────┘
                     (POST /webhook)
```

---

## 🛠️ Step-by-Step Setup Guide

Follow these simple steps to run the complete project on your computer.

### Prerequisites (What you need installed)
- **PHP 8.2 or higher**
- **Node.js** (v18 or higher)
- **Composer** (PHP Package Manager)

---

### Step 1: Start the Node.js WebSocket Server

1. Open your terminal / command prompt.
2. Navigate to the `nodejs-server` directory:
   ```bash
   cd nodejs-server
   ```
3. Install dependencies (if running for the first time):
   ```bash
   npm install
   ```
4. Start the server:
   ```bash
   node server.js
   ```
   > ✅ You will see: `🚀 Node.js WebSocket Server listening on http://localhost:3000`

---

### Step 2: Start the Laravel API Server

1. Open a **second terminal window**.
2. Navigate to the `laravel-app` directory:
   ```bash
   cd laravel-app
   ```
3. Run database setup (if running for the first time):
   ```bash
   php artisan migrate
   ```
4. Start the Laravel development server:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```
   > ✅ You will see: `INFO Server running on [http://127.0.0.1:8000]`

---

### Step 3: Open the Web Application

1. Open your browser (Chrome, Edge, Firefox, etc.).
2. Go to: **[http://localhost:8000](http://localhost:8000)**

---

## 🎬 How to Test the Real-Time Sync (Live Demo)

To see the real-time magic in action:

1. Open **two browser windows side-by-side** at `http://localhost:8000`.
2. Notice the green **"Live Sync Active"** beacon at the top right.
3. In **Window 1**, type a task (e.g., *"Finish Slides"*) and click **Add Task**.
4. Look at **Window 2**—the task appears **instantly** without reloading!
5. Check the checkbox in Window 1 to mark it completed—Window 2 updates the counter instantly (*"1 of 1 tasks completed (100%)"*).

---

## 📁 Project Directory Structure

```text
Laravel_NodeJS/
│
├── laravel-app/                 # Laravel REST API & Database
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── TaskController.php   # REST endpoints + sends Webhook to Node.js
│   │   └── Models/
│   │       └── Task.php             # Eloquent Model for Task table
│   ├── database/
│   │   ├── migrations/              # Tasks table database structure
│   │   └── database.sqlite          # Lightweight SQLite database
│   ├── resources/views/
│   │   └── welcome.blade.php        # Task Manager Web Interface
│   └── routes/
│       └── api.php                  # API endpoints (/api/tasks)
│
├── nodejs-server/               # Real-Time WebSocket Server
│   ├── server.js                # Express server + Socket.io broadcaster
│   └── package.json             # Node dependencies (express, socket.io, cors)
│
└── frontend/                    # Standalone HTML Interface
    └── index.html               # Backup single-file web app
```

---

## 🎓 Presentation & Q&A Cheat Sheet (For Viva / Demo)

**Q1: Why didn't you build everything in Node.js?**
> *Answer:* Laravel has built-in database ORM, robust routing, request validation, and clean MVC structure. Node.js is lightweight and specifically designed for non-blocking WebSockets. Combining them gives us the best of both worlds.

**Q2: How do Laravel and Node.js talk to each other?**
> *Answer:* Laravel sends an HTTP POST Webhook request to Node.js at `http://127.0.0.1:3000/webhook` whenever a task is created, updated, or deleted.

**Q3: How does the browser get updates without refreshing?**
> *Answer:* The browser maintains an open WebSocket connection with Socket.io on Node.js. When Node.js receives a webhook from Laravel, it broadcasts an event to the browser, which updates the DOM live.

---

---

## 🌐 Free Online Deployment (Render.com)

If you want to host your project live on the internet, follow our dedicated step-by-step guide:  
📖 **[DEPLOYMENT_RENDER.md](file:///f:/EWU/CSE479/Project/Laravel_NodeJS/DEPLOYMENT_RENDER.md)**

---

🎉 **You are all set to present your project! Good luck!**
