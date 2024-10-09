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
    <title>Fee Summary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .button{
                width:100%;
        }
        .card-body{
            color:blue;
            font-weight:bold;
        }
         .card-header {
            background-color: #343a40;
        }
        </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: white;">
                    <i class="fas fa-money-check-alt" style="color: white; font-size: 20px; margin-right: 10px;"></i>
                    Calculate total paid and balance
                </h2>
            </div>
            <div class="card-body">
                <form id="feeSummaryForm">
                    <div class="form-group">
                        <label for="year">Year:</label>
                        <select id="year" name="year" class="form-control">
                            <?php for($i = date('Y'); $i >= 2020; $i--): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="intake">Intake:</label>
                        <select id="intake" name="intake" class="form-control">
                            <option value="Jan-Mar">Jan-Mar</option>
                            <option value="Apr-Jun">Apr-Jun</option>
                            <option value="Jul-Sep">Jul-Sep</option>
                            <option value="Oct-Dec">Oct-Dec</option>
                        </select>
                    </div>
                    <button type="submit" class="btn button btn-primary">Calculate</button>
                </form>
                <div id="results" class="mt-4">
                    <!-- Display results will be here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#feeSummaryForm').on('submit', function(e) {
                e.preventDefault();

                var userRole = '<?php echo isset($_SESSION['role']) ? $_SESSION['role'] : ''; ?>';

                // Check if user is 'accounts'
                if (userRole !== 'accounts') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: 'You do not have permission to fetch accounts reports.',
                        showConfirmButton: false,
                        timer: 4000
                    });
                    return;
                }
                
                var year = $('#year').val();
                var intake = $('#intake').val();

                $.ajax({
                    url: 'fetch_fee_summary.php',
                    type: 'GET',
                    data: { year: year, intake: intake },
                    dataType: 'json',
                   success: function(response) {
                    $('#results').html(`
                        <p>Total Paid for ${intake} ${year}: <i  style="color:red;">Ksh ${response.total_paid}</i></p>
                        <p>Total Current Balance Owed: <i  style="color:red;">Ksh ${response.total_balance}</i></p>
                    `);
                }
                });
            });
        });
    </script>
</body>
</html>
