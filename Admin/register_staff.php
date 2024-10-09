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
    if ($user['role'] !== 'master admin' && $user['role'] !== 'principal') {
    echo json_encode(['status' => 'error', 'message' => 'Only Master Admin and Principal can register staff members.']);
    exit();
}

} else {
    echo json_encode(['status' => 'error', 'message' => 'User ' . $email . ' not found.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Upload picture
    $picture = $_FILES['picture']['name'];
    $target_dir = "../StaffImages/";
    $target_file = $target_dir . basename($picture);

    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
        // Store only the image name in the database
        $picture_name = basename($picture);

        $sql = "INSERT INTO staff (name, email, phone, role, picture) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $phone, $role, $picture_name);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'New Staff registered successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading picture']);
    }

    // Close statement and database connection
    $stmt->close();
    $conn->close();

    exit();
}
?>
