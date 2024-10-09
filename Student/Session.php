<?php

require 'session_start.php';

require 'db_connection.php';

$email = $_SESSION['email'];

// Prepare and execute SQL query to get student information
$stmt = $conn->prepare("SELECT id, regNumber, course, status FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $student_id = $row['id'];
    $regNumber = $row['regNumber'];
    $course = $row['course'];
    $status = $row['status']; // Retrieve student status
} else {
    echo json_encode(['error' => 'Student not found']);
    exit();
}

// Determine the current intake based on the month
$current_month = date('n');
if ($current_month >= 1 && $current_month <= 3) {
    $current_intake = "Jan-Mar";
} elseif ($current_month >= 4 && $current_month <= 6) {
    $current_intake = "Apr-Jun";
} elseif ($current_month >= 7 && $current_month <= 9) {
    $current_intake = "Jul-Sep";
} else {
    $current_intake = "Oct-Dec";
}

// Handle AJAX request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action'])) {
    if ($_POST['action'] === 'fetch_student_info') {
        echo json_encode([
            'regNumber' => $regNumber,
            'intake' => $current_intake
        ]);
        exit();
    }

    if ($_POST['action'] === 'check_session') {
        $intake = $_POST['intake'];
        $year = $_POST['year'];

        // Check if session is already reported
        $stmt_check = $conn->prepare("SELECT * FROM session WHERE student_id = ? AND intake = ? AND year = ?");
        $stmt_check->bind_param("iss", $student_id, $intake, $year);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo json_encode(['status' => 'reported']);
        } else {
            echo json_encode(['status' => 'not_reported']);
        }
        exit();
    }

    if ($_POST['action'] === 'report_session') {
        if ($status !== 'Active') {
            echo json_encode(['status' => 'error', 'message' => 'You are not an active student.']);
            exit();
        }

        $intake = $_POST['intake'];
        $year = $_POST['year'];

        // Check if the fee is set for the specified course, intake, and year
        $stmt_fee_check = $conn->prepare("SELECT fee_amount FROM fee WHERE course = ? AND intake = ? AND year = ?");
        $stmt_fee_check->bind_param("ssi", $course, $intake, $year);
        $stmt_fee_check->execute();
        $result_fee_check = $stmt_fee_check->get_result();

        if ($result_fee_check->num_rows == 0) {
            // No fee set for the specified intake and year
            echo json_encode([
                'status' => 'error',
                'message' => 'Fee not set for the specified intake and year.'
            ]);
            exit();
        }

        // Check if session is already reported
        $stmt_check = $conn->prepare("SELECT * FROM session WHERE student_id = ? AND intake = ? AND year = ?");
        $stmt_check->bind_param("iss", $student_id, $intake, $year);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows == 0) {
            // Insert the session report
            $stmt_insert = $conn->prepare("INSERT INTO session (student_id, year, intake, session_reported) VALUES (?, ?, ?, TRUE)");
            $stmt_insert->bind_param("iss", $student_id, $year, $intake);

            if ($stmt_insert->execute()) {
                $row_fee = $result_fee_check->fetch_assoc();
                $fee_amount = -$row_fee['fee_amount'];

                // Check fee balance
                $stmt_check_balance = $conn->prepare("SELECT * FROM feeBalance WHERE student_id = ?");
                $stmt_check_balance->bind_param("i", $student_id);
                $stmt_check_balance->execute();
                $result_check_balance = $stmt_check_balance->get_result();

                if ($result_check_balance->num_rows == 0) {
                    // Insert fee balance if not found
                    $stmt_insert_balance = $conn->prepare("INSERT INTO feeBalance (student_id, balance) VALUES (?, ?)");
                    $stmt_insert_balance->bind_param("id", $student_id, $fee_amount);
                    $stmt_insert_balance->execute();
                } else {
                    // Update the existing fee balance
                    $row_balance = $result_check_balance->fetch_assoc();
                    $current_balance = $row_balance['balance'];
                    $new_balance = $current_balance + $fee_amount;

                    $stmt_update_balance = $conn->prepare("UPDATE feeBalance SET balance = ? WHERE student_id = ?");
                    $stmt_update_balance->bind_param("di", $new_balance, $student_id);
                    $stmt_update_balance->execute();
                }

                echo json_encode(['status' => 'success', 'message' => 'Session reported and fee balance updated.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to report session.']);
            }
        } else {
            echo json_encode(['status' => 'reported', 'message' => 'Session has been reported for the selected intake and year.']);
        }
        exit();
    }
}

$conn->close();
?>
