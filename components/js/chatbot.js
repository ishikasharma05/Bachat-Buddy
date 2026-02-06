// Bachat Buddy Chatbot - Fixed and Enhanced Version
// This file handles the chatbot UI and interactions

console.log('💬 Chatbot script loaded');

// Toggle chatbot visibility
function toggleChat() {
    const chatBox = document.getElementById('bbChatBox');
    const chatToggle = document.getElementById('bbChatToggle');
    
    if (!chatBox) {
        console.error('Chat box not found!');
        return;
    }
    
    if (chatBox.classList.contains('d-none')) {
        // Open chat
        chatBox.classList.remove('d-none');
        chatToggle.innerHTML = '✖';
        console.log('💬 Chat opened');
    } else {
        // Close chat
        chatBox.classList.add('d-none');
        chatToggle.innerHTML = '💬';
        console.log('💬 Chat closed');
    }
}

// Send message function
function sendMessage() {
    const input = document.getElementById('bbChatInput');
    const chatBody = document.getElementById('bbChatBody');
    const message = input.value.trim();
    
    if (message === '') {
        return;
    }
    
    // Add user message to chat
    addMessage(message, 'user');
    
    // Clear input
    input.value = '';
    
    // Show typing indicator
    showTypingIndicator();
    
    // Send to backend
    fetch('backend/chatbot/chat_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        // Remove typing indicator
        removeTypingIndicator();
        
        // Add bot reply
        if (data.reply) {
            addMessage(data.reply, 'bot');
        }
        
        // Add follow-up questions if any
        if (data.followup && data.followup.length > 0) {
            setTimeout(() => {
                data.followup.forEach((question, index) => {
                    setTimeout(() => {
                        addMessage(question, 'bot');
                    }, index * 500);
                });
            }, 500);
        }
        
        // Show soft popup if needed
        if (data.soft_popup) {
            showSoftPopup(data.soft_popup);
        }
    })
    .catch(error => {
        console.error('Chat error:', error);
        removeTypingIndicator();
        addMessage('Oops! Something went wrong. Please try again 😅', 'bot');
    });
}

// Add message to chat
function addMessage(text, sender) {
    const chatBody = document.getElementById('bbChatBody');
    const messageDiv = document.createElement('div');
    
    if (sender === 'user') {
        messageDiv.className = 'bb-user-msg';
        messageDiv.innerHTML = `<strong>You:</strong> ${escapeHtml(text)}`;
    } else {
        messageDiv.className = 'bb-bot-msg';
        messageDiv.innerHTML = `<strong>Buddy:</strong> ${escapeHtml(text)}`;
    }
    
    chatBody.appendChild(messageDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Show typing indicator
function showTypingIndicator() {
    const chatBody = document.getElementById('bbChatBody');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'bb-bot-msg bb-typing';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = '<strong>Buddy:</strong> <span class="typing-dots"><span>.</span><span>.</span><span>.</span></span>';
    chatBody.appendChild(typingDiv);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Remove typing indicator
function removeTypingIndicator() {
    const typingIndicator = document.getElementById('typingIndicator');
    if (typingIndicator) {
        typingIndicator.remove();
    }
}

// Show soft popup notification
function showSoftPopup(message) {
    const popup = document.getElementById('bbSoftPopup');
    if (!popup) return;
    
    popup.textContent = message;
    popup.classList.remove('d-none');
    
    // Auto-hide after 5 seconds
    setTimeout(() => {
        popup.classList.add('d-none');
    }, 5000);
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Allow sending message with Enter key
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('bbChatInput');
    const chatToggle = document.getElementById('bbChatToggle');
    
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
    
    // Add click event to chat toggle button
    if (chatToggle) {
        chatToggle.addEventListener('click', toggleChat);
    }
    
    console.log('✅ Chatbot initialized successfully');
});