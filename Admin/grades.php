<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #343a40;
        }
        .sub-header {
            background-color: #ff9800;
        }
        .text-center .btn{
            width:100%;
        }
        /* Initially hide the elective row */
        .hidden {
            display: none;
        }
        #fetchForm{
            color:blue;
            font-weight:bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-3">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: #ffffff;">
                    <i class="fas fa-graduation-cap" style="color: #ffffff; font-size: 20px; margin-right: 10px;"></i>
                    Grades Form
                </h2>
            </div>
            <div class="card-body">
                <form id="fetchForm" class="border p-4 mb-3">
                    <!-- Filter Section -->
                    <div class="form-row mb-3">
                        <div class="col-md-4">
                            <label for="department">Department</label>
                            <select class="form-control" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <?php
                                require 'db_connection.php';
                                $sql = "SELECT department_id, departmentName FROM departments";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo '<option value="' . $row['department_id'] . '">' . $row['departmentName'] . '</option>';
                                    }
                                }
                                $conn->close();
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="course">Course</label>
                            <select class="form-control" id="course" name="course" required>
                                <option value="">Select Course</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="studentGroup">Group</label>
                            <input type="text" class="form-control" id="studentGroup" name="studentGroup" placeholder="Enter student group" required>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                       
                        <div class="col-md-4">
                            <label for="stage">Stage</label>
                            <select class="form-control" id="stage" name="stage" required>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="semester">Semester</label>
                            <select class="form-control" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1">Semester 1</option>
                                <option value="2">Semester 2</option>
                            </select>
                        </div>
                    <div class="form-row mb-3 hidden col-md-4" id="electiveCategoryRow">
                        <div class="col-md-12">
                            <label for="electiveCategory">Elective Category:</label>
                            <select class="form-control" id="elective" name="elective">
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

                    
                        <div class="col-md-12 text-center">
                            <button type="submit" class="btn btn-primary mt-4">Fetch</button>
                        </div>
                    </div>
                </form>

                <div id="gradesFormContainer"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fetch courses based on department selection
            $('#department').change(function() {
                var departmentId = $(this).val();
                if (departmentId) {
                    $.ajax({
                        url: 'fetch_courses.php',
                        type: 'GET',
                        data: { department_id: departmentId },
                        dataType: 'json',
                        success: function(data) {
                            var $courseSelect = $('#course');
                            $courseSelect.empty(); // Clear existing options
                            $courseSelect.append('<option value="">Select Course</option>'); // Default option
                            $.each(data, function(index, course) {
                                $courseSelect.append('<option value="' + course.course_id + '">' + course.courseName + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching courses:', error);
                        }
                    });
                } else {
                    $('#course').empty().append('<option value="">Select Course</option>');
                }
            });

          $('#course, #stage').on('change', function() {
            var courseId = $('#course').val();
            var stage = $('#stage').val();

            // Show the elective row and set required attribute if course_id = 20 and stage = 3
            if (courseId == '20' && stage == '3') {
                $('#electiveCategoryRow').removeClass('hidden');
                $('#elective').prop('required', true); 
            } else {
                $('#electiveCategoryRow').addClass('hidden');
                $('#elective').val(''); 
                $('#elective').prop('required', false); 
            }
        });

            $('#fetchForm').on('submit', function(e) {
                e.preventDefault();
                
                // Log the serialized data to console
                var formData = $(this).serialize();
                console.log('Form data submitted:', formData);
                
                $.ajax({
                    type: 'POST',
                    url: 'fetch_grades.php',
                    data: formData,
                    success: function(response) {
                        
                        $('#gradesFormContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching grades:', error);
                    }
                });
            });

            $(document).on('submit', '#gradesForm', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                
                $.ajax({
                    type: 'POST',
                    url: 'save_grades.php',
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success', 
                            title: 'Success!',
                            text: 'Grades saved successfully!',
                            confirmButtonText: 'Great'
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error', 
                            title: 'Error Saving Grades',
                            text: `An error occurred: ${error}. Status: ${status}`,
                            footer: `<pre>${xhr.responseText}</pre>`,  
                            confirmButtonText: 'Okay'
                        });
                    }

                });
            });
        });
    </script>
</body>
</html>
