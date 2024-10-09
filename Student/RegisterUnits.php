<?php
require 'session_start.php';
require 'db_connection.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("
    SELECT 
        s.id AS student_id, 
        c.courseName AS course_name, 
        c.course_id 
    FROM 
        students s 
    JOIN 
        courses c ON s.course = c.course_id 
    WHERE 
        s.email = ?
");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "No student found.";
    exit();
}

// Variables to be used in the form
$student_id = $student['student_id'];
$course_name = $student['course_name'];
$course_id = $student['course_id'];

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Units Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .custom-card-header {
            background-color: #36454f;
            color: white;
        }
        .hidden-row {
            display: none;
        }
        .full-width-btn {
            width: 100%;
        }
        .form-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 8px;
        }

    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header custom-card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px;">
                    <i class="fas fa-book-open" style="font-size: 20px; margin-right: 10px;"></i>
                    Register Units Form
                </h2>
            </div>
            <div class="card-body">
                <form id="unitsForm" class="form-container" method="post" action="register_units.php" enctype="multipart/form-data">
                    <input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
                    <input type="hidden" name="course_id" id="courseId" value="<?php echo $course_id; ?>">

                    <!-- Course Field -->
                    <div class="mb-3">
                        <label for="course" class="form-label">Course</label>
                        <input type="text" class="form-control" id="course" value="<?php echo $course_name; ?>" readonly>
                    </div>

                    <!-- Stage and Semester Fields -->
                    <div class="form-row mb-3">
                        <div class="col-md-3">
                            <label for="stage">Stage</label>
                            <select class="form-control" id="stage" name="stage">
                                <option value="">Select Stage</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="semester">Semester</label>
                            <select class="form-control" id="semester" name="semester">
                                <option value="">Select Semester</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                            </select>
                        </div>
                    </div>

                    <!-- Elective Category Section -->
                    <div class="form-row mb-3 hidden-row" id="electiveRow">
                        <div class="col-md-12">
                            <label for="electiveCategory">Elective Category</label>
                            <select class="form-control" id="electiveCategory" name="electiveCategory">
                                <option value="">Select Elective</option>
                                <option value="System Administration">System Administration</option>
                                <option value="Database Administration">Database Administration</option>
                                <option value="Network Administration">Network Administration</option>
                                <option value="Web Software Developer">Web Software Developer</option>
                                <option value="Mobile Software Developer">Mobile Software Developer</option>
                                <option value="Enterprise Software Developer">Enterprise Software Developer</option>
                            </select>
                        </div>
                    </div>

                    <!-- Unit List Section -->
                    <div id="unitList"></div>

                    <!-- Register Button -->
                    <div id="registerBtnContainer" class="mt-4 hidden-row">
                        <button type="submit" class="btn btn-primary full-width-btn" id="registerBtn">Register</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
      $(document).ready(function() {
    // Function to fetch units based on selected stage and semester
    function fetchUnits() {
        var courseId = $('#courseId').val();
        var stage = $('#stage').val();
        var semester = $('#semester').val();
        var electiveCategory = $('#electiveCategory').hasClass('hidden-row') ? null : $('#electiveCategory').val();

        $.ajax({
            url: 'fetchUnits.php',
            type: 'POST',
            data: {
                course_id: courseId,
                stage: stage,
                semester: semester,
                electiveCategory: electiveCategory
            },
            success: function(response) {
                try {
                    var units = JSON.parse(response);
                    var unitList = $('#unitList');
                    unitList.empty(); // Clear existing units

                    if (Array.isArray(units) && units.length > 0) {
                        units.forEach(function(unit) {
                            unitList.append(
                                '<div class="form-check">' +
                                '<input class="form-check-input" type="checkbox" name="unit_ids[]" value="' + unit.unit_id + '" id="unit_' + unit.unit_id + '">' +
                                '<label class="form-check-label" for="unit_' + unit.unit_id + '">' + unit.unit_name + '</label>' +
                                '</div>'
                            );
                        });
                        $('#registerBtnContainer').removeClass('hidden-row'); // Show the Register button
                    } else {
                        unitList.append('<p>No units available for the selected options.</p>');
                        $('#registerBtnContainer').addClass('hidden-row'); // Hide the Register button
                    }
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to parse server response. Check console for details.'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                console.error('Response Text:', jqXHR.responseText); // Log the raw response text
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request. Check console for details.'
                });
            }
        });
    }

    // Show or hide elective category row based on course and stage
    $('#courseId, #stage').on('change', function() {
        var courseId = $('#courseId').val();
        var stage = $('#stage').val();
        
        if (courseId == '20' && stage == '3') {
            $('#electiveRow').removeClass('hidden-row');
             $('#electiveCategory').prop('required', true); 
        } else {
            $('#electiveRow').addClass('hidden-row');
             $('#electiveCategory').prop('required', false); 
             $('#electiveCategory').val(''); 
        }

        // Fetch units when stage changes
        fetchUnits();
    });

    // Fetch units when semester changes
    $('#semester').on('change', function() {
        fetchUnits();
    });

    // Fetch units when elective category changes
    $('#electiveCategory').on('change', function() {
        fetchUnits();
    });

    // Validate form before submission
    $('#unitsForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        var allChecked = true;
        $('input[name="unit_ids[]"]').each(function() {
            if (!$(this).is(':checked')) {
                allChecked = false;
            }
        });

        if (!allChecked) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select all units before registering.'
            });
            return;
        }

        $.ajax({
            url: 'register_units.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                try {
                    var data = typeof response === 'string' ? JSON.parse(response) : response;

                    Swal.fire({
                        icon: data.success ? 'success' : 'error',
                        title: data.success ? 'Success' : 'Error',
                        text: data.message
                    });
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to parse server response. Check console for details.'
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('AJAX Error:', textStatus, errorThrown);
                console.error('Response Text:', jqXHR.responseText); // Log the raw response text
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while processing your request. Check console for details.'
                });
            }
        });
    });
});

    </script>
</body>
</html>
