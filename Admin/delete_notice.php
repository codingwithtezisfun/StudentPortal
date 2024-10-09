<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require 'session_start.php';
    require 'db_connection.php';

    $id = $_POST['id'];

    $delete_sql = "DELETE FROM noticeboard WHERE id = $id";

    if ($conn->query($delete_sql) === TRUE) {
        echo "Notice deleted successfully.";
    } else {
        echo "Error deleting notice: " . $conn->error;
    }

    $conn->close();
}
?>
