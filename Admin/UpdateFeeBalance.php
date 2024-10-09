<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_name = $_POST['student_name'];
    $reg_no = $_POST['reg_no'];
    $intake = $_POST['intake'];
    $year = $_POST['year'];

    $sql_student = "SELECT id, course FROM students WHERE name = '$student_name' AND regNumber = '$reg_no'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows == 1) {
        $row_student = $result_student->fetch_assoc();
        $student_id = $row_student['id'];
        $course_id = $row_student['course'];

        $sql_fee = "SELECT fee_amount FROM fee WHERE course_id = $course_id AND intake = '$intake' AND year = $year";
        $result_fee = $conn->query($sql_fee);

        if ($result_fee->num_rows == 1) {
            $row_fee = $result_fee->fetch_assoc();
            $fee_amount = $row_fee['fee_amount'];

            $sql_balance = "SELECT balance FROM feeBalance WHERE student_id = $student_id";
            $result_balance = $conn->query($sql_balance);

            if ($result_balance->num_rows == 1) {
                $row_balance = $result_balance->fetch_assoc();
                $current_balance = $row_balance['balance'];
                $new_balance = $current_balance - $fee_amount;

                $sql_update_balance = "UPDATE feeBalance SET balance = $new_balance WHERE student_id = $student_id";
                if ($conn->query($sql_update_balance) === TRUE) {
                    echo json_encode(['Result' => 'OK']);
                } else {
                    echo json_encode(['Result' => 'ERROR', 'Message' => 'Error updating balance: ' . $conn->error]);
                }
            } else {
                $new_balance = -$fee_amount;
                $sql_insert_balance = "INSERT INTO feeBalance (student_id, balance) VALUES ($student_id, $new_balance)";
                if ($conn->query($sql_insert_balance) === TRUE) {
                    echo json_encode(['Result' => 'OK']);
                } else {
                    echo json_encode(['Result' => 'ERROR', 'Message' => 'Error inserting balance: ' . $conn->error]);
                }
            }
        } else {
            echo json_encode(['Result' => 'ERROR', 'Message' => 'Fee record not found for the course and intake']);
        }
    } else {
        echo json_encode(['Result' => 'ERROR', 'Message' => 'Student not found']);
    }
} else {
    echo json_encode(['Result' => 'ERROR', 'Message' => 'Invalid request method']);
}

$conn->close();
?>
