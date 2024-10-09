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
    <title>Add Fee Statement</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
    <style>
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
                    Log Fee Payment
                </h2>
            </div>
            <div class="card-body">
                <form id="feeForm" class="border p-4">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="regNumber">Student Registration Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="regNumber" name="regNumber" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-12">
                            <button type="button" id="fetchStudentBtn" class="btn btn-primary">Fetch Student Details</button>
                        </div>
                    </div>
                    <input type="hidden" id="student_id" name="student_id">
                    <div id="studentDetails" style="display:none;">
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="name">Student Name</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="name" name="name" readonly>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="regNumberDisplay">Registration Number</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="regNumberDisplay" name="regNumberDisplay" readonly>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="intake">Intake</label>
                            </div>
                            <div class="col-md-8">
                            <select class="form-control" id="intake" name="intake" required>
                                <option value="">Select Intake</option> 
                                <option value="Jan-Mar">Jan-Mar</option>
                                <option value="Apr-Jun">Apr-Jun</option>
                                <option value="Jul-Sep">Jul-Sep</option>
                                <option value="Oct-Dec">Oct-Dec</option>
                            </select>

                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="amount_paid">Amount Paid</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" step="0.01" min="0" class="form-control" id="amount_paid" name="amount_paid" required>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="referenceNo">Reference Number</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="referenceNo" name="referenceNo" required placeholder="eg SIFOPV3VO"> 
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="date_paid">Date Paid</label>
                            </div>
                            <div class="col-md-8">
                                <input type="date" class="form-control" id="date_paid" name="date_paid" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-4"></div>
                            <div class="col-md-8">
                                <button style="width:100%;" type="button" name="submit" id="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#fetchStudentBtn').click(function() {
            var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                // Check if user is 'accounts'
                if (userRole !== 'accounts') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'You do not have permission to log fee payments.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }
            var regNumber = $('#regNumber').val();
            $.ajax({
                url: 'fetch_student.php',
                type: 'POST',
                data: { fetch_student: true, regNumber: regNumber },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#student_id').val(response.student_id);
                        $('#name').val(response.name);
                        $('#regNumberDisplay').val(response.regNumber);
                        $('#studentDetails').show();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });

         $('#submit').click(function() {
            var intake = $('#intake').val();
            var amount_paid = $('#amount_paid').val();
            var date_paid = $('#date_paid').val();
            var referenceNo = $('#referenceNo').val();

            // Check if required fields are filled
            if (intake === '' || amount_paid === '' || date_paid === '' || referenceNo === '') {
                Swal.fire('Error', 'Please fill in all required fields.', 'error');
                return;
            }

            $('form').submit(function(event) {
                if ($('#intake').val() === '') {
                    event.preventDefault();
                    alert('Please select an intake');
                }
            });

           var formData = {
                submit_fee: true,
                student_id: $('#student_id').val(),
                amount_paid: amount_paid,
                date_paid: date_paid,
                intake: intake,
                referenceNo: referenceNo 
            };
            $.ajax({
                url: 'fee_payment.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success', response.message, 'success');
                        $('#feeForm')[0].reset();
                        $('#studentDetails').hide();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        });
    });
</script>

</body>
</html>
