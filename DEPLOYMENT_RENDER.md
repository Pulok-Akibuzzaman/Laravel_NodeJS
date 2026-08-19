# 🌐 Step-by-Step Render Deployment Guide

This guide will walk you through hosting your **Laravel + Node.js Real-Time Task System** on [Render.com](https://render.com) for **FREE**.

---

## 📋 Prerequisites
1. A free account on [Render.com](https://render.com).
2. A free account on [GitHub.com](https://github.com).
3. Git installed on your computer.

---

## Step 1: Push Project to GitHub

1. Open your terminal at the root of `Laravel_NodeJS`.
2. Initialize git and commit your files:
   ```bash
   git init
   git add .
   git commit -m "Initial commit of Laravel + Node.js project"
   ```
3. Create a **New Repository** on GitHub named `Laravel_NodeJS`.
4. Link and push your code to GitHub:
   ```bash
   git remote add origin https://github.com/YOUR_USERNAME/Laravel_NodeJS.git
   git branch -M main
   git push -u origin main
   ```

---

## Step 2: Deploy Node.js WebSockets Server on Render

1. Log in to [Render Dashboard](https://dashboard.render.com/).
2. Click **New +** ➔ Select **Web Service**.
3. Connect your GitHub repository (`Laravel_NodeJS`).
4. Configure the Web Service:
   - **Name**: `task-node-socket-server`
   - **Root Directory**: `nodejs-server`
   - **Runtime**: `Node`
   - **Build Command**: `npm install`
   - **Start Command**: `node server.js`
   - **Instance Type**: `Free`
5. Click **Create Web Service**.
6. Wait 1-2 minutes for Render to build. Once complete, copy your Node server URL (e.g. `https://task-node-socket-server.onrender.com`).

---

## Step 3: Deploy Laravel API Backend on Render

1. On Render Dashboard, click **New +** ➔ Select **Web Service**.
2. Select the same GitHub repository (`Laravel_NodeJS`).
3. Configure the Web Service:
   - **Name**: `task-laravel-api`
   - **Root Directory**: `laravel-app`
   - **Runtime**: `Docker` *(It will automatically detect the Dockerfile we created)*
   - **Instance Type**: `Free`
4. Scroll down to **Environment Variables** ➔ Click **Add Environment Variable**:
   - `NODE_SERVER_URL` = `https://task-node-socket-server.onrender.com/webhook` *(Paste your Node server URL from Step 2 followed by `/webhook`)*
   - `APP_KEY` = `base64:BFTo9esA7Ostf/KT4F+BiBvGxFtXIU5P7RygmaaC/8E=`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `true`
5. Click **Create Web Service**.
6. Once deployed, copy your Laravel API URL (e.g. `https://task-laravel-api.onrender.com`).

---

## Step 4: Update Frontend App URLs

1. Open `frontend/index.html` (and `laravel-app/resources/views/welcome.blade.php`).
2. Update lines 546-547 to point to your live Render URLs:
   ```javascript
   const LARAVEL_API_URL = 'https://task-laravel-api.onrender.com/api/tasks';
   const SOCKET_SERVER_URL = 'https://task-node-socket-server.onrender.com';
   ```
3. Commit and push the updated frontend code to GitHub:
   ```bash
   git add .
   git commit -m "Update frontend with live Render URLs"
   git push
   ```

---

## 🎉 Done! Your App is Live on the Internet!

- **Web Application URL**: Open `https://task-laravel-api.onrender.com` in two browser windows to test real-time WebSockets sync live!
