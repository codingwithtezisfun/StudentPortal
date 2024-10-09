<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];
$sql = "SELECT role FROM staffLogin WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['role'] = $user['role']; // Store user role in session 
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit();
}

// Fetch users from database
$sql = "SELECT username, role FROM staffLogin";
$result = $conn->query($sql);

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container {
            margin-top: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;        }
        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f2f2f2;
        }
        .delete-btn {
            color: red;
            cursor: pointer;
        }
        .delete-btn.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mb-4">
            <div class="card-header text-white">
                <h2 class="card-title mb-0"><i class="fas fa-users"></i> View Users</h2>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['role']; ?></td>
                                    <td>
                                        <?php if ($_SESSION['role'] === 'master admin'): ?>
                                            <i class="fas fa-trash delete-btn" data-username="<?php echo $user['username']; ?>" data-role="<?php echo $user['role']; ?>"></i>
                                        <?php else: ?>
                                            <i class="fas fa-trash delete-btn disabled"></i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No users found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function() {
                var username = $(this).data('username');
                var role = $(this).data('role');

                // Verify if user is master admin
                if ('<?php echo $_SESSION['role']; ?>' !== 'master admin') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'Only Master Admin can delete users.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to delete this user.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_user.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { username: username },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.message
                                    });
                                    $('tr').filter(function() {
                                        return $(this).find('.delete-btn').data('username') === username;
                                    }).remove();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.message
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Failed to delete user. Please try again.'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>
