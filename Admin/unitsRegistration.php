<?php
header('Content-Type: application/json');

require 'session_start.php';
require 'db_connection.php';
// Function to check if a unit exists
function unitExists($conn, $unitName) {
    $stmt = $conn->prepare("SELECT unit_id FROM units WHERE unitName = ?");
    $stmt->bind_param("s", $unitName);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'checkUnitExists':
            $unitName = $_POST['unitName'] ?? '';
            if (unitExists($conn, $unitName)) {
                echo json_encode(['exists' => true]);
            } else {
                echo json_encode(['exists' => false]);
            }
            break;

        case 'registerUnit':
            $unitName = $_POST['unitName'] ?? '';
            if (unitExists($conn, $unitName)) {
                echo json_encode(['status' => 'error', 'message' => 'Unit name already exists.']);
            } else {
                $stmt = $conn->prepare("INSERT INTO units (unitName) VALUES (?)");
                $stmt->bind_param("s", $unitName);
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Unit registered successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to register unit.']);
                }
            }
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action.']);
    }
    $stmt->close();
}

$conn->close();
?>
