<?php

require 'db_connection.php';

$user = 'admin@kise.com';
$pass = 'admin@kise';
$role = 'master admin';

// Check if master admin already exists
$stmt = $conn->prepare("SELECT * FROM staffLogin WHERE username = ? AND role = ?");
$stmt->bind_param("ss", $user, $role);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo "Master Admin already exists. No need to create again.<br>";
} else {
    $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO staffLogin (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user, $hashedPassword, $role);

    if ($stmt->execute()) {
        echo "Master Admin created succesfully...delete this file after you are done with as it may pose a security risk<br>";
        echo "Your username is: admin@kise.com<br>";
        echo "Your password is: admin@kise<br>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

$stmt->close();
$conn->close();
?>