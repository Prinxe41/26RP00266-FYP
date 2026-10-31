// Function to toggle chat box visibility
function toggleChatBox(projectCode) {
    const chatBox = document.getElementById(`chat-box-${projectCode}`);
    if (chatBox.classList.contains('hidden')) {
        chatBox.classList.remove('hidden');
        loadMessages(projectCode);
    } else {
        chatBox.classList.add('hidden');
    }
}

// Function to load messages
function loadMessages(projectCode) {
    const messagesContainer = document.getElementById(`chat-messages-${projectCode}`);

    fetch(`/chat/messages/${projectCode}`)
        .then(response => response.json())
        .then(data => {
            const messages = data.messages;
            const userEmail = data.user_email;

            let messagesHtml = '';
            if (messages.length === 0) {
                messagesHtml = '<div class="text-center text-gray-500 text-sm">No messages yet. Start a conversation!</div>';
            } else {
                messages.forEach(message => {
                    const isCurrentUser = message.sender_email === userEmail;
                    messagesHtml += `
                        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-xs lg:max-w-md p-3 rounded-lg ${isCurrentUser ? 'bg-blue-500 text-white' : 'bg-gray-200'}">
                                <p>${message.message_body}</p>
                                <p class="text-xs mt-1 ${isCurrentUser ? 'text-blue-100' : 'text-gray-500'}">${new Date(message.timestamp).toLocaleTimeString()}</p>
                            </div>
                        </div>
                    `;
                });
            }

            messagesContainer.innerHTML = messagesHtml;
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        })
        .catch(error => {
            console.error('Error loading messages:', error);
            messagesContainer.innerHTML = '<div class="text-center text-red-500 text-sm">Error loading messages. Please try again.</div>';
        });
}

// Function to handle message submission
document.addEventListener('DOMContentLoaded', function () {
    const chatForms = document.querySelectorAll('[id^="chat-form-"]');

    chatForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const projectCode = this.querySelector('input[name="project_code"]').value;
            const messageInput = this.querySelector('input[name="message_body"]');
            const receiverEmail = this.querySelector('input[name="receiver_email"]').value;

            if (messageInput.value.trim() === '') return;

            const formData = {
                project_code: projectCode,
                message_body: messageInput.value,
                receiver_email: receiverEmail
            };

            fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        messageInput.value = '';
                        loadMessages(projectCode);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                });
        });
    });
});

// Poll for new messages every 5 seconds
function startPolling() {
    const activeChatBoxes = document.querySelectorAll('[id^="chat-box-"]:not(.hidden)');

    activeChatBoxes.forEach(chatBox => {
        const projectCode = chatBox.id.replace('chat-box-', '');
        loadMessages(projectCode);
    });

    setTimeout(startPolling, 5000);
}

document.addEventListener('DOMContentLoaded', startPolling);