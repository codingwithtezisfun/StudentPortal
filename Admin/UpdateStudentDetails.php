<?php
require 'session_start.php';
require 'db_connection.php';
// Fetch the user's role
$email = $_SESSION['username'];
$sql = "SELECT role FROM staffLogin WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['role'] = $user['role']; 
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found.']);
    exit();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .cbd {
            color: blue;
            font-weight: bold;
        }
        .form-row {
            margin-bottom: 1rem; /* Space between rows */
        }
         .student-image {
            width: 250px;
            height: 250px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-user-graduate" style="font-size: 20px; margin-right: 10px;"></i>
                    Edit Student Details
                </h2>
            </div>
            <div class="card-body cbd">
                <?php
                require 'db_connection.php';
                if (isset($_GET['id'])) {
                    $student_id = $_GET['id'];

                    // Prepare statement to avoid SQL injection
                    $stmt = $conn->prepare("SELECT s.id, s.name, s.regNumber, s.phoneNumber, s.email, s.course, s.studentGroup, s.gender, s.studentID, s.status, s.image, d.department_id, d.departmentName 
                                             FROM students s 
                                             JOIN departments d ON s.department_id = d.department_id 
                                             WHERE s.id = ?");
                    $stmt->bind_param("i", $student_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        ?>
                        <form id="updateStudentForm" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for=""></label>
                                </div>
                                <div class="col-md-8">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../StudentImages/<?php echo htmlspecialchars($row['image']); ?>" alt="Student Image" class="student-image">
                                <?php else: ?>
                                    <img src="path_to_default_image" alt="Default Image" class="student-image">
                                <?php endif; ?>
                            </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="name">Name</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="regNumber">Registration Number</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="regNumber" name="regNumber" value="<?php echo htmlspecialchars($row['regNumber']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="phoneNumber">Phone Number</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($row['phoneNumber']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="email">Email</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="department">Department</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="department" name="department">
                                        <?php
                                        $sql_departments = "SELECT department_id, departmentName FROM departments";
                                        $result_departments = $conn->query($sql_departments);

                                        if ($result_departments->num_rows > 0) {
                                            while ($row_department = $result_departments->fetch_assoc()) {
                                                $selected = ($row_department['department_id'] == $row['department_id']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row_department['department_id']) . '" ' . $selected . '>' . htmlspecialchars($row_department['departmentName']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="courses">Course</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="courses" name="courses">
                                        <?php
                                        $sql_courses = "SELECT course_id, courseName FROM courses WHERE department_id = ?";
                                        $stmt_courses = $conn->prepare($sql_courses);
                                        $stmt_courses->bind_param("i", $row['department_id']);
                                        $stmt_courses->execute();
                                        $result_courses = $stmt_courses->get_result();

                                        if ($result_courses->num_rows > 0) {
                                            while ($row_course = $result_courses->fetch_assoc()) {
                                                $selected = ($row_course['course_id'] == $row['course']) ? 'selected' : '';
                                                echo '<option value="' . htmlspecialchars($row_course['course_id']) . '" ' . $selected . '>' . htmlspecialchars($row_course['courseName']) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="studentGroup">Student Group</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="studentGroup" name="studentGroup" value="<?php echo htmlspecialchars($row['studentGroup']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="gender">Gender</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($row['gender']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="studentID">National ID No</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="studentID" name="studentID" value="<?php echo htmlspecialchars($row['studentID']); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="status">Status</label>
                                </div>
                                <div class="col-md-8">
                                    <select class="form-control" id="status" name="status">
                                        <option value="Active" <?php echo ($row['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="Inactive" <?php echo ($row['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4">
                                    <label for="image">Update Image</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="file" class="form-control-file" id="image" name="image">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4"></div>
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">Update Student</button>
                                </div>
                            </div>
                        
                        </form>
                        <?php
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Student not found.</div>';
                    }

                    $stmt->close();
                } else {
                    echo '<div class="alert alert-danger" role="alert">Student ID not provided.</div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function () {
            // Fetch courses based on selected department
            $("#department").on("change", function () {
                var departmentId = $(this).val();
                console.log("Department selected:", departmentId); // Log selected department

                $.ajax({
                    url: "fetch_courses.php",
                    type: "GET",
                    data: { department_id: departmentId },
                    dataType: "json",
                    success: function (data) {
                        console.log("Courses data received:", data); // Log the received data
                        var $courseSelect = $("#courses");
                        $courseSelect.empty(); // Clear existing options
                        $courseSelect.append('<option value="">Select Course</option>'); // Default option

                        if (data.error) {
                            console.error("Error fetching courses:", data.error);
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: data.error,
                                showConfirmButton: true,
                            });
                        } else {
                            $.each(data, function (index, course) {
                                $courseSelect.append(
                                    '<option value="' +
                                    course.course_id +
                                    '">' +
                                    course.courseName +
                                    "</option>"
                                );
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX error fetching courses:", status, error);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while fetching courses.",
                            showConfirmButton: true,
                        });
                    },
                });
            });

            // Submit form via AJAX
            $("#updateStudentForm").submit(function (e) {
               

                var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                // Allowed roles
                var allowedRoles = ['accounts', 'principal', 'quality_assurance', 'front_desk'];

                // Check if user role is in the allowed roles
                if (!allowedRoles.includes(userRole)) {
                 e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied !!',
                        text: 'You do not have permission to register student.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                var formData = new FormData(this);
                 for (var [key, value] of formData.entries()) {
        console.log(key, value);
    }
           e.preventDefault();

                $.ajax({
                    url: "UpdateStudent.php", // Adjust the URL based on your file path
                    type: "POST",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        let parsedResponse = JSON.parse(response);

                        if (parsedResponse.image_exists) {
                            // Show warning if image already exists
                            Swal.fire({
                                icon: 'warning',
                                title: 'Warning',
                                text: 'Image already exists. Please choose a different file.',
                                showConfirmButton: true
                            });
                        } else if (parsedResponse.success) {
                            // Show success message
                            Swal.fire({
                                icon: "success",
                                title: "Success!",
                                text: "Student details updated successfully.",
                                showConfirmButton: false,
                                timer: 1500,
                            }).then(function () {
                                // Optional callback after success
                            });
                        } else if (parsedResponse.error) {
                            Swal.fire({
                                icon: "error",
                                title: "Error",
                                text: parsedResponse.error,
                                showConfirmButton: true,
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "An error occurred while updating student details.",
                            showConfirmButton: true,
                        });
                    }
                });

            });
        });
    </script>
</body>
</html>
