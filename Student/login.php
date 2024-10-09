<?php
session_start();

// Check if the user is already logged in, redirect to index.php if session exists
if (isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

// Process login form submission if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $username = $_POST['username']; // Assuming username is regNumber
    $password = $_POST['password'];

    require 'db_connection.php';
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        $stored_password = $user['password']; // Fetch the stored hashed password from the database

        // Compare the stored hashed password with the entered password
        if (password_verify($password, $stored_password)) {
            // Password correct, create session with username
            $_SESSION['email'] = $username;
            // Redirect to index.php or any authenticated page
            header("Location: index.php");
            exit();
        } else {
            header("Location: loginForm.php?error=true");
            exit();
        }
    } else {
        header("Location: loginForm.php?error=true");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
