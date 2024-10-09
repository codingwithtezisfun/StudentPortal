<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    .card {
    margin: 5px;
    }
    .hidden {
    display: none;
    }
    .active-button {
    border: 3px solid blue;
    animation: flash 2s infinite;
     opacity: 1;
    }

    @keyframes flash {
    0% {
        border-color: blue;
    }
    50% {
        border-color: #6ca0dc;
    }
    100% {
        border-color: blue;
    }
    }
    .transparent-button {
    opacity: 0.3;
    }
    .card-header{
        background-color: #343a40;
        padding:15px;
        text-align:center;
    }
    .card-body{
        color:blue;
        font-weight:bold;
    }

        </style>

</head>
<body>
    <div class="card mt-5">
        <div class="card-header text-white">
            <h5 class="card-title mb-0"><i class="fas fa-calendar-check"></i> Record Attendance</h5>
        </div>
        <div class="card-body">
            <form id="fetchStudentsForm">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="department">Department:</label>
                        <select class="form-control" id="department" name="department">
                            <option value="" selected>Select Department</option>
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
                    <div class="form-group col-md-4">
                        <label for="course">Course:</label>
                        <select class="form-control" id="course" name="course" required>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="stage">Stage:</label>
                        <select class="form-control" id="stage" name="stage">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="semester">Semester:</label>
                        <select class="form-control" id="semester" name="semester">
                            <option value="1">1</option>
                            <option value="2">2</option>   
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="group">Group:</label>
                        <input type="text" id="group" name="group" class="form-control">
                    </div>
                    <div class="form-row mb-3 hidden col-md-4" id="electiveCategoryRow">
                        <div class="col-md-12">
                            <label style="color:red;" for="electiveCategory">Elective Category:</label>
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

                    
                </div>
                <button type="button" class="btn btn-primary" onclick="fetchStudents()">Fetch Students</button>
            </form>

            <form id="recordAttendanceForm" class="mt-4 hidden">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="unit">Unit:</label>
                        <select class="form-control" id="unit" name="unit" required>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-4">
                        <label for="weekStart">Week Start (Monday):</label>
                        <input type="date" id="weekStart" name="weekStart" class="form-control" required>
                    </div>
                </div>
                <div class="form-group col-md-12">
                <label for="daysOfWeek">Days to mark attendance:</label>
                <div class="row">
                    <div class="col-md-4">
                        <label for="monday">Monday</label>
                        <select id="monday-sessions" class="form-control" data-day="monday">
                            <option value="">Select Session</option>
                            <option value="morning">Morning</option>
                            <option value="mid-morning">Mid</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="tuesday">Tuesday</label>
                        <select id="tuesday-sessions" class="form-control" data-day="tuesday">
                            <option value="">Select Session</option>
                            <option value="morning">Morning</option>
                            <option value="mid-morning">Mid</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="wednesday">Wednesday</label>
                        <select id="wednesday-sessions" class="form-control" data-day="wednesday">
                            <option value="">Select Session</option>
                            <option value="morning">Morning</option>
                            <option value="mid-morning">Mid</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label for="thursday">Thursday</label>
                        <select id="thursday-sessions" class="form-control" data-day="thursday">
                            <option value="">Select Session</option>
                            <option value="morning">Morning</option>
                            <option value="mid-morning">Mid</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="friday">Friday</label>
                        <select id="friday-sessions" class="form-control" data-day="friday">
                            <option value="">Select Session</option>
                            <option value="morning">Morning</option>
                            <option value="mid-morning">Mid</option>
                            <option value="evening">Evening</option>
                        </select>
                    </div>
               </div>

                <table class="table mt-4">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Reg Number</th>
                            <th id="monday-header" class="day-column hidden">Monday</th>
                            <th id="tuesday-header" class="day-column hidden">Tuesday</th>
                            <th id="wednesday-header" class="day-column hidden">Wednesday</th>
                            <th id="thursday-header" class="day-column hidden">Thursday</th>
                            <th id="friday-header" class="day-column hidden">Friday</th>
                        </tr>
                    </thead>
                            <tbody id="studentsTableBody">
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-primary">Submit Attendance</button>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
   
