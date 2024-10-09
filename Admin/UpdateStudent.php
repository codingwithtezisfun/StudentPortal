<?php
require 'session_start.php';
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Debugging logs
    error_log('Received POST request');
    error_log(print_r($_POST, true));
    error_log(print_r($_FILES, true));

    // Retrieve form data
    $student_id = $_POST['id'];
    $name = $_POST['name'];
    $regNumber = $_POST['regNumber'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $course = $_POST['courses'];
    $studentGroup = $_POST['studentGroup'];
    $gender = $_POST['gender'];
    $studentID = $_POST['studentID'];
    $status = $_POST['status'];
    $department_id = $_POST['department'];

    // Handle image upload
    $imagePath = null;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['size'] > 0) {
        $targetDir = "../StudentImages/";
        $imagePath = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imagePath;

        if (file_exists($targetFile)) {
            echo json_encode(['image_exists' => true]);
            exit();
        }

        // Check if file is an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            http_response_code(400);
            echo json_encode(['error' => 'File is not an image']);
            exit();
        }

        // Move uploaded file and check permissions
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            error_log("Failed to upload image to " . $targetFile);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to upload image']);
            exit();
        }
    }

    // Update user credentials
    $hashedPassword = password_hash($regNumber, PASSWORD_BCRYPT);
    $updateUserQuery = "UPDATE users SET username = ?, password = ? WHERE username = (SELECT email FROM students WHERE id = ?)";
    $stmt = $conn->prepare($updateUserQuery);
    if ($stmt === false) {
        error_log("Failed to prepare SQL statement: " . $conn->error);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to prepare SQL statement']);
        exit();
    }
    $stmt->bind_param("ssi", $email, $hashedPassword, $student_id);
    if (!$stmt->execute()) {
        error_log("Failed to update user credentials: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update user credentials']);
        exit();
    }

    // Update student information
    $updateStudentQuery = "UPDATE students 
                           SET name = ?, regNumber = ?, phoneNumber = ?, email = ?, course = ?, studentGroup = ?, gender = ?, department_id = ?, status = ?, studentID = ?" . 
                           ($imagePath ? ", image = ?" : "") . " 
                           WHERE id = ?";
    $stmt = $conn->prepare($updateStudentQuery);
    if ($stmt === false) {
        error_log("Failed to prepare SQL statement: " . $conn->error);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to prepare SQL statement']);
        exit();
    }

    // Bind parameters and execute query
    if ($imagePath) {
        error_log("Updating student with image: " . $imagePath);
        $stmt->bind_param("ssssissssssi", $name, $regNumber, $phoneNumber, $email, $course, $studentGroup, $gender, $department_id, $status, $studentID, $imagePath, $student_id);
    } else {
        $stmt->bind_param("ssssisssssi", $name, $regNumber, $phoneNumber, $email, $course, $studentGroup, $gender, $department_id, $status, $studentID, $student_id);
    }

    if (!$stmt->execute()) {
        error_log("Failed to update student details: " . $stmt->error);
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update student details']);
        exit();
    }

    echo json_encode(['success' => 'Student details updated successfully']);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}

$conn->close();

?>
