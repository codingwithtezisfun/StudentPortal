<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    // Upload picture
    $picture = $_FILES['picture']['name'];
    $target_dir = "StaffImages/";
    $target_file = $target_dir . basename($picture);

    if (move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
        // Insert data into staff table
        $sql = "INSERT INTO staff (name, email, phone, role, picture) VALUES ('$name', '$email', '$phone', '$role', '$picture')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Error uploading picture";
    }
}

$conn->close();
?>
