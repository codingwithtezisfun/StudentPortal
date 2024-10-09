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
    <title>Fetch Student Fee Statement</title>
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
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-money-check-alt" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    Fetch Student Fee Statement
                </h2>
            </div>
            <div class="card-body">
                <form id="fetchFeeForm" class="border p-4">
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
                            <button type="button" id="fetchStudentFeeBtn" class="btn btn-primary">Fetch Fee Statement</button>
                        </div>
                    </div>
                    <div id="feeStatementDetails" style="display:none;">
                        <h4>Fee Statement Details</h4>
                        <div id="feeDetailsContainer">
                            <!-- Fee statement details will be populated here -->
                        </div>
                        <hr>
                        <h4>Total Amount Paid per Year</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Total Amount Paid</th>
                                </tr>
                            </thead>
                            <tbody id="totalAmountPaidTable">
                                <!-- Total amounts will be populated here -->
                            </tbody>
                        </table>
                        <hr>
                        <h4>Current Balance</h4>
                        <div id="balanceContainer">
                            <!-- Balance will be populated here -->
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
            $('#fetchStudentFeeBtn').click(function() {
                var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                // Check if user is 'accounts'
                if (userRole !== 'accounts') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'You do not have permission to fetch student fee records.',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    return;
                }
                var regNumber = $('#regNumber').val();
                $.ajax({
                    url: 'fetch_student_fee.php',
                    type: 'POST',
                    data: { fetch_fee: true, regNumber: regNumber },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            displayFeeStatement(response.feeStatements);
                            console.log(response.feeStatements);
                            console.log(response.totalAmountPaid);
                            console.log(response.balance);
                            displayTotalAmountPaid(response.totalAmountPaid);
                            displayBalance(response.balance);
                            $('#feeStatementDetails').show();
                        } else {
                            Swal.fire('Error', response.message, 'error');
                            $('#feeStatementDetails').hide();
                        }
                    }
                });
            });
        });

        function displayFeeStatement(feeStatements) {
            var html = '<table class="table table-bordered">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>Reference Number</th>'; // Reference number column
            html += '<th>Date Paid</th>';
            html += '<th>Intake</th>';
            html += '<th>Amount Paid</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            feeStatements.forEach(function(statement) {
                html += '<tr>';
                html += '<td>' + statement.id + '</td>'; // Reference number
                html += '<td>' + (statement.date !== '0000-00-00' ? statement.date : '') + '</td>';
                html += '<td>' + statement.intake + '</td>';
                html += '<td>' + statement.amount_paid + '</td>';
                html += '</tr>';
            });

            html += '</tbody>';
            html += '</table>';

            $('#feeDetailsContainer').html(html);
        }



        function displayTotalAmountPaid(totalAmountPaid) {
            var html = '<table class="table table-bordered">';
            html += '<thead>';
            html += '<tr>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            Object.keys(totalAmountPaid).forEach(function(year) {
                html += '<tr>';
                html += '<td>' + year + '</td>';
                html += '<td>' + totalAmountPaid[year] + '</td>';
                html += '</tr>';
            });

            html += '</tbody>';
            html += '</table>';

            $('#totalAmountPaidTable').html(html);
        }


        function displayBalance(balance) {
            var html = '<div class="form-row mb-3">';
            html += '<div class="col-md-4">';
            html += '<label>Current Balance</label>';
            html += '</div>';
            html += '<div class="col-md-8">';
            html += '<input type="text" class="form-control" value="' + balance + '" readonly>';
            html += '<small style="color:red;">* Positive fee balance may indicate that the student has not reported session</small>'
            html += '</div>';
            html += '</div>';

            $('#balanceContainer').html(html);
        }
    </script>
</body>
</html>
