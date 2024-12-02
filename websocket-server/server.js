const express = require("express");
const { createServer } = require("http");
const { Server } = require("socket.io");
const cors = require("cors");

const app = express();
app.use(cors()); // Cho phép mọi kết nối

const httpServer = createServer(app);
const io = new Server(httpServer, {
    cors: {
        origin: "*", // Cho phép mọi client
        methods: ["GET", "POST"]
    }
});

let onlineUsers = 0;

io.on("connection", (socket) => {
    console.log("Client connected");
    onlineUsers++;

    // Gửi số lượng người dùng đang online
    io.emit("user-online", { onlineCount: onlineUsers });

    socket.on("disconnect", () => {
        console.log("Client disconnected");
        onlineUsers--;
        io.emit("user-online", { onlineCount: onlineUsers });
    });
});

const PORT = 6001;
httpServer.listen(PORT, () => {
    console.log(`WebSocket server is running on http://localhost:${PORT}`);
});
