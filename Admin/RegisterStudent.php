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
    <title>Student Registration Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .card-body {
            color: blue;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-user-graduate" style="font-size: 20px; margin-right: 10px;"></i>
                    Student Registration Form
                </h2>
            </div>
            <div class="card-body">
                <form id="studentForm" enctype="multipart/form-data">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="name">Name</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="regNumber">Registration Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="regNumber" name="regNumber" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="phoneNumber">Phone Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="tel" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="email">Email</label>
                        </div>
                        <div class="col-md-8">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="department">Department</label>
                        </div>
                        <div class="col-md-8">
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <?php
                            require 'db_connection.php';
                            $sql = "SELECT department_id, departmentName FROM departments";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                                }
                            }

                            $conn->close();
                            ?>
                        </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="course">Course</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="course" name="course" required>
                                <!-- Courses will be loaded based on the selected department -->
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="group">Group</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="group" name="group">
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="gender">Gender</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="gender" name="gender" required>
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="studentID">National ID</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="studentID" name="studentID" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="status">Status</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="image">Image</label>
                        </div>
                        <div class="col-md-8">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="image" name="image" accept="image/*" required>
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4"></div>
                        <div class="col-md-8">
                            <button style="width:100%;" type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
                <div id="response" class="mt-3"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
    $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });

    $(document).ready(function() {
          $('#phoneNumber').on('blur', function() {
                var phoneNumber = $(this).val(); 

                if (phoneNumber.length !== 10 || !/^\d+$/.test(phoneNumber)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Phone Number',
                        text: 'Phone number must be exactly 10 digits.',
                    });
                }
            });
        $('#department').on('change', function() {
            var departmentId = $(this).val();

            $.ajax({
                url: 'fetch_courses.php',
                type: 'GET',
                data: { department_id: departmentId },
                dataType: 'json',
                success: function(data) {
                    var $courseSelect = $('#course');
                    $courseSelect.empty(); // Clear existing options
                    $courseSelect.append('<option value="">Select Course</option>'); // Default option

                    if (data.error) {
                        console.error('Error fetching courses:', data.error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                            showConfirmButton: true
                        });
                    } else {
                        $.each(data, function(index, course) {
                            $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error fetching courses:', status, error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching courses.',
                        showConfirmButton: true
                    });
                }
            });
        });

       $("#studentForm").on("submit", function(event) {

    var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

    // Allowed roles
    var allowedRoles = ['accounts', 'principal', 'quality_assurance', 'front_desk'];

    // Check if user role is in the allowed roles
    if (!allowedRoles.includes(userRole)) {
        event.preventDefault();  
        Swal.fire({
            icon: 'error',
            title: 'Access Denied !!',
            text: 'You do not have permission to register student.',
            showConfirmButton: false,
            timer: 3000
        });
        return;  
    }

    event.preventDefault();  

    var formData = new FormData(this);

    $.ajax({
        url: "register_students.php",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log('Raw response received:', response); // Log raw response

            try {
                var result = typeof response === 'string' ? JSON.parse(response) : response;
                console.log('Parsed JSON:', result); // Log parsed JSON

                if (result.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Student registered successfully.',
                        showConfirmButton: false,
                        timer: 900
                    }).then(() => {
                        $('#studentForm')[0].reset();
                        $('#course').empty().append('<option value="">Select Course</option>');
                    });
                } else if (result.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: result.error,
                        showConfirmButton: true
                    });
                } else if (result.image_exists) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning',
                        text: 'Image already exists. Please choose a different file.',
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
            console.error('AJAX error registering student:', status, error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while registering the student.',
                showConfirmButton: true
            });
        }
    });
});

    });
</script>

</body>
</html>