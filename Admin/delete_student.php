<?php
require 'session_start.php';
require 'db_connection.php';

if (isset($_POST['id'])) {
    $studentId = $_POST['id'];

    if ($studentId !== null) {
        // Prepare the SQL query to delete the student
        $sql = "DELETE FROM students WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param("i", $studentId);

        try {
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Student deleted successfully.']);
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                // Foreign key constraint error
                echo json_encode(['status' => 'error', 'message' => 'Cannot delete student because they are linked to attendance or grades records.']);
            } else {
                // Other SQL execution errors
                echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}

$conn->close();
?>
