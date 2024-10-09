<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];

    if ($action == 'loadUnits') {
        // Load units into the dropdown
        $units_sql = "SELECT unit_id, unitName FROM units ORDER BY unitName ASC";
        $result = $conn->query($units_sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['unit_id'] . '">' . $row['unitName'] . '</option>';
            }
        } else {
            echo '<option value="">No units available</option>';
        }
    } elseif ($action == 'updateUnit') {
        // Update unit name
        $unitId = $_POST['unitId'];
        $newUnitName = $_POST['newUnitName'];

        $stmt = $conn->prepare("UPDATE units SET unitName = ? WHERE unit_id = ?");
        $stmt->bind_param("si", $newUnitName, $unitId);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            http_response_code(500);
            echo 'error';
        }

        $stmt->close();
    } elseif ($action == 'deleteUnit') {
        // Check if unit is being used in course_units
        $unitId = $_POST['unitId'];

        $check_sql = "SELECT COUNT(*) as count FROM course_units WHERE unit_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("i", $unitId);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count == 0) {
            // If not used, delete the unit
            $delete_sql = "DELETE FROM units WHERE unit_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $unitId);

            if ($stmt->execute()) {
                echo 'success';
            } else {
                http_response_code(500);
                echo 'error';
            }

            $stmt->close();
        } else {
            echo 'in_use'; // Unit is being used in course_units
        }
    }

    $conn->close();
} else {
    http_response_code(405);
    echo 'Method Not Allowed';
}
?>
