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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <style>
       .table-img {
            width: 70px;
            height: 60px;
        }
        .card-body{
            overflow-y:auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mt-4">
        <h2 style="background-color: #343a40; color:white; padding:10px; text-align:center;" class="card-title"><i class="bi bi-people"></i> All Staff</h2>

            <div class="card-body" id="card-body">
               
                <?php
                // Get the user's email from the session
                $userEmail = $_SESSION['username'];

                include 'db_connection.php';

                // Fetch user role based on email
                $roleQuery = "SELECT role FROM staffLogin WHERE username = ?";
                $stmt = $conn->prepare($roleQuery);
                $stmt->bind_param("s", $userEmail);
                $stmt->execute();
                $stmt->bind_result($userRole);
                $stmt->fetch();
                $stmt->close();

                // Query to fetch all staff
                $sql = "SELECT * FROM staff";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered'>
                            <thead class='thead-dark'>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Picture</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>";
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row["id"]."</td>
                                <td>".$row["name"]."</td>
                                <td>".$row["email"]."</td>
                                <td>".$row["phone"]."</td>
                                <td>".$row["role"]."</td>
                                <td><img src='../StaffImages/".$row["picture"]."' class='table-img' width='70px' height='60px'></td>
                                <td>
                                    <button class='btn btn-sm btn-primary edit-button' data-id='".$row["id"]."'><i class='bi bi-pencil'></i> Edit</button>
                                    <button class='btn btn-sm btn-danger delete-button' data-id='".$row["id"]."' ".($userRole !== 'master admin' ? 'disabled' : '')."><i class='bi bi-trash'></i> Delete</button>
                                </td>
                            </tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p>No staff records found.</p>";
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.4/dist/sweetalert2.min.js"></script>
    <script>
    

        $(document).ready(function() {
            $('.delete-button').on('click', function() {
                const userRole = '<?php echo $userRole; ?>';
                if (userRole !== 'master admin') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Access Denied',
                        text: 'Only Master Admins can delete users.',
                    });
                } else {
                    const id = $(this).data('id');
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
                            $.ajax({
                                url: 'delete_staff.php',
                                type: 'POST',
                                data: { id: id },
                                success: function(response) {
                                    let res = JSON.parse(response);
                                    if (res.status === 'success') {
                                        Swal.fire(
                                            'Deleted!',
                                            res.message,
                                            'success'
                                        ).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            res.message,
                                            'error'
                                        );
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Error!',
                                        'An error occurred: ' + error,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                }
            });


            $('.edit-button').on('click', function() {
                const id = $(this).data('id');
                $.ajax({
                    url: 'edit_staff.php', 
                    type: 'GET',
                    data: { id: id },
                    success: function(response) {
                        $('#card-body').html(response);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            'An error occurred: ' + error,
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>
</html>
