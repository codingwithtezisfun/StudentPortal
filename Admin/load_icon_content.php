<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['username'];


$query1 = "SELECT COUNT(*) AS total_students FROM students";
$query2 = "SELECT COUNT(*) AS total_staff FROM staff";
$query3 = "SELECT COUNT(*) AS active_students FROM students WHERE status = 'active'";
$query4 = "SELECT COUNT(*) AS total_courses FROM courses";
$query5 = "SELECT COUNT(*) AS new_students 
           FROM students 
           WHERE SUBSTRING(studentGroup, 1, 2) = YEAR(CURDATE()) % 100
             AND SUBSTRING(studentGroup, 3, 2) = LPAD(MONTH(CURDATE()), 2, '0')";
$query6 = "SELECT COUNT(*) AS total_notices 
           FROM noticeboard 
           WHERE targetAudience IN ('staff', 'all')";
$query7 = "SELECT COUNT(*) AS total_units FROM units";
$query8 = "SELECT COUNT(*) AS num_notifications 
           FROM notifications 
           WHERE expiry_time > NOW() 
             AND to_user IN ('staff', 'all')";
$query9 = "SELECT COUNT(DISTINCT unit_id) AS registered_units_count 
           FROM student_unit_registrations";
$query10 = "SELECT COUNT(DISTINCT course_id) AS active_courses 
            FROM student_unit_registrations";

$query11 = "SELECT COUNT(*) AS total_departments FROM departments";

// Execute queries
$result1 = $conn->query($query1);
$result2 = $conn->query($query2);
$result3 = $conn->query($query3);
$result4 = $conn->query($query4);
$result5 = $conn->query($query5);
$result6 = $conn->query($query6);
$result7 = $conn->query($query7);
$result8 = $conn->query($query8);
$result9 = $conn->query($query9);
$result10 = $conn->query($query10);   
$result11 = $conn->query($query11);

if ($result11->num_rows > 0) {
    $row = $result11->fetch_assoc();
    $total_departments = $row["total_departments"];
} else {
    $total_departments = 0;
}

$stmt = $conn->prepare("SELECT id FROM staff WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $staff = $result->fetch_assoc();
    $staff_id = $staff['id'];

    $message_query = $conn->prepare("SELECT COUNT(*) AS unread_count FROM messages WHERE receiver = ? AND status = 'unread'");
    $message_query->bind_param("i", $staff_id);
    $message_query->execute();
    $message_result = $message_query->get_result();
    
    if ($message_result->num_rows > 0) {
        $message_row = $message_result->fetch_assoc();
        $unread_count = $message_row['unread_count'];
    } else {
        $unread_count = 0;
    }

    $message_query->close();
} else {
    $unread_count = 0;
}

$stmt->close();

$content = '<div class="container">';

// First row
$content .= '<div class="row">';

