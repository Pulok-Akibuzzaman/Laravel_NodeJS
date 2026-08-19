const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const cors = require('cors');

const app = express();
const PORT = process.env.PORT || 3000;

// Enable CORS and JSON parsing
app.use(cors());
app.use(express.json());

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: '*',
        methods: ['GET', 'POST', 'PUT', 'DELETE']
    }
});

// Webhook endpoint called by Laravel API
app.post('/webhook', (req, res) => {
    const payload = req.body;
    console.log('Received webhook event from Laravel:', payload.event, payload.data ? payload.data.id : '');

    // Broadcast real-time update event to all connected Socket.io clients
    io.emit('taskUpdated', payload);

    return res.status(200).json({
        success: true,
        message: 'Event broadcasted to all connected clients',
        event: payload.event,
        connectedClients: io.engine.clientsCount
    });
});

// Root status page
app.get('/', (req, res) => {
    res.send(`
        <div style="font-family: system-ui, sans-serif; max-width: 600px; margin: 4rem auto; padding: 2rem; background: #0f172a; color: #f8fafc; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            <h2 style="color: #6366f1; margin-top: 0;">🚀 Node.js WebSocket Server Online</h2>
            <p style="color: #94a3b8; line-height: 1.5;">This service handles background WebSockets broadcasts (Socket.io) and listens for Laravel API webhooks at <code>POST /webhook</code>.</p>
            <div style="margin-top: 1.5rem; padding: 1rem; background: rgba(99, 102, 241, 0.1); border-radius: 8px; border: 1px solid rgba(99, 102, 241, 0.2);">
                <span style="display: block; font-weight: 600; color: #a5b4fc;">Connected Socket Clients: ${io.engine.clientsCount}</span>
            </div>
            <p style="margin-top: 1.5rem;">👉 Open the Task Manager Web App at: <a href="http://localhost:8000" style="color: #38bdf8; font-weight: 600; text-decoration: underline;">http://localhost:8000</a></p>
        </div>
    `);
});

// Health check endpoint
app.get('/health', (req, res) => {

    res.json({
        status: 'online',
        service: 'Task Management WebSocket Server',
        connectedClients: io.engine.clientsCount,
        timestamp: new Date().toISOString()
    });
});

// Socket.io Connection Handlers
io.on('connection', (socket) => {
    console.log(`[Socket.io] Client connected: ${socket.id}`);

    // Send acknowledgement to client
    socket.emit('connectionAck', {
        socketId: socket.id,
        message: 'Connected to Node.js Real-time Socket Server'
    });

    socket.on('disconnect', () => {
        console.log(`[Socket.io] Client disconnected: ${socket.id}`);
    });
});

server.listen(PORT, () => {
    console.log(`🚀 Node.js WebSocket Server listening on http://localhost:${PORT}`);
});
