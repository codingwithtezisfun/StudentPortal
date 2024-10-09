<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['email'];

$sql = "SELECT id, regNumber, course FROM students WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $student_id = $row['id'];
    $regNumber = $row['regNumber'];
    $course = $row['course'];
} else {
    echo "Student not found";
    exit();
}

// Check if session is already reported
$session_reported = false;
$intake = $year = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_session'])) {
    $intake = $_POST['intake'];
    $year = $_POST['year'];

    $sql_check = "SELECT * FROM session WHERE student_id = $student_id AND intake = '$intake' AND year = $year";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows == 0) {
        $sql_insert = "INSERT INTO session (student_id, year, intake, session_reported) 
                       VALUES ($student_id, $year, '$intake', TRUE)";
        
        if ($conn->query($sql_insert) === TRUE) {
            // Fetch course and intake to calculate fee_amount
            $sql_fee = "SELECT fee_amount FROM fee WHERE course = $course AND intake = '$intake'";
            $result_fee = $conn->query($sql_fee);

            if ($result_fee->num_rows == 1) {
                $row_fee = $result_fee->fetch_assoc();
                $fee_amount = -$row_fee['fee_amount'];

                $sql_update_balance = "UPDATE feeBalance SET balance = balance + $fee_amount WHERE student_id = $student_id";
                $conn->query($sql_update_balance);
            }

            $session_reported = true;
            $success_message = "Session reported successfully";
        } else {
            $error_message = "Error: " . $sql_insert . "<br>" . $conn->error;
        }
    } else {
        $session_reported = true;
        $success_message = "Session already reported for the selected intake and year.";
    }
}

// Check if session is reported without form submission
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $current_month = date('n');
    $session = '';
    if ($current_month >= 1 && $current_month <= 3) {
        $session = "Jan-Mar";
    } elseif ($current_month >= 4 && $current_month <= 6) {
        $session = "Apr-Jun";
    } elseif ($current_month >= 7 && $current_month <= 9) {
        $session = "Jul-Sep";
    } elseif ($current_month >= 10 && $current_month <= 12) {
        $session = "Oct-Dec";
    }

    $year = date('Y');
    $sql_check = "SELECT * FROM session WHERE student_id = $student_id AND intake = '$session' AND year = $year";
    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        $session_reported = true;
        $success_message = "Session already reported for the current intake and year.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Session Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h2>Report Session</h2>
            </div>
            <div class="card-body">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!$session_reported): ?>
                    <form class="border p-4" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-row mb-3">
                            <div class="col-md-4">
                                <label for="regNo">Registration Number</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="regNo" name="reg_no" value="<?php echo $regNumber; ?>" readonly>
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
                                <label for="year">Year</label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="year" name="year" value="<?php echo date('Y'); ?>" placeholder="Enter year">
                            </div>
                        </div>
                        <div class="form-row mb-3">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-primary" name="report_session">Report Session</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="alert alert-success text-center">
                        Session already reported.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var today = new Date();
            var currentMonth = today.getMonth();
            
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
        });
    </script>
</body>
</html>
