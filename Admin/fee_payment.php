<?php
include 'db_connection.php';

$response = array('success' => false, 'message' => '');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_fee'])) {
    $student_id = $_POST['student_id'];
    $amount_paid = $_POST['amount_paid'];
    $date_paid = $_POST['date_paid'];
    $intake = $_POST['intake'];
    $referenceNo = $_POST['referenceNo'];

    $conn->begin_transaction();

    try {
        // Insert fee payment into feeStatement table
        $stmt = $conn->prepare("INSERT INTO feeStatement (student_id, amount, date, intake, referenceNo) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $student_id, $amount_paid, $date_paid, $intake, $referenceNo);
        $stmt->execute();

        // Check if the student exists in the feeBalance table
        $stmt = $conn->prepare("SELECT balance FROM feeBalance WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Student exists, update the balance
            $row = $result->fetch_assoc();
            $new_balance = $row['balance'] + $amount_paid;

            $stmt = $conn->prepare("UPDATE feeBalance SET balance = ? WHERE student_id = ?");
            $stmt->bind_param("di", $new_balance, $student_id);
        } else {
            // Student doesn't exist, insert new balance record
            $stmt = $conn->prepare("INSERT INTO feeBalance (student_id, balance) VALUES (?, ?)");
            $stmt->bind_param("id", $student_id, $amount_paid);
        }
        $stmt->execute();

        $conn->commit();

        $response['success'] = true;
        $response['message'] = "Fee payment logged successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = "Error: " . $e->getMessage();
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>
