<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mail Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
         .container {
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-radius: 8px;
        }
        .message-list-container {
            background-color: #73c2fb;
            color: white;
            padding: 10px;
            border-radius: 8px;
            max-height: 450px;
        }
        .message-enclose-container {
            max-height: 300px;
            overflow-y: auto;
            background-color: #ffffff;
            border-radius:3px;
        }
        .message-content-container {
            background-color: #bcd4e6;
            color: black;
            padding: 20px;
            border-radius: 8px; 
            min-height: 450px;
            max-height:500px;
        }
        .message-content {
            background-color: #fdfff5;
            color: black;
            height: 270px;
            border-radius: 10px;
            padding: 25px;
            overflow-y: auto;
        }
        .reply-delete-buttons {
            text-align: center;
            margin-top: 20px;
        }
        .list-group-item {
            color: black;
            background-color: #ffffff;
            border: none;
        }
        .list-group-item .message-indicator {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color:#a9a9a9;
            color: white;
            text-align: center;
            line-height: 15px;
        }
        .list-group-item.unread {
            background-color: #f2f3f4;
               font-weight: bold;
               color:blue;
        }
        .list-group-item.unread .message-indicator {
            background-color: #ff6347;
            color:#ff6347;
        }

        .buttons {
    margin-top: 5px;
    margin-bottom: 10px;
    white-space: nowrap; /* Prevent line breaks */
       width:100%;
    }

    .buttons button {
        height: 40px;
        margin-right: 5px; 
        display: inline-block;
        font-size: 15px;
        font-weight:bold;
        vertical-align: middle;
    }
        #btnRefreshMessages {
          display: none;
          width:100%;
        }
        #btnDelete{
               width:50%;
        }
         #btnUnreadMessages{
             width:100%;
             background-color:#ffc87c;
         }
    </style>
</head>
<body>
    <div class="container mt-3">
        <div class="row">
            <div class="col-md-4 message-list-container">
                <h3>Inbox <i class="fas fa-envelope"></i> <span id="unread-count" class="badge badge-danger"></span></h3>
                <div class="message-enclose-container">
                    <div class="list-group mt-4" id="message-list">

                    </div>
                </div>
                <div class="buttons mt-3">
                    <button class="btn " id="btnUnreadMessages">Unread messages</button>
                    <button class="btn btn-warning" id="btnRefreshMessages"><i class="fas fa-redo-alt"></i> Refresh</button>
                </div>
            </div>
            <div class="col-md-8">
                <div class="message-content-container">
                    <div>
                        <label style="font-size: 16px; font-weight: bold; color:blue;" for="message-subject">Subject:</label>
                        <span id="message-subject"></span>
                    </div>
                    <div>
                        <label for="message-from" style="font-size: 16px; font-weight: bold; color:blue;">From:</label>
                        <span id="message-from"></span>
                    </div>
                    <div class="message-content mt-3">
                        <p id="message-content"></p>
                    </div>
                    <div class="reply-delete-buttons">
                        <button class="btn btn-danger" id="btnDelete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
 $(document).ready(function() {
    let currentMessageId = null; // Track the currently opened message ID

    // Fetch all messages by default
    fetchMessages('./fetch_messages.php');

    $('#btnUnreadMessages').on('click', function() {
        fetchMessages('get_unread_messages.php');
        $('#btnRefreshMessages').css('display', 'block');
        $('#btnUnreadMessages').css('display', 'none');
    });

    $('#btnRefreshMessages').on('click', function() {
        fetchMessages('get_unread_messages.php');
    });

    // Delegate event for dynamically loaded .message-row elements
    $(document).on('click', '.message-row', function(event) {
        event.preventDefault();
        const messageId = $(this).data('id');
        currentMessageId = messageId; // Store the ID of the currently opened message

        fetch('./get_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                message_id: messageId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error
                });
            } else {
                $('#message-subject').text(data.subject);
                $('#message-from').text(data.sender);
                $('#message-content').text(data.message);

                // Find the message row by its data-id and check if it's unread
                const messageRow = $(`[data-id=${messageId}]`);

                if (messageRow.hasClass('unread')) {
                    // Remove the unread class if it's there
                    messageRow.removeClass('unread');

                    // Update the unread count only if it was unread
                    let unreadCount = parseInt($('#unread-count').text());

                    if (!isNaN(unreadCount) && unreadCount > 0) {
                        unreadCount -= 1;
                    } else {
                        unreadCount = 0; // Ensure it doesn't go negative
                    }

                    $('#unread-count').text(unreadCount > 0 ? unreadCount : ''); // Update display
                }

                // Update message status to read
                fetch('./update_message_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        message_id: messageId,
                        status: 'read'
                    })
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error fetching message details. Please try again later.'
            });
        });
    });

    // When the delete button is clicked
    $('#btnDelete').on('click', function() {
        if (!currentMessageId) {
            Swal.fire({
                icon: 'warning',
                title: 'No Message Selected',
                text: 'Please select a message to delete.'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const requestBody = new URLSearchParams({
                    message_id: currentMessageId
                });

                fetch('./delete_message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: requestBody
                })
                .then(response => response.json())
                .then(data => {
                   if (data.success) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Message has been deleted.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 1000 
                    });
                    fetchMessages('fetch_messages.php');
                    $('#message-subject').text('');
                    $('#message-from').text('');
                    $('#message-content').text('');
                    currentMessageId = null; // Reset the current message ID
                }else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete message.'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error deleting message:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error deleting message. Please try again later.'
                    });
                });
            }
        });
    });

    // Fetch messages function
    function fetchMessages(url) {
        fetch(url)
            .then(response => response.json())
            .then(messages => {
                if (!Array.isArray(messages)) {
                    console.error('Error: Expected an array but received:', messages);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error fetching messages. Please try again later.'
                    });
                    return;
                }

                const messageList = document.getElementById('message-list');
                messageList.innerHTML = ''; // Clear previous messages

                if (messages.length === 0) {
                    messageList.innerHTML = `
                        <div class='list-group-item'>
                            No messages found.
                        </div>`;
                    document.getElementById('unread-count').textContent = '';
                    return;
                }

                let unreadCount = 0;

                messages.forEach(message => {
                    const unreadClass = message.status === 'unread' ? 'unread' : '';

                    messageList.innerHTML += `
                    <a href='#' class='list-group-item list-group-item-action message-row ${unreadClass}' data-id='${message.id}'>
                        <span class="message-indicator">${unreadClass === 'unread' ? '<i class="fas fa-circle"></i>' : ''}</span>
                        ${message.subject}
                    </a>`;

                    if (message.status === 'unread') {
                        unreadCount++;
                    }
                });

                document.getElementById('unread-count').textContent = unreadCount > 0 ? unreadCount : '';
            })
            .catch(error => {
                console.error('Error fetching messages:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error fetching messages. Please try again later.'
                });
            });
    }
});
</script>
</body>
</html>
