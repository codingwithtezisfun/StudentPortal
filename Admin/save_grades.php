<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['grades'])) {
    $grades = $_POST['grades'];
    
    include 'db_connection.php';

    foreach ($grades as $student_id => $units) {
        foreach ($units as $unit_id => $grade) {
            $grade = $grade ? $grade : 0;
            $stmt = $conn->prepare("INSERT INTO grades (student_id, unit_Id, grade) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE grade = VALUES(grade)");
            $stmt->bind_param("iid", $student_id, $unit_id, $grade);
            $stmt->execute();
        }
    }

    $conn->close();
    echo 'Grades saved successfully!';
}
?>
