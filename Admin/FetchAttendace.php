<?php
require 'session_start.php';
require 'db_connection.php';

// Fetch students and units
$students = $conn->query("SELECT id, name, regNumber, course FROM students");
$units = $conn->query("SELECT unit_id, unitName FROM units");

// Handle form submission
$attendance_data = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $unit_id = $_POST['unit_id'];

    $result = $conn->query("SELECT date, status FROM attendance WHERE student_id = $student_id AND unit_id = $unit_id");

    $total_days = 0;
    $present_days = 0;

    while ($row = $result->fetch_assoc()) {
        $total_days++;
        if ($row['status'] == 'P') {
            $present_days++;
        }
        $attendance_data[] = $row;
    }

    $attendance_percentage = ($total_days > 0) ? ($present_days / $total_days) * 100 : 0;
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .attendance-form {
            max-width: 600px;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="fas fa-chart-line"></i> View Attendance</h5>
            </div>
            <div class="card-body">
                <form action="" method="POST" class="attendance-form">
                    <div class="form-group">
                        <label for="student_id">Student:</label>
                        <select id="student_id" name="student_id" class="form-control" required>
                            <?php while ($row = $students->fetch_assoc()): ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name'] . " (" . $row['regNumber'] . ")"; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="unit_id">Unit:</label>
                        <select id="unit_id" name="unit_id" class="form-control" required>
                            <?php while ($row = $units->fetch_assoc()): ?>
                                <option value="<?php echo $row['unit_id']; ?>"><?php echo $row['unitName']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">View Attendance</button>
                </form>

                <?php if (!empty($attendance_data)): ?>
                    <h5 class="mt-4">Attendance Data:</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance_data as $data): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($data['date']); ?></td>
                                    <td><?php echo htmlspecialchars($data['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <h5 class="mt-4">Attendance Percentage: <?php echo round($attendance_percentage, 2); ?>%</h5>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
