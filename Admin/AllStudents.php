<?php
require 'session_start.php';
require 'db_connection.php';

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
    <title>Student Registration Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: bold;
        }
        .filter-section {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
        }
        .filter-section input[type="text"] {
            margin-top: 5px;
        }
        .card {
            overflow-x: auto;
        }
        .container-fluid.icons .jumbotron {
            background-color: #e9ecef;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 0.5rem;
            cursor: pointer;
            max-height: 150px;
            overflow: hidden;
            margin-top: 10px;
        }
        .container-fluid.icons .icon-top,
        .container-fluid.icons .icon-bottom {
            text-align: center;
        }
        .container-fluid.icons .icon-bottom {
            margin-top: 1px;
        }
        .container-fluid.icons .details-link {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }
        .container-fluid.icons {
            min-height: 200px !important;
        }
        .table th {
            color: blue;
        }
        .small {
            color: red;
        }
        .text-center .btn{
            min-width:150px;
        }
        .button{
            width: 150px;
        }
    </style>
</head>
<body>
<div id="UpdateForm">
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-user-graduate" style="font-size: 20px; margin-right: 10px;"></i>
                    All Students
                </h2>
            </div>
            <div style="color: blue;" class="card-body">
                <form id="student-form">
                    <div class="form-row mb-1">
                        <div class="col-md-4">
                            <div class="filter-section">
                                <label for="department">Fetch by Department</label>
                                <select class="form-control" id="department" name="department">
                                    <option value="" selected>Select Department</option> 
                                    <?php
                                    require 'db_connection.php';
                                    $sql = "SELECT department_id, departmentName FROM departments";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                                        }
                                    }
                                    $conn->close();
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-section">
                                <label for="course">Fetch by Course</label>
                                <select class="form-control" id="course" name="course">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-section">
                                <label for="regNumber">Fetch by Registration Number</label>
                                <input type="text" class="form-control" id="regNumber" name="regNumber" placeholder="Enter Registration Number">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-section">
                                <label for="group">Fetch by Group</label>
                                <input type="text" class="form-control" id="group" name="group" placeholder="Enter Group">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-section">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">All</option>
                                    <option value="Active">Active</option>
                                    <option value="InActive">InActive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="filter-section">
                                <h6>You can filter further by combining</h6>
                                <small class="small">* Department and course</small><br>
                                <small class="small">* Department, course, and group</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn button btn-primary" name="fetchStudents">Fetch</button>
                            <button type="button" class="btn button btn-secondary" id="refreshTable">Refresh Table</button>
                        </div>
                    </div>
                </form>
                <div class="form-row mb-3">
                    <div class="col-md-12">
                        <h3>All Students</h3>
                        <table class="table table-bordered" id="studentsTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Registration Number</th>
                                    <th>Phone Number</th>
                                    <th>Email</th>
                                    <th>Course</th>
                                    <th>Department</th>
                                    <th>Group</th>
                                    <th>Gender</th>
                                    <th>Status</th>
                                    <th>Image Preview</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Data will be inserted here via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <!-- Modal -->
<div class="modal fade" id="updateStudentModal" tabindex="-1" role="dialog" aria-labelledby="updateStudentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Content will be loaded dynamically here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to fetch and display students based on filters
        function fetchStudents() {
            let formData = $('#student-form').serialize();
            $.ajax({
                url: 'fetch_students.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#studentsTableBody').html(response);
                },
                error: function() {
                    $('#studentsTableBody').html('<tr><td colspan="12">Error fetching students.</td></tr>');
                }
            });
        }

        // Fetch students on page load with default filters
        fetchStudents();

        // Submit form and fetch students on form submission
        $('#student-form').on('submit', function(event) {
            event.preventDefault();
            fetchStudents();
        });

        // Refresh table without filters
        $('#refreshTable').on('click', function() {
            $('#student-form')[0].reset(); 
            $('#status').val(''); // Ensure "All" is selected by default
            fetchStudents();
        });

        // Handle department change to populate courses
        $('#department').on('change', function() {
            let departmentId = $(this).val();
            $.ajax({
                url: 'fetch_courses.php',
                type: 'GET',
                data: { department_id: departmentId },
                dataType: 'json',
                success: function(data) {
                    let $courseSelect = $('#course');
                    $courseSelect.empty();
                    $courseSelect.append('<option value="">Select Course</option>');
                    $.each(data, function(index, course) {
                        $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                    });
                }
            });
        });

        // Handle "View All" button click to load all students
        $('#studentsTableBody').on('click', '#viewAllBtn', function() {
            $.ajax({
                url: 'fetch_students.php',
                type: 'POST',
                data: $('#student-form').serialize(),
                success: function(response) {
                    $('#studentsTableBody').html(response);
                },
                error: function() {
                    $('#studentsTableBody').html('<tr><td colspan="12">Error fetching students.</td></tr>');
                }
            });
        });
    });

           // Load update form in the modal
    function loadUpdateForm(studentId) {
                var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                //allowed roles
                var allowedRoles = ['accounts', 'principal', 'quality_assurance', 'front_desk'];

                // Check if user role is in the allowed roles
                if (!allowedRoles.includes(userRole)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied !!',
                        text: 'You do not have permission to update student information.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

        $.ajax({
            url: 'UpdateStudentDetails.php', 
            type: 'GET',
            data: { id: studentId },
            success: function(response) {
                $('#updateStudentModal .modal-body').html(response);
                $('#updateStudentModal').modal('show');

                // Re-bind department change event for the modal content
                $('#updateStudentModal').on('change', '#department', function() {
                    let departmentId = $(this).val();
                     console.log(departmentId); // Log the received data
                    $.ajax({
                        url: 'fetch_courses.php',
                        type: 'GET',
                        data: { department_id: departmentId },
                        dataType: 'json',
                        success: function(data) {
                            console.log(data); // Log the received data
                            let $courseSelect = $('#courses');
                            $courseSelect.empty();
                            $courseSelect.append('<option value="">Select Course</option>');
                            $.each(data, function(index, course) {
                                $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                            });
                        }
                    });
                });
            },
            error: function() {
                alert('Error loading update form.');
            }
        });
    }
</script>
</body>
</html>
