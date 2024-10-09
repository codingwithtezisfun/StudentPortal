<?php
session_start();

require 'db_connection.php';

$course_id = $_POST['course_id'];
$stage = $_POST['stage'];
$semester = $_POST['semester'];
$electiveCategory = isset($_POST['electiveCategory']) ? $_POST['electiveCategory'] : null;

$sql = "
    SELECT 
        u.unit_id, u.unitName AS unit_name
    FROM 
        course_units cu 
    JOIN 
        units u ON cu.unit_id = u.unit_id 
    WHERE 
        cu.course_id = ? 
        AND cu.stage = ? 
        AND cu.semester = ?
";

$params = [$course_id, $stage, $semester];

if ($electiveCategory) {
    $sql .= " AND cu.electiveCategory = ?";
    $params[] = $electiveCategory;
}

$stmt = $conn->prepare($sql);
$stmt->bind_param(str_repeat("i", count($params) - 1) . "s", ...$params);
$stmt->execute();
$result = $stmt->get_result();

$units = [];
while ($row = $result->fetch_assoc()) {
    $units[] = $row; // Fetching both unit_id and unitName
}

$stmt->close();
$conn->close();

echo json_encode($units);
?>
