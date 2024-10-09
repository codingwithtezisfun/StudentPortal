<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
    <style>
        .table-img {
            width: 70px;
            height: 60px;
        }
        .card-body{
            overflow-x:auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card mt-4">
            <h2 style="background-color: #36454f; color:white; padding:10px; text-align:center;" class="card-title"><i class="bi bi-people"></i> All Staff</h2>
            <div class="card-body">
                <?php
                include 'db_connection.php';

                // Query to fetch only name, email, phone, role, and picture
                $sql = "SELECT name, email, phone, role, picture FROM staff";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='table table-bordered'>
                            <thead class='thead-dark'>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Picture</th>
                                </tr>
                            </thead>
                            <tbody>";
                    
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>".$row["name"]."</td>
                                <td>".$row["email"]."</td>
                                <td>".$row["phone"]."</td>
                                <td>".$row["role"]."</td>
                                <td><img src='../StaffImages/".$row["picture"]."' class='table-img' width='70px' height='60px'></td>
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
</body>
</html>
