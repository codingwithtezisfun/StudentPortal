<?php
include 'db_connection.php';
// Initialize variables
$student_id = $year = $intake = '';
$session_reported = false;
$fee_balance_updated = false;

// Process form data for reporting session
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['report_session'])) {
    $regNumber = $_POST['regNumber'];
    $year = $_POST['year'];
    $intake = $_POST['intake'];

    // Fetch student ID using regNumber
    $sql_student = "SELECT * FROM students WHERE regNumber = '$regNumber'";
    $result_student = $conn->query($sql_student);

    if ($result_student->num_rows == 1) {
        $row_student = $result_student->fetch_assoc();
        $student_id = $row_student['id'];
        $course_id = $row_student['course'];

        // Check if session is already reported
        $sql_check_session = "SELECT * FROM session WHERE student_id = $student_id AND year = $year AND intake = '$intake'";
        $result_check_session = $conn->query($sql_check_session);

        if ($result_check_session->num_rows == 0) {
            // Insert new session record
            $sql_insert_session = "INSERT INTO session (student_id, year, intake, session_reported) VALUES ($student_id, $year, '$intake', true)";
            if ($conn->query($sql_insert_session) === TRUE) {
                $session_reported = true;

                // Fetch fee amount based on intake and course
                $sql_fee = "SELECT fee_amount FROM fee WHERE course = $course_id AND intake = '$intake'";
                $result_fee = $conn->query($sql_fee);

                if ($result_fee->num_rows == 1) {
                    $row_fee = $result_fee->fetch_assoc();
                    $fee_amount = $row_fee['fee_amount'];

                    // Update fee balance in fee table
                    $new_balance = -$fee_amount;
                    $sql_update_fee = "UPDATE fee SET fee_balance = $new_balance WHERE course = $course_id AND intake = '$intake'";
                    if ($conn->query($sql_update_fee) === TRUE) {
                        $fee_balance_updated = true;
                    }
                }
            }
        } else {
            echo "Session already reported for the selected year and intake.";
        }
    } else {
        echo "Student not found with provided Registration Number.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Session</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h2>Report Session</h2>
            </div>
            <div class="card-body">
                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="regNumber">Student Registration Number</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="regNumber" name="regNumber" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="year">Year</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" id="year" name="year" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="intake">Intake</label>
                        </div>
                        <div class="col-md-8">
                            <select class="form-control" id="intake" name="intake" required>
                                <option value="Jan-Mar">Jan-Mar</option>
                                <option value="Apr-Jun">Apr-Jun</option>
                                <option value="Jul-Sep">Jul-Sep</option>
                                <option value="Oct-Dec">Oct-Dec</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-4"></div>
                        <div class="col-md-8">
                            <button style="width:100%;" type="submit" class="btn btn-primary" name="report_session">Report Session</button>
                        </div>
                    </div>
                </form>
                <?php if ($session_reported && $fee_balance_updated) : ?>
                    <div class="alert alert-success mt-3">
                        Session reported successfully and fee balance updated.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
