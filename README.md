# 🚀 Real-Time Task Management System
### *Built with Laravel 11 + Node.js (Socket.io) + Vanilla HTML/CSS/JS*

Welcome! This is the complete, all-in-one guide for the **Simple Real-Time Task Management System**. It covers how the architecture works, how to run it locally, how to deploy it live on Render, and presentation Q&A.

---

## 🌐 Live Deployed Application

- 💻 **Web Application UI & API**: [https://task-laravel-api.onrender.com/](https://task-laravel-api.onrender.com/)
- 📢 **Node.js WebSockets Server**: [https://laravel-nodejs.onrender.com/](https://laravel-nodejs.onrender.com/)


---

## 🧠 How It Works (In Easy Words)

Think of the system like a restaurant team:

1. **Laravel (The Manager / Head Chef)** 🍳
   - Handles saving tasks, updating status, deleting tasks, and managing the SQLite database.
   - Fast, secure, and structured.

2. **Node.js (The Megaphone / Broadcaster)** 📢
   - Every time Laravel modifies a task, Laravel dispatches an HTTP Webhook POST request to Node.js.
   - Node.js instantly shouts a WebSockets message (via Socket.io) to **every open browser window**:
     > *"Attention! A task was just updated!"*

3. **Frontend (The Customer Screen)** 💻
   - The web app listens to Node.js and updates the task list and completion counter **live on screen without refreshing (F5)**.

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

## 💻 Local Setup & Running Guide

### Prerequisites
- **PHP 8.2 or higher**
- **Node.js** (v18 or higher)
- **Composer** (PHP Package Manager)

---

### Step 1: Start Node.js WebSockets Server

Open a terminal window and run:

```bash
cd nodejs-server
npm install
node server.js
```
> ✅ Serves on `http://localhost:3000`

---

### Step 2: Start Laravel API Server

Open a second terminal window and run:

```bash
cd laravel-app
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```
> ✅ Serves on `http://127.0.0.1:8000`

---

### Step 3: Open the Web Application

1. Open your browser and visit: **[http://localhost:8000](http://localhost:8000)**.
2. Open **two browser windows side-by-side** to test real-time WebSockets synchronization live!

---

## 🌐 Free Online Deployment Guide (Render.com)

Follow these steps to deploy both servers live on [Render.com](https://render.com) for free:

### 1. Push Latest Code to GitHub
```bash
git add .
git commit -m "Deploy project to Render"
git push
```

### 2. Deploy Node.js WebSockets Server on Render
1. Go to [Render Dashboard](https://dashboard.render.com/) ➔ Click **New +** ➔ **Web Service**.
2. Connect your GitHub repository (`Laravel_NodeJS`).
3. Settings:
   - **Name**: `task-node-socket-server`
   - **Root Directory**: `nodejs-server`
   - **Runtime**: `Node`
   - **Build Command**: `npm install`
   - **Start Command**: `node server.js`
   - **Instance Type**: `Free`
4. Click **Create Web Service**. Copy the generated URL (e.g. `https://task-node-socket-server.onrender.com`).

### 3. Deploy Laravel API Backend on Render
1. On Render Dashboard ➔ Click **New +** ➔ **Web Service**.
2. Select your repository (`Laravel_NodeJS`).
3. Settings:
   - **Name**: `task-laravel-api`
   - **Root Directory**: *(Leave blank)*
   - **Runtime**: Select **`Docker`**
   - **Instance Type**: `Free`
4. Scroll to **Environment Variables** ➔ Add:
   - `NODE_SERVER_URL` = `https://task-node-socket-server.onrender.com/webhook` *(your Node URL + `/webhook`)*
   - `APP_KEY` = `base64:BFTo9esA7Ostf/KT4F+BiBvGxFtXIU5P7RygmaaC/8E=`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `true`
5. Click **Create Web Service**.

---

## 📁 Project Directory Structure

```text
Laravel_NodeJS/
│
├── Dockerfile                   # Docker build instructions for Render
├── README.md                    # Single master documentation file
│
├── laravel-app/                 # Laravel REST API & Database
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── TaskController.php   # REST endpoints + Webhook to Node.js
│   │   └── Models/
│   │       └── Task.php             # Eloquent Model for Task table
│   ├── database/
│   │   ├── migrations/              # Tasks table schema
│   │   └── database.sqlite          # SQLite database file
│   ├── resources/views/
│   │   └── welcome.blade.php        # Task Manager Web Interface
│   └── routes/
│       └── api.php                  # API endpoints (/api/tasks)
│
├── nodejs-server/               # Real-Time WebSocket Server
│   ├── server.js                # Express server + Socket.io broadcaster
│   └── package.json             # Node dependencies
│
└── frontend/                    # Standalone HTML Interface
    └── index.html               # Backup single-file web app
```

---

## 🎓 Presentation & Viva Cheat Sheet

**Q1: Why combine Laravel and Node.js?**
> *Answer:* Laravel provides structure, ORM database management, and API route validation. Node.js is asynchronous and non-blocking, making it ideal for persistent WebSockets connections. Combining them gives us the best of both worlds.

**Q2: How do Laravel and Node.js communicate?**
> *Answer:* Laravel sends an HTTP POST Webhook request to Node.js (`/webhook`) whenever a task is created, updated, or deleted.

**Q3: How does the browser update without refreshing?**
> *Answer:* The browser maintains an open Socket.io WebSocket connection to Node.js. When Node.js receives a webhook from Laravel, it broadcasts an event to the browser, updating the screen live.

---

🎉 **You are all set! Everything is documented in this single README.md.**
