<?php
require 'db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve POST data
    $name = $_POST['name'];
    $regNumber = $_POST['regNumber'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $course = intval($_POST['course']); 
    $studentGroup = $_POST['group'];
    $gender = $_POST['gender'];
    $studentID = $_POST['studentID'];
    $status = $_POST['status'];
    $department_id = intval($_POST['department']); // Ensure it's an integer

    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "../StudentImages/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if image file already exists
        if (file_exists($targetFile)) {
            echo json_encode(['image_exists' => true]);
            exit();
        }

        // Check if image file is valid
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                $image = basename($_FILES["image"]["name"]);
            } else {
                echo json_encode(['error' => 'Failed to upload image.']);
                exit();
            }
        } else {
            echo json_encode(['error' => 'File is not an image.']);
            exit();
        }
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO students (name, regNumber, phoneNumber, email, course, studentGroup, gender, studentID, status, image, department_id) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    if ($stmt === false) {
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit();
    }

    // Bind parameters
    $stmt->bind_param("ssssisssssi", $name, $regNumber, $phoneNumber, $email, $course, $studentGroup, $gender, $studentID, $status, $image, $department_id);

    // Execute and check for errors
    if ($stmt->execute()) {
        // Insert into users table
        $userStmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?,?)");
        if ($userStmt === false) {
            echo json_encode(['error' => 'User prepare failed: ' . $conn->error]);
            exit();
        }
        $password = password_hash($regNumber, PASSWORD_BCRYPT);
        $userStmt->bind_param("ss", $email, $password);
        if (!$userStmt->execute()) {
            echo json_encode(['error' => 'Failed to create user: ' . $userStmt->error]);
            exit();
        }

        echo json_encode(['success' => true, 'message' => 'Student registered successfully.']);
    } else {
        echo json_encode(['error' => 'Failed to register student: ' . $stmt->error]);
    }

    // Close statements and connection
    $stmt->close();
    $userStmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}