function fetchStudents() {
    const course = $('#course').val();
    const group = $('#group').val();
    const stage = $('#stage').val();
    const semester = $('#semester').val();
    const elective = $('#elective').val();
    
    console.log('Fetching students with:', { course, group, stage, semester, elective }); 
    fetchUnits(course, stage, semester, elective); // Fetch units after students are fetched

    $.ajax({
        url: 'fetch_students_attendance.php',
        type: 'POST',
        data: { course: course, group: group },
        dataType: 'json',
        success: function(data) {
            const studentsTableBody = $('#studentsTableBody');
            studentsTableBody.empty();

            if (data.length === 0) {
                studentsTableBody.append('<tr><td colspan="8">No students found</td></tr>');

                // Replace console log with SweetAlert notification
                Swal.fire({
                    icon: 'info', 
                    title: 'No Students Found',
                    text: 'No students were found for the selected course and group.',
                    confirmButtonText: 'Okay'
                });

                return;
            }

            data.forEach(student => {
                const row = `
                <tr>
    <td>${student.name}</td>
    <td>${student.regNumber}</td>
                        <td id="monday-column" class="day-column col-display hidden">
                        <div class="session morning hidden">
                            <strong>Monday Morning</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'monday_am')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'monday_am')">A</button>
                        </div>
                        <div class="session mid-morning hidden">
                            <strong>Monday Mid</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'monday_mid')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'monday_mid')">A</button>
                        </div>
                        <div class="session evening hidden">
                            <strong>Monday Evening</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'monday_eve')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'monday_eve')">A</button>
                        </div>
                        <input type="hidden" name="attendances[${student.id}][monday_am]" value="0">
                        <input type="hidden" name="attendances[${student.id}][monday_mid]" value="0">
                        <input type="hidden" name="attendances[${student.id}][monday_eve]" value="0">
                    </td>
                    <td id="tuesday-column" class="day-column col-display hidden">
                        <div class="session morning hidden">
                            <strong>Tuesday Morning</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'tuesday_am')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'tuesday_am')">A</button>
                        </div>
                        <div class="session mid-morning hidden">
                            <strong>Tuesday Mid</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'tuesday_mid')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'tuesday_mid')">A</button>
                        </div>
                        <div class="session evening hidden">
                            <strong>Tuesday Evening</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'tuesday_eve')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'tuesday_eve')">A</button>
                        </div>
                        <input type="hidden" name="attendances[${student.id}][tuesday_am]" value="0">
                        <input type="hidden" name="attendances[${student.id}][tuesday_mid]" value="0">
                        <input type="hidden" name="attendances[${student.id}][tuesday_eve]" value="0">
                    </td>
                    <td id="wednesday-column" class="day-column col-display hidden">
                        <div class="session morning hidden">
                            <strong>Wednesday Morning</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'wednesday_am')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'wednesday_am')">A</button>
                        </div>
                        <div class="session mid-morning hidden">
                            <strong>Wednesday Mid</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'wednesday_mid')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'wednesday_mid')">A</button>
                        </div>
                        <div class="session evening hidden">
                            <strong>Wednesday Evening</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'wednesday_eve')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'wednesday_eve')">A</button>
                        </div>
                        <input type="hidden" name="attendances[${student.id}][wednesday_am]" value="0">
                        <input type="hidden" name="attendances[${student.id}][wednesday_mid]" value="0">
                        <input type="hidden" name="attendances[${student.id}][wednesday_eve]" value="0">
                    </td>
                    <td id="thursday-column" class="day-column col-display hidden">
                        <div class="session morning hidden">
                            <strong>Thursday Morning</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'thursday_am')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'thursday_am')">A</button>
                        </div>
                        <div class="session mid-morning hidden">
                            <strong>Thursday Mid</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'thursday_mid')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'thursday_mid')">A</button>
                        </div>
                        <div class="session evening hidden">
                            <strong>Thursday Evening</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'thursday_eve')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'thursday_eve')">A</button>
                        </div>
                        <input type="hidden" name="attendances[${student.id}][thursday_am]" value="0">
                        <input type="hidden" name="attendances[${student.id}][thursday_mid]" value="0">
                        <input type="hidden" name="attendances[${student.id}][thursday_eve]" value="0">
                    </td>
                    <td id="friday-column" class="day-column col-display hidden">
                        <div class="session morning hidden">
                            <strong>Friday Morning</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'friday_am')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'friday_am')">A</button>
                        </div>
                        <div class="session mid-morning hidden">
                            <strong>Friday Mid</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'friday_mid')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'friday_mid')">A</button>
                        </div>
                        <div class="session evening hidden">
                            <strong>Friday Evening</strong><br>
                            <button type="button" class="btn btn-success" onclick="markAttendance(this, 'P', 'friday_eve')">P</button>
                            <button type="button" class="btn btn-danger" onclick="markAttendance(this, 'A', 'friday_eve')">A</button>
                        </div>
                        <input type="hidden" name="attendances[${student.id}][friday_am]" value="0">
                        <input type="hidden" name="attendances[${student.id}][friday_mid]" value="0">
                        <input type="hidden" name="attendances[${student.id}][friday_eve]" value="0">
                    </td>
                </tr>
                `;
                studentsTableBody.append(row);
            });

            $('#recordAttendanceForm').removeClass('hidden');
        },
       error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error', 
                title: 'AJAX Error',
                text: `An error occurred: ${error}. Status: ${status}`,
                footer: `<pre>${xhr.responseText}</pre>`, 
                confirmButtonText: 'Okay'
            });
        }

    });
}
function markAttendance(button, status, session) {
   // Get week start date
        // Get week start date
   const weekStart = $('#weekStart').val();
    const weekStartDate = new Date(weekStart);

    if (!weekStart || isNaN(weekStartDate.getTime())) {
        Swal.fire({
            title: "Error",
            text: "Enter a valid (Monday) week start date.",
            icon: "error",
            confirmButtonText: "OK",
        });
        return;
    }

    if (weekStartDate.getDay() !== 1) {
        Swal.fire({
            title: "Error",
            text: "Week start date must be a Monday.",
            icon: "error",
            confirmButtonText: "OK",
        });
        return;
    }

    // Determine the day from the button's parent column ID
    const dayColumnId = $(button).closest('td').attr('id');
    const day = dayColumnId.split('-')[0]; // Extract day (e.g., 'monday')

    // Calculate the date for the day
    const date = getDateForDay(weekStart, getDayOffset(day));

    // Find the studentId from the hidden input in the current row
    const studentIdElement = $(button).closest('tr').find('input[type="hidden"]').first();
    const studentId = studentIdElement.attr('name').match(/\d+/)[0]; // Extract studentId from name

    if (!studentId) {
        console.error('Student ID not found.');
        return;
    }

    // Build the input name using the date, session, and studentId
   // Build the input name using the session and studentId
   const inputName = `attendances[${studentId}][${session}]`;
    // Find the input element
    const input = $(`input[name="${inputName}"]`);

    if (input.length > 0) {
        // Update the main attendance input value
        input.val(status); // 'P' or 'A'
        console.log(`Updated input ${inputName} to ${status}`);
    } else {
        console.error(`Input not found for ${inputName}`);
    }

    // Update button states
    $(button).siblings('button').removeClass('active-button');
    $(button).addClass('active-button');
    $(button).siblings('button').addClass('transparent-button');
    $(button).removeClass('transparent-button');
}

