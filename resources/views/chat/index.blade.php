<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - RP-FYP Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
    .message-bubble {
        max-width: 70%;
        word-wrap: break-word;
    }

    .chat-container {
        height: calc(100vh - 320px);
        overflow-y: auto;
    }

    .active-supervisor {
        background-color: #e5edff;
        border-left: 4px solid #3b82f6;
    }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    @php
    $user_role = Session::get('user_role');
    @endphp

    <nav class="{{ $user_role == 'supervisor' ? 'bg-purple-700' : 'bg-blue-600' }} text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold">RP-FYP {{ ucfirst($user_role) }} Chat</h1>
            <div class="flex items-center space-x-4">
                <a href="{{ route($user_role . '.dashboard') }}"
                    class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-800">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </nav>
    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 p-2 rounded mb-3 text-sm">
        @foreach ($errors->all() as $error)
        <div>{{ $error }}</div>
        @endforeach
    </div>
    @endif
    <main class="container mx-auto py-8 px-4 flex-grow">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b p-4 bg-gray-50">
                <h2 class="text-lg font-semibold">{{ $project->project_name }}</h2>
                <div class="flex justify-between">
                    <p class="text-sm text-gray-600">
                        <span class="font-medium">Student:</span> {{ $student->student_first_name }}
                        {{ $student->student_last_name }}
                    </p>
                </div>
            </div>

            <div class="flex h-full">
                @if($user_role == 'student' && $supervisors->count() > 1)
                <!-- Supervisor List for Students - Only show if multiple supervisors -->
                <div class="w-1/4 border-r">
                    <div class="p-3 bg-gray-50 border-b">
                        <h3 class="font-medium">Your Supervisors</h3>
                    </div>
                    <div class="overflow-y-auto" style="max-height: calc(100vh - 320px);">
                        @foreach($supervisors as $sup)
                        <a href="{{ route('chat.index', ['project_code' => $project->project_code, 'receiver_email' => $sup->supervisor_email]) }}"
                            class="block p-3 border-b hover:bg-gray-50 {{ $active_receiver && $active_receiver->supervisor_email == $sup->supervisor_email ? 'active-supervisor' : '' }}">
                            <p class="font-medium">{{ $sup->supervisor_first_name }} {{ $sup->supervisor_last_name }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $sup->supervisor_email }}</p>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Chat Area -->
                <div class="{{ ($user_role == 'student' && $supervisors->count() > 1) ? 'w-3/4' : 'w-full' }}">
                    <div class="p-2 bg-gray-50 border-b">
                        @if($user_role == 'supervisor')
                        <p class="text-sm font-medium">Chatting with: {{ $student->student_first_name }}
                            {{ $student->student_last_name }}
                        </p>
                        @elseif($active_receiver)
                        <p class="text-sm font-medium">Chatting with: {{ $active_receiver->supervisor_first_name }}
                            {{ $active_receiver->supervisor_last_name }}
                        </p>
                        @endif
                    </div>

                    <div class="chat-container p-4 bg-gray-100" id="chat-container">
                        @if($messages->count() > 0)
                        @foreach($messages as $message)
                        <div
                            class="mb-4 flex {{ $message->sender_email == $current_user_email ? 'justify-end' : 'justify-start' }}">
                            <div
                                class="message-bubble rounded-lg p-3 {{ $message->sender_email == $current_user_email ? 'bg-blue-500 text-white' : 'bg-white border' }}">
                                <p>{{ $message->message_body }}</p>
                                <p
                                    class="text-xs {{ $message->sender_email == $current_user_email ? 'text-blue-100' : 'text-gray-500' }} mt-1">
                                    {{ \Carbon\Carbon::parse($message->timestamp)->format('M d, Y h:i A') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-8 text-gray-500" id="no-messages">
                            No messages yet. Start the conversation!
                        </div>
                        @endif
                    </div>

                    <div class="p-4 border-t">
                        @if(($user_role == 'student' && $active_receiver) || $user_role == 'supervisor')
                        <form id="message-form" class="flex space-x-2">
                            @csrf
                            <input type="hidden" name="project_code" value="{{ $project->project_code }}"
                                id="project-code">
                            <input type="hidden" name="receiver_email"
                                value="{{ $active_receiver->supervisor_email ?? $student->student_email }}"
                                id="receiver-email">
                            <input type="hidden" id="last-message-id"
                                value="{{ $messages->count() > 0 ? $messages->last()->message_id : 0 }}">


                            @if($user_role == 'student' && $active_receiver)
                            <input type="hidden" name="receiver_email" value="{{ $active_receiver->supervisor_email }}"
                                id="receiver-email">
                            @elseif($user_role == 'supervisor')
                            <input type="hidden" name="receiver_email" value="{{ $student->student_email }}"
                                id="receiver-email">
                            @endif

                            <input type="text" name="message_body" id="message-body" placeholder="Type your message..."
                                class="flex-grow border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400"
                                required>
                            <button type="submit" id="send-button"
                                class="{{ $user_role == 'supervisor' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-4 py-2 rounded-lg">
                                Send
                            </button>
                        </form>
                        @else
                        <p class="text-center text-gray-500">Please select a supervisor to chat with.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white p-4 mt-auto">
        <div class="container mx-auto text-center">
            <p>&copy; 2025 RP-FYP Management System</p>
        </div>
    </footer>

    <script>
    $(document).ready(function() {
        console.log("Document ready");

        // Check if the form exists
        console.log("Form exists:", $('#message-form').length > 0);

        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Get variables
        const chatContainer = document.getElementById('chat-container');
        let lastMessageId = $("#last-message-id").val() || 0;
        const projectCode = $('#project-code').val();
        const receiverEmail = $('#receiver-email').val();

        console.log("Project Code:", projectCode);
        console.log("Receiver Email:", receiverEmail);
        console.log("Last Message ID:", lastMessageId);

        // Scroll to bottom of chat container on load
        scrollToBottom();

        // Send message via AJAX
        $('#message-form').submit(function(e) {
            console.log("Form submitted");
            e.preventDefault(); // This prevents the default form submission

            const messageBody = $('#message-body').val();
            if (!messageBody.trim()) return;

            console.log("Sending message:", messageBody);
            $('#send-button').prop('disabled', true);

            $.ajax({
                url: '/chat/send',
                type: 'POST',
                data: {
                    message_body: messageBody,
                    project_code: projectCode,
                    receiver_email: receiverEmail
                },
                success: function(response) {
                    console.log("Message sent successfully:", response);
                    // Clear input field and re-enable button
                    $('#message-body').val('');
                    $('#send-button').prop('disabled', false);
                    $('#message-body').focus();

                    // Add this to see the message immediately rather than waiting for polling
                    if (response.success) {
                        const message = response.message;
                        const messageHTML = `
                        <div class="mb-4 flex justify-end">
                            <div class="message-bubble rounded-lg p-3 bg-blue-500 text-white">
                                <p>${message.message_body}</p>
                                <p class="text-xs text-blue-100 mt-1">
                                    ${formatTimestamp(message.timestamp)}
                                </p>
                            </div>
                        </div>
                    `;
                        $(chatContainer).append(messageHTML);
                        lastMessageId = message.message_id;
                        scrollToBottom();
                        $('#no-messages').remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error sending message:", xhr.responseText);
                    alert('Error sending message. Please try again.');
                    $('#send-button').prop('disabled', false);
                }
            });
        });

        // Poll for new messages every 3 seconds
        function pollMessages() {
            if (!projectCode || !receiverEmail) {
                console.log("Missing project code or receiver email, skipping poll");
                return;
            }

            $.ajax({
                url: '/chat/get-messages',
                type: 'POST', // Changed from GET to POST
                data: {
                    project_code: projectCode,
                    receiver_email: receiverEmail,
                    last_message_id: lastMessageId
                },
                success: function(response) {
                    if (response.messages && response.messages.length > 0) {
                        console.log("New messages received:", response.messages.length);
                        // Remove "no messages" div if it exists
                        $('#no-messages').remove();

                        // Append new messages
                        response.messages.forEach(function(message) {
                            const isCurrentUser = message.sender_email === response
                                .current_user_email;
                            const messageHTML = `
                            <div class="mb-4 flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                                <div class="message-bubble rounded-lg p-3 ${isCurrentUser ? 'bg-blue-500 text-white' : 'bg-white border'}">
                                    <p>${message.message_body}</p>
                                    <p class="text-xs ${isCurrentUser ? 'text-blue-100' : 'text-gray-500'} mt-1">
                                        ${formatTimestamp(message.timestamp)}
                                    </p>
                                </div>
                            </div>
                        `;
                            $(chatContainer).append(messageHTML);
                            lastMessageId = message.message_id;
                        });

                        // Scroll to bottom
                        scrollToBottom();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error polling messages:", xhr.responseText);
                }
            });
        }

        // Format timestamp
        function formatTimestamp(timestamp) {
            const date = new Date(timestamp);
            const options = {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: 'numeric',
                hour12: true
            };
            return date.toLocaleDateString('en-US', options);
        }

        // Scroll to bottom function
        function scrollToBottom() {
            if (chatContainer) {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            }
        }

        // Start polling
        setInterval(pollMessages, 3000);

        // Focus on message input
        $('#message-body').focus();
    });
    </script>
</body>

</html>