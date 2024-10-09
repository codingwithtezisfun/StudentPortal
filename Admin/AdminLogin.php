<?php
session_start();
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM staffLogin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $username; 
            header("Location: AdminDashboard.php");
            exit();
        } else {
            header("Location: StaffLoginForm.php?error=true");
            exit();
        }
    } else {
        header("Location: StaffLoginForm.php?error=true");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