// Ensure date format is correct
function getDateForDay(weekStart, daysToAdd) {
    const date = new Date(weekStart);
    date.setDate(date.getDate() + daysToAdd);
    return date.toISOString().split('T')[0]; // Format date as YYYY-MM-DD
}


    // Get day offset
    function getDayOffset(day) {
        const dayOffsets = {
            monday: 0,
            tuesday: 1,
            wednesday: 2,
            thursday: 3,
            friday: 4
        };
        return dayOffsets[day] || 0;
    }

       function processRow(row, unit, weekStart, attendances) {
    const studentId = row.find('input[type="hidden"]').first().attr('name').match(/\d+/)[0];
    if (!studentId) {
        console.log('No Student ID found.');
        return;
    }

    const studentAttendance = {
        student_id: studentId,
        unit: unit,
        active_days: {}
    };

    // For each day (Monday to Friday)
    ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'].forEach(function(day) {
        const dayColumn = row.find(`#${day}-column`);
        if (dayColumn.length === 0) {
            console.log('No day column found for:', day);
            return;
        }

        const dayAttendance = {};
        let hasActiveButtons = false;

        dayColumn.find('button.active-button').each(function() {
            const button = $(this);
            const onclickAttr = button.attr('onclick'); // Get the onclick attribute
            console.log(`Onclick attribute: ${onclickAttr}`); // Log the onclick attribute for debugging

            // Extract session and status using regex
            const match = onclickAttr.match(/markAttendance\(this, '([^']+)', '([^']+)'\)/);
            if (match) {
                const status = match[1]; // Extract status ('P' or 'A')
                const session = match[2]; // Extract session (e.g., 'monday_eve')

                // Log the session and status
                console.log(`Fetched session: ${session}, Status: ${status}`);
                
                dayAttendance[session] = status; // Store the status for the session (P/A)
                hasActiveButtons = true;
            } else {
                console.log('Failed to parse session and status from onclick attribute.');
            }
        });



        // If there were active buttons, calculate the date for that day and store attendance
        if (hasActiveButtons) {
            const date = getDateForDay(weekStart, getDayOffset(day));
            studentAttendance.active_days[date] = dayAttendance;
            console.log(`Added attendance for ${day} on ${date}:`, dayAttendance);
        }
    });

    // Add student attendance if at least one day has been marked
    if (Object.keys(studentAttendance.active_days).length > 0) {
        attendances.push(studentAttendance);
        console.log('Added Student Attendance:', studentAttendance);
    }
}


     $('#recordAttendanceForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Form submitted');

        const attendances = [];
        const rows = $('#studentsTableBody tr');
        const unit = $('#unit').val();
        const weekStart = $('#weekStart').val();
        
        rows.each(function() {
            processRow($(this), unit, weekStart, attendances);
        });

        console.log('Final Attendances:', attendances);


        $.ajax({
    url: 'submit_attendance.php',
    type: 'POST',
    data: { attendances: JSON.stringify(attendances) },
    success: function(response) {
        var jsonResponse = JSON.parse(response);
        fetchStudents();
        var weekStartInput = document.getElementById('weekStart');
        weekStartInput.value = '';
        
        if (jsonResponse.requiresUpdate) {
            Swal.fire({
                title: 'Update Available',
                text: jsonResponse.message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Update',
                cancelButtonText: 'No, Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Trigger another AJAX request to handle the update
                    $.ajax({
                        url: 'submit_attendance.php',
                        type: 'POST',
                        data: { attendances: JSON.stringify(attendances), update: true },
                        success: function(updateResponse) {
                            Swal.fire({
                                title: 'Success',
                                text: 'Attendance has been updated successfully!',
                                icon: 'success',
                                confirmButtonText: 'Ok'
                            });
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'Failed to update attendance. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'Ok'
                            });
                        }
                    });
                }
            });
        } else {
            Swal.fire({
                title: 'Success',
                text: jsonResponse.message,
                icon: 'success',
                confirmButtonText: 'Ok'
            });
        }
    },
    error: function() {
        Swal.fire({
            title: 'Error',
            text: 'Failed to record attendance. Please try again.',
            icon: 'error',
            confirmButtonText: 'Ok'
        });
    }
});

    });
