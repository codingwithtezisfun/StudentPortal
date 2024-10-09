<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 10px;
            color:blue;
            font-weight:bold;
        }
        .button{
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card form-container">
                    <div style ="background-color: #343a40; border-radius:4px;" class="card-header text-center">
                        <h2 style="font-family: Arial, sans-serif; font-size: 24px; color:white">
                            <i class="fas fa-user-tie" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                            Staff Registration
                        </h2>
                    </div>
                    <div class="card-body">
                         <div id="response-message" class="mt-3"></div>
                        <form id="staff-form" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name:</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <small style="color: red;">**Ensure staff email matches their login email if they have an account to avoid conflict</small>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number:</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="role"  class="form-label">Role(<span style="color: red;">School</span>):</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select Role</option>
                                    <option value="lecturer">Lecturer</option>
                                    <option value="registrar">Registrar</option>
                                    <option value="accounts">Accounts</option>
                                    <option value="quality_assurance">Quality Assurance</option>
                                    <option value="principal">Principal</option>
                                    <option value="security">Security</option>
                                    <option value="front_desk">Front Desk</option>
                                    <option value="librarian">Librarian</option>
                                    <option value="helping_staff">Helping Staff</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="picture" class="form-label">Picture:</label>
                                <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn button btn-primary">Register Staff</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#staff-form').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'register_staff.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        let res = JSON.parse(response);
                        if (res.status === 'success') {
                            $('#response-message').html('<div class="alert alert-success">' + res.message + '</div>');
                            $('#staff-form')[0].reset();
                        } else {
                            $('#response-message').html('<div class="alert alert-danger">' + res.message + '</div>');
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#response-message').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
