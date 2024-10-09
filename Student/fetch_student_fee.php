<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['fetch_fee'])) {
    $regNumber = $_POST['regNumber'];

    // Fetch student id using regNumber
    $sql_student = "SELECT id FROM students WHERE regNumber = '$regNumber'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows == 1) {
        $row_student = $result_student->fetch_assoc();
        $student_id = $row_student['id'];

        // Fetch fee statements
        $sql_fee = "SELECT id, date, intake, amount,referenceNo  FROM feeStatement WHERE student_id = '$student_id'";
        $result_fee = $conn->query($sql_fee);

        if ($result_fee->num_rows > 0) {
            while ($row_fee = $result_fee->fetch_assoc()) {
                $feeStatements[] = array(
                    'id' => $row_fee['referenceNo'], // This will act as the reference number
                    'date' => $row_fee['date'],
                    'intake' => $row_fee['intake'],
                    'amount_paid' => $row_fee['amount']
                );


                // Calculate total amount paid per intake year
                $intakeYear = substr($row_fee['date'], 0, 4); // Extract year from date (assuming 'date' field is in 'YYYY-MM-DD' format)
                if (!isset($totalAmountPaid[$intakeYear])) {
                    $totalAmountPaid[$intakeYear] = 0;
                }
                $totalAmountPaid[$intakeYear] += $row_fee['amount'];
            }

            // Fetch current balance from feeBalance table
            $sql_balance = "SELECT balance FROM feeBalance WHERE student_id = '$student_id'";
            $result_balance = $conn->query($sql_balance);

            if ($result_balance->num_rows == 1) {
                $row_balance = $result_balance->fetch_assoc();
                $balance = $row_balance['balance'];
            } else {
                $balance = 0; // Set default balance if no record found
            }

            $response['success'] = true;
            $response['feeStatements'] = $feeStatements;
            $response['totalAmountPaid'] = $totalAmountPaid;
            $response['balance'] = $balance;
        } else {
            $response['message'] = "You dont have any fee statements Currently.";
        }
    } else {
        $response['message'] = "Your registration Number is not in the system.";
    }
} else {
    $response['message'] = "Invalid request.";
}

$conn->close();

// Send JSON response to AJAX request
header('Content-Type: application/json');
echo json_encode($response);
?>
