<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];
$stmt = $conn->prepare("SELECT name,picture,phone FROM staff WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $staff = $result->fetch_assoc();
    $staff_name = $staff['name'];
    $phone = $staff['phone'];
    $picture = $staff['picture'] ? $staff['picture'] : 'default_image.png';
} else {
    $staff_name = "Unknown";
    $phone = "";
    $picture = 'default_image.png';
}


$stmt2 = $conn->prepare("SELECT role FROM staffLogin WHERE username = ?");
$stmt2->bind_param("s", $email);
$stmt2->execute();
$result2 = $stmt2->get_result();

if ($result2->num_rows === 1) {
     $staffs = $result2->fetch_assoc();
     $role = $staffs['role'];
} else {
    $picture = 'image.jpg';
}


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Details and Password Update</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .profile-picture {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
            padding:2px;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
        }
        .card-body .btn{
            width:100%;
            padding:10px;
            font-weight:bold;
        }
        .card-body{
            color:blue;
            font-weight:bold;
        }
        .card-body h5{
            color:white;
            font-weight:bold;
            text-align:center;
            background-color: #343a40;
            padding:10px;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-white">
                <h5 class="card-title mb-0"><i class="fas fa-user"></i> My User Profile</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                    <img src="<?php echo '../StaffImages/' . $picture; ?>" alt="Profile Picture" class="profile-picture" id="profilePicture">
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="email">Email(Username):</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo $_SESSION['username']; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="role">Role(Portal):</label>
                            <input type="text" id="role" name="role" class="form-control" value="<?php echo $role; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="role">Name:</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $staff_name ; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="role">Phone No:</label>
                            <input type="text" id="role" name="role" class="form-control" value="<?php echo $phone; ?>" disabled>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-md-12">
                        <h5><i class="fas fa-lock"></i> Update Password</h5>
                        <form id="updatePasswordForm">
                            <div class="form-group">
                                <label for="oldPassword">Old Password:</label>
                                <input type="password" id="oldPassword" name="oldPassword" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="newPassword">New Password:</label>
                                <input type="password" id="newPassword" name="newPassword" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#updatePasswordForm').on('submit', function(event) {
                event.preventDefault();
                let oldPassword = $('#oldPassword').val();
                let newPassword = $('#newPassword').val();
                
                $.ajax({
                    url: 'update_admin_password.php',
                    type: 'POST',
                    data: {
                        oldPassword: oldPassword,
                        newPassword: newPassword
                    },
                    success: function(response) {
                        alert('Password updated successfully!');
                        $('#updatePasswordForm')[0].reset();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error updating password:', error);
                        alert('Error updating password. Please try again.');
                    }
                });
            });
        });
    </script>
</body>
</html>
