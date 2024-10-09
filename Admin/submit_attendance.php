<?php
require 'db_connection.php';

// Retrieve and decode the attendance data
$attendances = json_decode($_POST['attendances'], true);
$update = isset($_POST['update']) && $_POST['update'] == 'true';

foreach ($attendances as $attendance) {
    $student_id = intval($attendance['student_id']);
    $unit_id = intval($attendance['unit']);
    $active_days = $attendance['active_days'];  // Adjust according to your data structure

    foreach ($active_days as $date => $sessions) {
        foreach ($sessions as $session => $status) {
            // Check if the attendance record already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM attendance WHERE student_id = ? AND unit_id = ? AND date = ? AND session = ?");
            $stmt->bind_param("iiss", $student_id, $unit_id, $date, $session);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                // Record exists, check if we should update it
                if (!$update) {
                    echo json_encode([
                        'requiresUpdate' => true,
                        'message' => "Attendance record on $date during $session session already exists. Do you want to update it?"
                    ]);
                    exit;
                } else {
                    // Update the existing record
                    $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE student_id = ? AND unit_id = ? AND date = ? AND session = ?");
                    $stmt->bind_param("siiis", $status, $student_id, $unit_id, $date, $session);
                }
            } else {
                // Insert a new record
                $stmt = $conn->prepare("INSERT INTO attendance (student_id, unit_id, date, status, session) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iisss", $student_id, $unit_id, $date, $status, $session);
            }

            // Execute the query
            if (!$stmt->execute()) {
                echo "Error: " . $stmt->error;
                $stmt->close();
                $conn->close();
                exit;
            }

            $stmt->close();
        }
    }
}

// Close the connection
$conn->close();

// Send a success message if no update was required
if (!$update) {
    echo json_encode(['requiresUpdate' => false, 'message' => 'Attendance records added successfully.']);
}

?>
