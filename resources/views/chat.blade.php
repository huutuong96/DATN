<div class="chat-container">
    <div class="chat-header">
        <h2>Chat</h2>
    </div>
    <div class="chat-messages" id="chat-messages">
        <!-- Messages will be dynamically inserted here -->
    </div>
    <div class="chat-input">
        <input type="text" id="message-input" placeholder="Type your message...">
        <button id="send-button">Send</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const chatMessages = document.getElementById('chat-messages');

    sendButton.addEventListener('click', sendMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const message = messageInput.value.trim();
        if (message) {
            // Here you would typically send the message to your server
            // For now, we'll just append it to the chat
            const messageElement = document.createElement('div');
            messageElement.textContent = message;
            messageElement.classList.add('message', 'sent');
            chatMessages.appendChild(messageElement);
            messageInput.value = '';
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }
});
</script>

<style>
.chat-container {
    width: 300px;
    border: 1px solid #ccc;
    border-radius: 5px;
    overflow: hidden;
}
.chat-header {
    background-color: #f1f1f1;
    padding: 10px;
    text-align: center;
}
.chat-messages {
    height: 300px;
    overflow-y: auto;
    padding: 10px;
}
.chat-input {
    display: flex;
    padding: 10px;
}
#message-input {
    flex-grow: 1;
    padding: 5px;
}
#send-button {
    padding: 5px 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}
.message {
    margin-bottom: 10px;
    padding: 5px;
    border-radius: 5px;
}
.sent {
    background-color: #e1ffc7;
    text-align: right;
}
</style>
