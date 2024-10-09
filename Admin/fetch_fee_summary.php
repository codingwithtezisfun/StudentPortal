<?php
require 'db_connection.php';

$year = $_GET['year'];
$intake = $_GET['intake'];

// Fetch total amount paid for selected intake and year
$paidQuery = $conn->prepare("
    SELECT SUM(amount) AS total_paid 
    FROM feeStatement 
    WHERE YEAR(date) = ? 
    AND intake = ?
");
$paidQuery->bind_param('is', $year, $intake);
$paidQuery->execute();
$paidResult = $paidQuery->get_result()->fetch_assoc();

// Fetch total balance owed by students
$balanceQuery = $conn->prepare("
    SELECT SUM(balance) AS total_balance 
    FROM feeBalance
");
$balanceQuery->execute();
$balanceResult = $balanceQuery->get_result()->fetch_assoc();

// Return response as JSON
echo json_encode([
    'total_paid' => $paidResult['total_paid'] ?? 0,
    'total_balance' => $balanceResult['total_balance'] ?? 0
]);

$paidQuery->close();
$balanceQuery->close();
$conn->close();
?>
