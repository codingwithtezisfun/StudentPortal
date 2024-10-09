<?php
include 'db_connection.php';

if (isset($_POST['id']) && is_numeric($_POST['id'])) {
    $feeId = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM fee WHERE id = ?");
    $stmt->bind_param("i", $feeId);

    if ($stmt->execute()) {
        $response = [
            'Result' => 'OK',
            'Message' => 'Fee statement deleted successfully.'
        ];
    } else {

        $response = [
            'Result' => 'ERROR',
            'Message' => 'Failed to delete fee statement.'
        ];
    }

    $stmt->close();
} else {

    $response = [
        'Result' => 'ERROR',
        'Message' => 'Invalid or missing fee statement ID.'
    ];
}

header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>
