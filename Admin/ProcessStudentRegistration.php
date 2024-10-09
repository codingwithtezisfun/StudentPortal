<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {

    require 'db_connection.php';

    // Retrieve form data
    $name = $_POST['name'];
    $regNumber = $_POST['regNumber'];
    $phoneNumber = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $group = $_POST['group'];
    $gender = $_POST['gender'];
    $studentID = $_POST['studentID'];
    $status = $_POST['status'];
    $image = $_FILES['image']['name'];

    // Image upload directory
    $target_dir = "StudentImages/";
    $target_file = $target_dir . basename($image);

    // Move uploaded file to target directory
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert student data into students table
        $stmt_students = $conn->prepare("INSERT INTO students (name, regNumber, phoneNumber, email, course, studentGroup, gender, studentID, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_students->bind_param("ssssisssss", $name, $regNumber, $phoneNumber, $email, $course, $group, $gender, $studentID, $status, $target_file);

        // Insert user data into users table
        $stmt_users = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt_users->bind_param("ss", $email, $regNumber); // email as username, regNumber as password

        // Execute both queries
        $success = $stmt_students->execute() && $stmt_users->execute();

        if ($success) {
            echo "<p class='text-success'>Student registered successfully!</p>";
        } else {
            echo "<p class='text-danger'>Error: " . $conn->error . "</p>";
        }

        $stmt_students->close();
        $stmt_users->close();
    } else {
        echo "<p class='text-danger'>Error uploading file.</p>";
    }

    $conn->close();
} else {
    echo "<p class='text-danger'>Form submission failed. Please try again.</p>";
}
?>
