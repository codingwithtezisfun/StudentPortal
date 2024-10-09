<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: loginForm.php");
    exit();
}

require 'db_connection.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT id, course FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    $student_id = $student['id'];
    $course = $student['course'];
} else {
    $student_id = 0;
    $course = '';
}

$stmt->close();

// Determine the current intake based on the month
$current_month = date('n');
$intake = '';
if ($current_month >= 1 && $current_month <= 3) {
    $intake = "Jan-Mar";
} elseif ($current_month >= 4 && $current_month <= 6) {
    $intake = "Apr-Jun";
} elseif ($current_month >= 7 && $current_month <= 9) {
    $intake = "Jul-Sep";
} elseif ($current_month >= 10 && $current_month <= 12) {
    $intake = "Oct-Dec";
}

// Get the current year
$year = date('Y');

// Check if the session is reported for the current year and intake
$sql_check = "SELECT * FROM session WHERE student_id = ? AND intake = ? AND year = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("iss", $student_id, $intake, $year);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    $session_reported = true;
    $status_message = '<i class="fa-solid fa-check fa-1x" style="color:green;"></i> <span style="color:green;">Reported.</span>';} else {
    $session_reported = false;

    $status_message = '<i class="fa-solid fa-times fa-1x" style="color:red;"></i> <span style="color:red;">Not reported.</span>';
}

$stmt_check->close();

// Queries to fetch and count various data
$query6 = "SELECT COUNT(*) AS total_notices FROM noticeboard";
$query7 = "SELECT COUNT(*) AS total_units FROM course_units WHERE course_id = ?";
$query8 = "SELECT COUNT(*) AS num_notifications FROM notifications WHERE expiry_time > NOW() 
           AND (to_user = 'All' OR to_user = 'Students')";

// Execute queries
$result6 = $conn->query($query6);

$stmt7 = $conn->prepare($query7);
$stmt7->bind_param("s", $course);
$stmt7->execute();
$result7 = $stmt7->get_result();

$result8 = $conn->query($query8);

// Prepare HTML content
$content = '';

// Jumbotron for Notice Board
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #e0ffff  ;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-bullhorn fa-2x" style="color:blue;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Notice Board</h4>';
$content .= '<h5 class="text-center">';
if ($result6->num_rows > 0) {
    $row = $result6->fetch_assoc();
    $total_notices = $row["total_notices"];
    $content .= '<span>' . $total_notices . ' Notices</span>';
} else {
    $content .= '<span>0 Notices</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Session
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color:#faebd7;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fa-solid fa-school fa-2x" style="color:blue;"></i> <i class="fa-solid fa-circle-check" style="color:blue;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Session Status</h4>';
$content .= '<h5 class="text-center">';
$content .= '<span>' . $status_message . '</span>';
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Units
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color:#fdfd96">';
$content .= '<div class="icon-top">';
$content .= '<i class="fa-solid fa-book-open fa-2x" style="color:blue;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">My Units</h4>';
$content .= '<h5 class="text-center">';
if ($result7->num_rows > 0) {
    $row = $result7->fetch_assoc();
    $total_units = $row["total_units"];
    $content .= '<span>' . $total_units . ' Total Units</span>';
} else {
    $content .= '<span>0 Total Units</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Notifications
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #add8e6;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-bell fa-2x" style="color:blue;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Notifications</h4>';
$content .= '<h5 class="text-center">';
if ($result8->num_rows > 0) {
    $row = $result8->fetch_assoc();
    $num_notifications = $row["num_notifications"];
    $content .= '<span style="color: red;">' . $num_notifications . ' Message(s)</span>';
} else {
    $content .= '<span style="color: red;">0 Messages</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Output generated content
echo $content;

// Close connection
$conn->close();
?>