$(document).ready(function () {
  $("#department").on("change", function () {
    let departmentId = $(this).val();
    console.log("Selected Department ID:", departmentId); // Log department ID
    $.ajax({
      url: "fetch_courses.php",
      type: "GET",
      data: { department_id: departmentId },
      dataType: "json",
      success: function (data) {
        console.log("Courses Data:", data); // Log fetched courses data
        let $courseSelect = $("#course");
        $courseSelect.empty();
        $courseSelect.append('<option value="">Select Course</option>');
        $.each(data, function (index, course) {
          $courseSelect.append(
            '<option value="' +
              course.course_id +
              '">' +
              course.courseName +
              "</option>"
          );
        });
      },
      error: function (xhr, status, error) {
        console.error("AJAX Error:", status, error);
        console.log("Response:", xhr.responseText); // Log error response
      },
    });
  });

  $("#course, #stage").on("change", function () {
    var courseId = $("#course").val();
    var stage = $("#stage").val();

    console.log("Course ID:", courseId, "Stage:", stage); // Log course and stage values

    if (courseId == "20" && stage == "3") {
        $('#electiveCategoryRow').removeClass('hidden');
                $('#elective').prop('required', true); 
    } else {
      $("#electiveCategoryRow").addClass("hidden");
      $("#elective").val("");
        $('#elective').prop('required', false); 
    }
  });

  // Fetch units on course and stage change
  $("#course, #stage, #semester, #elective").on("change", function () {
    const course = $("#course").val();
    const stage = $("#stage").val();
    const semester = $("#semester").val();
    const elective = $("#elective").val();


    fetchUnits(course, stage, semester, elective); // Fetch units
  });
});

