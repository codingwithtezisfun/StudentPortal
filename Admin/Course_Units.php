<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Units Form</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #343a40;
            color: white;
        }
        .hidden {
            display: none;
        }
        #unitsContainer{
            color:blue;
            font-weight:bold;
        }
        .button{
            width:100%;
        }
    </style>
</head>
<body>
    <div class="container mt-2">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; ">
                    <i class="fas fa-book-open" style="font-size: 20px; margin-right: 10px;"></i>
                    Course-Units Linking Form
                </h2>
            </div>
            <div id="unitsContainer">
                <div class="card-body">
                    <h5>Register Unit Name</h5>
                    <form id="manageUnitsForm" class="border p-4 " method="post" action="" enctype="multipart/form-data">
                        <div class="form-row mb-3">
                            <div class="col-md-8">
                                <label for="unitName">Unit Name</label>
                                <input type="text" class="form-control" id="unitName" name="unitName" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" id="registerUnitBtn" class="btn button btn-primary">Register Unit</button>
                            </div>
                        </div>      
                    </form>
                </div>
                <div class="card-body">
                    <h5>Link unit to course stage and semester</h5>
                    <form id="unitsForm" class="border p-4" method="post" action="" enctype="multipart/form-data">
                        <!-- Register Unit Section -->
                        <div class="form-row mb-3">
                        <div class="col-md-12 position-relative">
                            <input type="text" class="form-control" id="searchUnits" placeholder="Filter units in the dropdown here">
                            <i class="fas fa-search position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);"></i>
                        </div>
                    </div>

                        <div class="form-row mb-3">
                            <div class="col-md-12">
                                 <label for="unitNameSelect">Unit Name</label>
                                <select class="form-control" id="unitNameSelect" name="unitName" required>
                                </select>
                            </div>
                        </div>
                        <div class="form-row mb-3">
                        <div class="col-md-6">
                            <label for="department">Department</label>
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
                        <div class="col-md-6">
                                <label for="courseId">Course</label>
                                <select class="form-control" id="course" name="course" required>
                                <!-- Courses will be loaded based on the selected department -->
                            </select>
                        </div>
                        <div class="form-row mb-3">
                            
                        </div>
                            <div class="col-md-3">
                                <label for="stage">Stage</label>
                                <select class="form-control" id="stage" name="stage">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="semester">Semester</label>
                                <select class="form-control" id="semester" name="semester">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                               
                        <!-- Elective Category Section -->
                        <div class="form-row mb-3 hidden" id="electiveCategoryRow">
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
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" id="registerCourseUnitBtn" class="btn button btn-primary">Register Course Unit</button>
                            </div>
                        </div>
                     
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {

            // Function to load units into the dropdown
            function loadUnits(selectedUnit = '') {
                $.ajax({
                    url: 'getUnits.php', // Endpoint to fetch units
                    method: 'GET',
                    success: function(data) {
                        $('#unitNameSelect').html(data);
                        if (selectedUnit) {
                            $('#unitNameSelect').val(selectedUnit);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to load units.'
                        });
                    }
                });
            }

            loadUnits();

                    // Filter units as user types
            $('#searchUnits').on('keyup', function() {
                var searchTerm = $(this).val().toLowerCase(); 
                $('#unitNameSelect option').each(function() {
                    var unitText = $(this).text().toLowerCase();
                    if (unitText.includes(searchTerm)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
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
              $('#department').on('change', function() {
            var departmentId = $(this).val();

            $.ajax({
                url: 'fetch_courses.php',
                type: 'GET',
                data: { department_id: departmentId },
                dataType: 'json',
                success: function(data) {
                    console.log('Courses data received:', data); // Log the received data
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

            // Handle unit registration form submission
            $('#manageUnitsForm').on('submit', function(event) {
                event.preventDefault();

                var unitName = $('#unitName').val().trim();

                if (unitName === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Unit name cannot be empty.'
                    });
                    return;
                }

                $.ajax({
                    url: 'unitsRegistration.php',
                    type: 'POST',
                    data: { unitName: unitName, action: 'registerUnit' },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                                 timer: 1000,
                                  showConfirmButton: false 
                            }).then(() => {
                                $('#manageUnitsForm')[0].reset();
                                loadUnits(); // Reload units in the dropdown
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                    console.log("AJAX Error:", textStatus, errorThrown); // Log the error details
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing the request.Check if unit is registered already'
                    });
                }

                });
            });

            // Handle course unit registration form submission
            $('#unitsForm').on('submit', function(event) {
                event.preventDefault();

               var formData = {
                    unitName: $('#unitNameSelect').val(),
                    courseId: $('#course').val(),
                    stage: $('#stage').val(),
                    semester: $('#semester').val(),
                    electiveCategory: $('#electiveCategoryRow').hasClass('hidden') ? null : $('#electiveCategory').val(),
                    action: 'registerUnit' 
                };
    console.log('Form Data Submitted:', formData);

                $.ajax({
                    url: 'unitsHandler.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            }).then(() => {
                                $('#unitsForm')[0].reset();
                                $('#electiveCategoryRow').addClass('hidden');
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while registering the course unit.'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
