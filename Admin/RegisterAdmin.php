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
    if ($user['role'] !== 'master admin') {
        echo json_encode(['status' => 'error', 'message' => 'Only Master Admins can register users.']);
        exit();
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'User ' . $email . ' not found.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO staffLogin (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $hashedPassword, $role);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'User registered successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register user.']);
    }

    $stmt->close();
    $conn->close();

    exit();
}
?>