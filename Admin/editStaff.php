<?php
require 'session_start.php';
require 'db_connection.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch staff details
    $sql = "SELECT * FROM staff WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $role = $row['role'];
        $picture = $row['picture']; 
    } else {
        echo "Staff not found";
        exit;
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process form data for update
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $response = [];

    // Check if a new image file is uploaded
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];

        // Image upload directory
        $target_dir = "../StaffImages/";
        $target_file = $target_dir . basename($image);

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Update data in staff table including image
            $sql_update = "UPDATE staff SET name='$name', email='$email', phone='$phone', role='$role', picture='$image' WHERE id=$id";
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Error uploading file.';
            echo json_encode($response);
            exit;
        }
    } else {
        // Update data in staff table without changing the image
        $sql_update = "UPDATE staff SET name='$name', email='$email', phone='$phone', role='$role' WHERE id=$id";
    }

    if ($conn->query($sql_update) === TRUE) {
        $response['status'] = 'success';
        $response['message'] = 'Record updated successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error updating record: ' . $conn->error;
    }
    echo json_encode($response);
    exit;
} else {
    echo "ID parameter not specified";
    exit;
}

$conn->close();
?>