// Jumbotron for All Students
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #c9c0bb;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-users fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">All Students</h4>';
$content .= '<h5 class="text-center">';
if ($result1->num_rows > 0) {
    $row = $result1->fetch_assoc();
    $total_students = $row["total_students"];
    $content .= '<span>' . $total_students . ' Students</span>';
} else {
    $content .= '<span>0 Students</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for All Staff
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #f7e98e ;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-user-tie fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">All Staff</h4>';
$content .= '<h5 class="text-center">';
if ($result2->num_rows > 0) {
    $row = $result2->fetch_assoc();
    $total_staff = $row["total_staff"];
    $content .= '<span>' . $total_staff . ' Staff</span>';
} else {
    $content .= '<span>0 Staff</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Active Students
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #b2ec5d;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-user-graduate fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Active Students</h4>';
$content .= '<h5 class="text-center">';
if ($result3->num_rows > 0) {
    $row = $result3->fetch_assoc();
    $active_students = $row["active_students"];
    $content .= '<span>' . $active_students . ' Active Students</span>';
} else {
    $content .= '<span>0 Active Students</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Total Courses
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #e0ffff;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-book fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Total Courses</h4>';
$content .= '<h5 class="text-center">';
if ($result4->num_rows > 0) {
    $row = $result4->fetch_assoc();
    $total_courses = $row["total_courses"];
    $content .= '<span>' . $total_courses . ' Total Courses</span>';
} else {
    $content .= '<span>0 Total Courses</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// End of the first row and start of the second row
$content .= '</div>'; 
$content .= '<div class="row">';

// Jumbotron for New Students
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #e3f988;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-user-plus fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">New Students</h4>';
$content .= '<h5 class="text-center">';
if ($result5->num_rows > 0) {
    $row = $result5->fetch_assoc();
    $new_students_count = $row["new_students"];
    $content .= '<span>' . $new_students_count . ' New Students</span>';
} else {
    $content .= '<span>0 New Students</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Notice Board
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #cec8ef;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-bullhorn fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Notice Board</h4>';
$content .= '<h5 class="text-center">';
if ($result6->num_rows > 0) {
    $row = $result6->fetch_assoc();
    $total_notices = $row["total_notices"];
    $content .= '<span style="color: red;">' . $total_notices . ' Notice(s)</span>';
} else {
    $content .= '<span style="color: red;">0 Notices</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Units
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #fdf5e6;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-book-open fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Total Units</h4>';
$content .= '<h5 class="text-center">';
if ($result7->num_rows > 0) {
    $row = $result7->fetch_assoc();
    $total_units = $row["total_units"];
    $content .= '<span>' . $total_units . ' Units</span>';
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
$content .= '<i class="fas fa-bell fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Notifications</h4>';
$content .= '<h5 class="text-center">';
if ($result8->num_rows > 0) {
    $row = $result8->fetch_assoc();
    $num_notifications = $row["num_notifications"];
    $content .= '<span style="color: red;">' . $num_notifications . ' Notification(s)</span>';
} else {
    $content .= '<span style="color: red;">0 Notifications</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// End of the second row and start of the third row
$content .= '</div>'; 
$content .= '<div class="row">';

// Jumbotron for Registered Units
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #d3d3d3;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-pen fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Registered Units</h4>';
$content .= '<h5 class="text-center">';
if ($result9->num_rows > 0) {
    $row = $result9->fetch_assoc();
    $registered_units_count = $row["registered_units_count"];
    $content .= '<span>' . $registered_units_count . ' Units Registered</span>';
} else {
    $content .= '<span>0 Units Registered</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Active Courses
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #d1e7dd;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-chalkboard fa-2x " style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Active Courses</h4>';
$content .= '<h5 class="text-center">';
if ($result10->num_rows > 0) {
    $row = $result10->fetch_assoc();
    $active_courses_count = $row["active_courses"];
    $content .= '<span>' . $active_courses_count . ' Active Courses</span>';
} else {
    $content .= '<span>0 Active Courses</span>';
}
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Departments
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #f4bbff;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-building fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Departments</h4>';
$content .= '<h5 class="text-center">';
$content .= '<span>' . $total_departments . ' Departments</span>';
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// Jumbotron for Unread Messages
$content .= '<div class="col-lg-3 col-md-6 col-sm-6">';
$content .= '<div class="jumbotron" style="background-color: #ffd700;">';
$content .= '<div class="icon-top">';
$content .= '<i class="fas fa-envelope fa-2x" style="color:#1e90ff;"></i>';
$content .= '</div>';
$content .= '<h4 class="text-center">Unread Messages</h4>';
$content .= '<h5 class="text-center">';
$content .= '<span style="color: red;">' . $unread_count . ' Unread Message(s)</span>';
$content .= '</h5>';
$content .= '</div>';
$content .= '</div>';

// End of the third row and container
$content .= '</div>'; 
$content .= '</div>';

// Output generated content
echo $content;

// Close connection
$conn->close();
?>
