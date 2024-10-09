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

$sql = "SELECT * FROM fee";
$result = $conn->query($sql);

$feeStatements = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feeStatements[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Statement Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.16.6/dist/sweetalert2.min.css">
    <style>
        .text-muted {
            color: #6c757d;
            font-size: 0.8rem;
        }

        .card-header {
            background-color: #343a40;
        }
        .card-body{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-money-check-alt" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    Current Fee Rates
                </h2>
            </div>
            <div class="card-body">
                <form id="feeForm" class="border p-4">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="year">Year</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" id="year" name="year" value="<?php echo date('Y'); ?>" placeholder="Enter year">
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="intake">Intake</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="intake" name="intake">
                                <option>Jan-Mar</option>
                                <option>Apr-Jun</option>
                                <option>Jul-Sep</option>
                                <option>Oct-Dec</option>
                            </select>
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
                            <label for="feeAmount">Fee Amount</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" step="0.01" class="form-control" id="feeAmount" name="feeAmount" placeholder="Enter fee amount" required>
                        </div>
                    </div>

                    <div class="form-row mb-3">
                        <div class="col-md-12 text-center">
                            <button style="width:50%;" type="button" class="btn btn-primary" id="addFee">Add Fee</button>
                        </div>
                    </div>

                    <small style="color: red; font-size: 0.8em;">* The table is to be updated strictly before 1st of every intake begins or earlier</small>
                </form>
                        </div>
                <div id="feeTableContainer" class="mt-4"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Check user role and show/hide delete buttons
            var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

            // Set default values for year and intake based on current month
            var currentDate = new Date();
            var currentMonth = currentDate.getMonth();
            var year = currentDate.getFullYear();
            $('#year').val(year);

            var intakeSelect = document.getElementById("intake");

            if (currentMonth >= 0 && currentMonth <= 2) {
                intakeSelect.value = "Jan-Mar";
            } else if (currentMonth >= 3 && currentMonth <= 5) {
                intakeSelect.value = "Apr-Jun";
            } else if (currentMonth >= 6 && currentMonth <= 8) {
                intakeSelect.value = "Jul-Sep";
            } else if (currentMonth >= 9 && currentMonth <= 11) {
                intakeSelect.value = "Oct-Dec";
            }

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

            // Add fee statement button click handler
            $('#addFee').click(function() {
                var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                // Check if user is 'accounts'
                if (userRole !== 'accounts') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'You do not have permission to add fee statements.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                var feeAmount = $('#feeAmount').val();

                // Validate fee amount as numeric
                if (!$.isNumeric(feeAmount)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Fee Amount',
                        text: 'Please enter a valid numeric fee amount.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }

                // Proceed with adding fee statement
                addFee();
            });

            fetchData(); // Fetch and display initial data

            function addFee() {
                var year = $('#year').val();
                var courseId = $('#course').val();
                var intake = $('#intake').val();
                var feeAmount = $('#feeAmount').val();

                // Perform AJAX request to add fee statement
                $.ajax({
                    url: 'insert_fee.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        year: year,
                        course: courseId,
                        intake: intake,
                        fee_amount: feeAmount
                    },
                    
                    success: function(response) {
                        if (response.Result === 'SUCCESS') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: 'Fee statement added successfully.'
                            });
                            // Clear form inputs or update UI as needed
                            $('#year').val('');
                            $('#courseId').val('');
                            $('#intake').val('');
                            $('#feeAmount').val('');
                            // Refresh fee table
                            fetchData();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.Message || 'Failed to add fee statement. Please try again later  .'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to add fee statement. Please try again.'
                        });
                    }
                });
            }

            function fetchData() {
                $.ajax({
                    url: 'fetch_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        displayData(data);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            }

            function displayData(data) {
                var html = '<table class="table table-striped">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>Year</th>';
                html += '<th>Current Intake</th>';
                html += '<th>Course</th>';
                html += '<th>Fee Amount</th>';
                html += '<th>Action</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';

                data.forEach(function(row) {
                    html += '<tr>';
                    html += '<td>' + row.year + '</td>';
                    html += '<td>' + row.intake + '</td>';
                    html += '<td>' + row.course + '</td>';
                    html += '<td>' + row.fee_amount + '</td>';
                    html += '<td><button class="btn btn-sm btn-danger delete-btn" data-id="' + row.id + '">Delete</button></td>';
                    html += '</tr>';
                });

                html += '</tbody>';
                html += '</table>';

                $('#feeTableContainer').html(html);

                // Delete button click handler
                $('.delete-btn').click(function() {
                    var feeId = $(this).data('id');
                    
                    if (userRole !== 'accounts') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Permission Denied',
                            text: 'You do not have permission to delete fee statements.'
                        });
                        return;
                    }

                    deleteFee(feeId);
                });
            }

            // JavaScript function to delete fee statement
            function deleteFee(id) {
                console.log(id); // Check if ID is correctly received
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this fee statement!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // AJAX request to delete fee statement
                        $.ajax({
                            url: 'delete_fee.php',
                            type: 'POST',
                            dataType: 'json',
                            data: { id: id }, // Pass ID parameter here
                            success: function(response) {
                                if (response.Result === 'OK') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: response.Message
                                    });
                                    // Optionally, reload the table or update UI
                                    fetchData(); // Example: Refresh fee table
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: response.Message
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Failed to delete fee statement. Please try again.'
                                });
                            }
                        });
                    }
                });
            }

        });
    </script>
</body>
</html>
