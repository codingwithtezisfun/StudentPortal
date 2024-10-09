<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses by Department</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background-color: #73c2fb;
        }
        .list-group-item {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: #000000;">
                    <i class="fas fa-book-open" style="color: #000000; font-size: 20px; margin-right: 10px;"></i>
                    Courses by Department
                </h2>
            </div>
            <div class="card-body">
                <form id="coursesForm" class="border p-4">
                    <div id="coursesContainer"></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- Add Font Awesome -->
    <script>
        $(document).ready(function() {
            $.ajax({
                url: 'fetchCourses.php',
                type: 'GET',
                success: function(response) {
                    try {
                        let departments = response.departments;
                        let courses = response.courses;
                        let coursesContainer = $('#coursesContainer');
                        
                        for (let departmentId in departments) {
                            let departmentName = departments[departmentId];
                            let departmentCourses = courses.filter(course => course.department_id == departmentId);
                            
                            let departmentSection = `
                                <div class="mb-4">
                                    <h4>${departmentName}</h4>
                                    <ul class="list-group">
                            `;
                            
                            departmentCourses.forEach(course => {
                                departmentSection += `
                                    <li class="list-group-item">
                                        ${course.courseName} <!-- Corrected field name -->
                                    </li>
                                `;
                            });

                            departmentSection += '</ul></div>';
                            
                            coursesContainer.append(departmentSection);
                        }
                    } catch (e) {
                        console.error('Error processing response:', e);
                    }
                },
                error: function() {
                    console.error('An error occurred while fetching courses.');
                }
            });
        });
    </script>
</body>
</html>
