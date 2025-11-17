<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Chatbot UI</title>

<style>
    body {
        margin: 0;
        background: #f2f2f2;
        font-family: "Poppins", sans-serif;
    }

    /* Floating button */
    .chatbot-btn {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 65px;
        height: 65px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        font-size: 30px;
        cursor: pointer;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        transition: 0.3s;
        z-index: 999;
    }

    .chatbot-btn:hover {
        transform: scale(1.1);
    }

    /* Chat window */
    .chat-window {
        position: fixed;
        bottom: 100px;
        right: 25px;
        width: 330px;
        height: 420px;
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.25);
        transform: scale(0);
        opacity: 0;
        transition: 0.3s;
        overflow: hidden;
        z-index: 999;
    }

    .chat-window.active {
        transform: scale(1);
        opacity: 1;
    }

    /* Header */
    .chat-header {
        padding: 15px;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: white;
        text-align: center;
        font-size: 20px;
        font-weight: bold;
    }

    /* Messages */
    .chat-body {
        height: 300px;
        overflow-y: auto;
        padding: 15px;
    }

    .msg {
        max-width: 80%;
        padding: 10px 14px;
        margin: 8px 0;
        border-radius: 15px;
        font-size: 14px;
        animation: fadeIn 0.3s ease;
    }

    .user-msg {
        background: #2575fc;
        color: #fff;
        margin-left: auto;
        border-bottom-right-radius: 0;
    }

    .bot-msg {
        background: #fff;
        color: #333;
        border-bottom-left-radius: 0;
        border: 1px solid #ddd;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Input */
    .chat-input {
        display: flex;
        padding: 10px;
        background: #fff;
    }

    .chat-input input {
        flex: 1;
        padding: 10px;
        border: none;
        outline: none;
        font-size: 14px;
        border-radius: 10px;
        background: #f4f4f4;
    }

    .chat-input button {
        margin-left: 10px;
        padding: 10px 15px;
        border: none;
        background: #2575fc;
        color: white;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.2s;
    }

    .chat-input button:hover {
        background: #1c5fd6;
    }

</style>
</head>
<body>

<!-- Floating Button -->
<div class="chatbot-btn" id="chatbotBtn">💬</div>

<!-- Chat Window -->
<div class="chat-window" id="chatWindow">
    <div class="chat-header">Chat Support</div>
    <div class="chat-body" id="chatBody"></div>

    <div class="chat-input">
        <input type="text" id="userInput" placeholder="Type a message..." />
        <button onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
    // Toggle Chat Window
    document.getElementById("chatbotBtn").addEventListener("click", function () {
        document.getElementById("chatWindow").classList.toggle("active");
    });

    function sendMessage() {
        let input = document.getElementById("userInput");
        let text = input.value.trim();
        if (text === "") return;

        // User message
        appendMessage(text, "user");

        input.value = "";

        // Bot reply with delay
        setTimeout(() => {
            let reply = getBotReply(text);
            appendMessage(reply, "bot");
        }, 600);
    }

    function appendMessage(msg, type) {
        let chatBody = document.getElementById("chatBody");

        let div = document.createElement("div");
        div.classList.add("msg");
        div.classList.add(type === "user" ? "user-msg" : "bot-msg");
        div.innerText = msg;

        chatBody.appendChild(div);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // Simple bot logic
    function getBotReply(input) {
        input = input.toLowerCase();

        if (input.includes("hello")) return "Hello! How can I help you today?";
        if (input.includes("budget")) return "I can help you track and manage your budget!";
        if (input.includes("thank")) return "Happy to help! 😊";

        return "Sorry, I didn't understand. Please ask something else!";
    }
</script>

</body>
</html>
