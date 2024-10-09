<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <style>
        .con{
            max-width:900px;
            border-radius:5px;
        }
        .card{
            border-radius:5px;
        }
        .card h2{
             border-top-left-radius:5px;
              border-top-right-radius:5px;
        }
        </style>
</head>
<body>
    <div class="container con">
        <div class="card mt-4">
            <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white; background-color:#36454f;padding:10px; text-align:center;" class="card-title">
            <i class="fa-solid fa-comment" style="color: white; font-size: 20px;"></i>
            Send Message
            </h2>
            <div class="card-body">
                <form id="sendMessageForm" style="color:blue; font-weight:bold;">
                    <div class="form-group">
                        <label for="from">From:</label>
                        <input type="text" class="form-control" id="from" name="from" required placeholder="Enter name and regNumber">
                    </div>
                    <div class="form-group">
                        <label for="receiver">Receiver:</label>
                        <select class="form-control" id="receiver" name="receiver" required>
                            <option value="">Select Receiver</option>
                            <?php
                            require 'db_connection.php';
                            $sql = "SELECT id, name, role FROM staff";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='{$row['id']}'>{$row['name']} ({$row['role']})</option>";
                                }
                            }

                            $conn->close();
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message / Query:</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button style="width:100%; padding:10px; font-weight:bold;" type="submit" class="btn btn-primary">Send Message</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission using AJAX
            $('#sendMessageForm').submit(function(e) {
                e.preventDefault(); // Prevent normal form submission

                // Serialize form data
                var formData = $(this).serialize();

                // AJAX request
                $.ajax({
                    type: 'POST',
                    url: 'sendMessage.php',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Message Sent',
                                text: response.message
                            }).then(function() {
                               // location.reload(); 
                                $('#sendMessageForm')[0].reset();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while sending the message.'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
