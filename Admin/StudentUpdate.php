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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .student-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .card-header {
            color: white;
            background-color: #343a40;
        }
        .marge{
            padding:20px;
        }
        .button{
            width: 100%;
        }
        .container{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h4>
                    <i class="fas fa-user-graduate" style="font-size: 20px; margin-right: 10px;"></i>
                    Edit Student Details
                </h4>
            </div>
            <div class="card-body">
                <!-- Form to fetch student by registration number -->
                <div class="card marge">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="OldregNumber">Student Registration Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="OldregNumber" name="OldregNumber" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-12">
                            <button type="button" id="fetchStudentBtn" class="btn btn-primary">Fetch Student Details</button>
                        </div>
                    </div>
                </div>
                <!-- Hidden section for student details (displayed after fetching) -->
                <div id="studentDetails" style="display:none;" class="card marge m-2">
                    <form id="updateStudentForm" enctype="multipart/form-data">
                        <input type="hidden" id="id" name="id">
                        <div class="form-group text-center">
                            <img src="" id="studentImage" class="student-image" alt="Student Image">
                            <input type="file" id="image" name="image">
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="name">Name</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="regNumber">Registration Number</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="regNumber" name="regNumber">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="phoneNumber">Phone Number</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="phoneNumber" name="phoneNumber">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="email">Email</label>
                            </div>
                            <div class="col-md-8">
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="studentGroup">Student Group</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="studentGroup" name="studentGroup">
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="gender">Gender</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="gender" name="gender">
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="department">Department</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="department" name="department">
                                    <?php
                                    require 'db_connection.php'; // Ensure connection is included here
                                    $sql_departments = "SELECT department_id, departmentName FROM departments";
                                    $result_departments = $conn->query($sql_departments);

                                    if ($result_departments->num_rows > 0) {
                                        while ($row_department = $result_departments->fetch_assoc()) {
                                            echo '<option value="' . htmlspecialchars($row_department['department_id']) . '">' . htmlspecialchars($row_department['departmentName']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="course">Course</label>
                            </div>
                            <div class="col-md-8">
                               <select class="form-control" id="courses" name="courses"></select>
                            </div>
                        </div>

                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="status">Status</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="status" name="status">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                         <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="status">National ID</label>
                            </div>
                            <div class="col-md-8">
                                 <input type="text" class="form-control" id="studentID" name="studentID">
                            </div>
                        </div>

                        <button type="submit" class="btn button btn-primary">Update Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
    $(document).ready(function() {
        // Fetch student details when the button is clicked
        $('#fetchStudentBtn').click(function() {
            var regNumber = $('#OldregNumber').val(); 
            
            if (regNumber) {
                $.ajax({
                    url: 'fetchStudent.php', 
                    type: 'GET',
                    data: { regNumber: regNumber }, 
                    dataType: 'json',
                    success: function(data) {
                        if (data.error) {
                            Swal.fire({ icon: 'error', title: 'Error', text: data.error });
                        } else {
                            $('#studentDetails').show(); // Show hidden student detail section
                            $('#id').val(data.id);
                            $('#name').val(data.name);
                            $('#regNumber').val(data.regNumber);
                            $('#phoneNumber').val(data.phoneNumber);
                            $('#email').val(data.email);
                            $('#studentGroup').val(data.studentGroup);
                            $('#gender').val(data.gender);
                            $('#status').val(data.status);
                            $('#department').val(data.department_id);
                            $('#courses').val(data.course_id);
                            $('#studentID').val(data.studentID);


                            // Set the image source if available
                            if (data.image) {
                                $('#studentImage').attr('src', '../StudentImages/' + data.image);
                            } else {
                                $('#studentImage').attr('src', '../StudentImages/default_image.jpg'); // Placeholder if no image
                            }

                            // Fetch courses based on department and preselect the student's course
                            fetchCourses(data.department_id, data.course_id);
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching student details.' });
                    }
                });
            } else {
                Swal.fire({ icon: 'warning', title: 'Warning', text: 'Please enter the registration number.' });
            }
        });

        // Fetch courses when department is changed
        $('#department').on('change', function() {
            var departmentId = $(this).val();
            fetchCourses(departmentId);
        });

        function fetchCourses(departmentId, selectedCourseId = null) {
            $.ajax({
                url: 'fetch_courses.php',
                type: 'GET',
                data: { department_id: departmentId },
                dataType: 'json',
                success: function(data) {
                    var $courseSelect = $('#courses');
                    $courseSelect.empty();
                    $courseSelect.append('<option value="">Select Course</option>');

                    $.each(data, function(index, course) {
                        var isSelected = (selectedCourseId && selectedCourseId == course.course_id) ? 'selected' : '';
                        $courseSelect.append('<option value="' + course.course_id + '" ' + isSelected + '>' + course.courseName + '</option>');
                    });
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'An error occurred while fetching courses.' });
                }
            });
        }

       // Handle form submission for updating student details
$('#updateStudentForm').submit(function(e) {
    e.preventDefault(); // Prevent form from submitting the traditional way

    var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

    // Allowed roles
    var allowedRoles = ['accounts', 'principal', 'quality_assurance', 'front_desk'];

    // Check if user role is in the allowed roles
    if (!allowedRoles.includes(userRole)) {
        Swal.fire({
            icon: 'error',
            title: 'Access Denied !!',
            text: 'You do not have permission to update student details.',
            showConfirmButton: false,
            timer: 3000
        });
        return;
    }

    var formData = new FormData(this); // Get the form data including file uploads

    // Log the contents of FormData for debugging
    formData.forEach((value, key) => {
        if (value instanceof File) {
            console.log(key, value.name); // Log file names
        } else {
            console.log(key, value); // Log normal form values
        }
    });

    // AJAX call to update student details
    $.ajax({
        url: 'UpdateStudent.php', // Endpoint for updating student details
        type: 'POST',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(response) {
            // Parse the response
            try {
                var result = typeof response === 'string' ? JSON.parse(response) : response;

                // Check if the update was successful
                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Student details updated successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Additional actions (if necessary)
                    });
                } 
                // Check if the image already exists
                else if (result.image_exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Image already exists. Please choose a different file.',
                        showConfirmButton: true
                    });
                } 
                // Handle other errors
                else if (result.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.error,
                        showConfirmButton: true
                    });
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred.',
                    showConfirmButton: true
                });
            }
        },
        error: function(xhr, status, error) {
            // Handle AJAX errors
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while updating student details.',
                showConfirmButton: true
            });
        }
    });
});

    });
    </script>
</body>
</html>
