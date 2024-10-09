<?php
require 'session_start.php';
require 'db_connection.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            max-width: 600px;
        }
        .alert-container {
            margin-bottom: 20px;
        }
        .btn1{
            width: 100%;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
        }
        .card{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="alert-container"></div>
        <div class="card">
            <div class="card-header  text-white">
                <h5 class="card-title mb-0"><i class="fas fa-user-cog"></i> Register Admin User</h5>
            </div>
            <div class="card-body">
                <form id="registerForm">
                  <div class="form-group">
                        <label for="username">Username(email):</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="example@gmail.com">
                        <small style="color: red;">**Ensure User email matches their staff email to avoid conflict (unless its Master Admin)</small><br>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="text" class="form-control" id="password" name="password" required autocomplete="off">
                    </div>
                    <div class="form-group">
                      <label for="role">Role(<span style="color: red;">Portal</span>):</label>

                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="registrar">Registrar</option>
                            <option value="accounts">Accounts</option>
                            <option value="master admin">Master Admin</option>
                            <option value="quality_assurance">Quality Assurance</option>
                            <option value="principal">Principal</option>
                            <option value="security">Security</option>
                            <option value="front_desk">Front Desk</option>
                            <option value="librarian">Librarian</option>
                            <option value="helping_staff">Helping Staff</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <button  type="submit" class="btn btn-primary btn1">Save</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Handle form submission
            $('#registerForm').on('submit', function(event) {
                event.preventDefault(); // Prevent form submission

                var formData = $(this).serialize();
                
                $.ajax({
                    url: 'RegisterAdmin.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        var res = JSON.parse(response);
                        if (res.status === 'success') {
                            showAlert(res.message, 'success');
                            $('#registerForm')[0].reset(); // Clear the form
                        } else {
                            showAlert(res.message, 'danger');
                        }
                    },
                    error: function(xhr, status, error) {
                        showAlert('Failed to register user. Please try again.', 'danger');
                    }
                });
            });

            // Function to show Bootstrap alert
            function showAlert(message, type) {
                var alertHTML = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">';
                alertHTML += message;
                alertHTML += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
                alertHTML += '<span aria-hidden="true">&times;</span>';
                alertHTML += '</button></div>';
                $('.alert-container').html(alertHTML);
            }
        });
    </script>
</body>
</html>
