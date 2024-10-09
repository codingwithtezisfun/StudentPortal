<?php
require 'session_start.php';
require 'db_connection.php';

echo '<option value="" disabled selected>Select a unit</option>';

// Fetch units from the database
$sql = "SELECT unit_id, unitName FROM units";
$result = $conn->query($sql);

// Check if any units were found
if ($result->num_rows > 0) {
    // Generate options for each unit
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['unit_id'] . '">' . $row['unitName'] . '</option>';
    }
} else {
    // If no units were found, display a message
    echo '<option value="" disabled>No units found</option>';
}

// Close the database connection
$conn->close();
?>
