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
    <title>Delete Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    .card{
        color:blue;
        font-weight:bold;
    }
    .card-header{
        text-align:center;
        background-color: #343a40;
    }
    .response{
        color:black;
    }
    </style>
<body class="container mt-5">
    <div class="card">
        <div class="card-header text-white">
            <h3><i class="fa fa-trash"></i >  Delete Student</h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label for="regNumber">Registration Number:</label>
                <input type="text" class="form-control" id="regNumber" placeholder="Enter student registration number">
            </div>
            <button class="btn btn-primary" id="fetchBtn">Fetch Student</button>

            <h4 class="mt-4">Student Details:</h4>
            <hr>
            <div id="studentDetails"></div>
            <input type="hidden" id="studentId">

            <button class="btn btn-danger mt-3" id="deleteBtn">Delete Student</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
        $('#fetchBtn').click(function() {
            
            var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

            // Allowed roles
            var allowedRoles = ['accounts', 'principal', 'quality_assurance', 'front_desk'];

            // Check if user role is in the allowed roles
            if (!allowedRoles.includes(userRole)) {
                event.preventDefault();  
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied !!',
                    text: 'You do not have permission to delete student.',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;  
            }
            const regNumber = $('#regNumber').val();
            $.ajax({
                url: 'fetchStudent.php',
                type: 'GET',
                data: { regNumber: regNumber },
                success: function(data) {
                    const response = JSON.parse(data);
                    if (response.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.error,
                        });
                    } else {
                        // Display all student details
                        $('#studentName').text(response.name);
                        $('#studentId').val(response.id); // Store ID in hidden input
                        const imagePath = `../StudentImages/${response.image}`;
                        $('#studentDetails').html(`
                            <p><img src="${imagePath}" alt="Student Image" width="100" height="100"></p>
                            <p>Registration Number: <span class="response">${response.regNumber}</span></p>
                            <p>Phone Number: <span class="response">${response.phoneNumber}</span></p>
                            <p>Email: <span class="response">${response.email}</span></p>
                            <p>Student Group: <span class="response">${response.studentGroup}</span></p>
                            <p>Gender: <span class="response">${response.gender}</span></p>
                            <p>Status: <span class="response">${response.status}</span></p>
                            <p>Department ID: <span class="response">${response.department_id}</span></p>
                            <p>Course ID: <span class="response">${response.course_id}</span></p>
                            <p>Course Name: <span class="response">${response.courseName}</span></p>
                                                        
                        `);
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error fetching student details.',
                    });
                }
            });
        });

        $('#deleteBtn').click(function() {
            const studentId = $('#studentId').val();
            console.log('Student ID to delete:', studentId); // Log the student ID before sending the request

            $.ajax({
                url: 'delete_student.php',
                type: 'POST',
                data: { id: studentId },
                success: function(data) {
                    console.log('Raw response data:', data); // Log the raw response data
                    const response = JSON.parse(data);
                    console.log('Parsed response:', response); // Log the parsed response

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Student deleted successfully.',
                        });
                        $('#studentId').val('');
                        $('#studentDetails').html(''); // Clear all student details
                    } else if (response.message.includes('foreign key constraint')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Student cannot be deleted because they are linked to attendance or grades records.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting student:', status, error); // Log error details
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error deleting student.',
                    });
                }
            });
        });
    });
</script>

</script>

</body>
</html>