function fetchUnits(course, stage, semester, elective) {

  $.ajax({
    url: "fetch_units_attendance.php",
    type: "POST",
    data: {
      course: course,
      stage: stage,
      semester: semester,
      elective: elective,
    },
    dataType: "json",
    success: function (data) {
      const unitSelect = $("#unit");
      unitSelect.empty();
      unitSelect.append('<option value="">Select Unit</option>');
      data.forEach((unit) => {
        unitSelect.append(
          `<option value="${unit.unit_id}">${unit.unitName}</option>`
        );
      });
    },
    error: function (xhr, status, error) {
      console.error("AJAX Error:", status, error);
    },
  });
}

function handleSessionSelection() {
  document.querySelectorAll("select[data-day]").forEach((select) => {
    select.addEventListener("change", function () {
      const day = this.getAttribute("data-day");

      // Show the selected day column
      document.querySelectorAll(".day-column").forEach((col) => {
        if (col.id === `${day}-header` || col.id === `${day}-column`) {
          col.classList.remove("hidden");
        }
      });

      // Get the currently selected sessions
      const selectedSessions = Array.from(this.selectedOptions).map(
        (option) => option.value
      );

      // Show all sessions that are either selected or already visible
      document
        .querySelectorAll(`#${day}-column .session`)
        .forEach((sessionDiv) => {
          const sessionClass = sessionDiv.classList.contains("morning")
            ? "morning"
            : sessionDiv.classList.contains("mid-morning")
            ? "mid-morning"
            : "evening";

          // Keep sessions that are either selected or already visible
          if (
            selectedSessions.includes(sessionClass) ||
            !sessionDiv.classList.contains("hidden")
          ) {
            sessionDiv.classList.remove("hidden");
          } else {
            sessionDiv.classList.add("hidden");
          }
        });
    });
  });
}

// Initialize on page load
handleSessionSelection();

</script>
</body>
</html>
