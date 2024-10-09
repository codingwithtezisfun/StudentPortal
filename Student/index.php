<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo '<script type="text/javascript">
            window.location.href = "loginForm.php"; // Redirect to the login form
            window.close(); // Close the current window
          </script>';
    exit();
}
include 'db_connection.php';

$email = $_SESSION['email'];
$stmt = $conn->prepare("SELECT name FROM students WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $student = $result->fetch_assoc();
    $student_name = $student['name'];
} else {
    $student_name = "Student";
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style>
    body {
        display: flex;
        flex-direction: column;
    }

    .sidebar {
        height: 100vh;
        width: 300px;
        position: fixed;
        top: 0;
        left: 0;
        background-color: #36454f;
        color: white;
        transition: width 0.4s;
        overflow-y: auto; 
        overflow-x: hidden;
    }

    .footer {
        bottom: 0;
    }

    .sidebar.collapsed {
        width: 0;
        overflow: hidden;
        transition: width 0.4s;
    }

    .sidebar .nav-link {
        color: white;
    }

    .sidebar .nav-link:hover {
        background-color: #a9a9a9;
    }

    .sidebar .nav-link {
        display: flex;
        align-items: center;
        padding: 20px;
        height: 50px; /* Fixed height for even link sizes */
        box-sizing: border-box;
        white-space: nowrap;
    }

    .sidebar .nav-link i {
        font-size: 1.2rem;
        margin-right: 20px;
    }

    .content {
        margin-left: 302px;
        width: calc(100% - 302px);
        transition: margin-left 0.4s, width 0.4s;
        background-color: #a9a9a9;
        min-height: 100vh;
        transition: width 0.4s;
    }

    .content.collapsed {
        margin-left: 0;
        width: 100%;
        transition: width 0.4s;
    }

    @media (max-width: 768px) {
        .sidebar {
            display: none;
            transition: width 0.4s;
        }

        .sidebar.expanded {
            display: block;
            width: 300px;
            transition: width 0.7s;
        }

        .content {
            margin-left: 0;
            width: 100%;
            transition: width 0.7s;
        }

        .content.expanded {
            margin-left: 300px;
            width: calc(100% - 300px);
            transition: width 0.7s;
        }

        #sidebarToggleSmall {
            display: block;
        }

        .footer {
            bottom: 0;
            width: 100%;
        }
    }

    @media (min-width: 769px) {
        #sidebarToggleSmall {
            display: none;
        }
    }

    .navbar {
        background-color: #36454f;
        color: #ffffff;
        padding: 10px 15px;
        overflow: hidden;
        position: fixed;
    }

    .nav-link {
        color: #ffffff;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .navbar .nav-link i {
        margin-left: 20px;
    }

    .ElementsContainer {
        flex-wrap: nowrap !important;
        align-items: center;
        justify-content: flex-start;
        box-sizing: border-box;
        overflow-y: auto;
        background-color: #a9a9a9;
    }

    #logout {
        margin-bottom: 0px;
    }

    .nav {
        margin-bottom: 100px;
    }

    #themeToggle {
        margin-right: 50px;
        font-size: 23px;
        margin-top: 5px;
    }

    .light-theme .sidebar {
        background-color: #bfc1c2;
        color: #343a40;
    }

    .light-theme .sidebar .nav-link {
        color: #343a40;
    }

    .light-theme .sidebar .nav-link:hover {
        background-color: #ced4da;
    }

    .light-theme .content {
        background-color: #f5f5f5;
    }

    .light-theme .navbar {
        background-color: #bfc1c2;
        color: #343a40;
    }

    .light-theme .nav-link {
        color: #343a40;
    }

    .light-theme .theme-icon {
        color: #343a40;
    }
    </style>
</head>
<body>
<div class="sidebar">
    <div class="d-flex align-items-center justify-content-center p-3">
        <h4 class="mb-0">MY DASHBOARD</h4>
    </div>
    <nav class="nav flex-column">
        <a id="homeLink" class="nav-link" href="#"><i class="fas fa-home"></i> Home</a>
        <a id="profile" class="nav-link" href="#"><i class="fas fa-user-circle"></i> My Profile</a>
        <a id="feeStatement" class="nav-link" href="#"><i class="fas fa-file-invoice-dollar"></i> My Fee Statement</a>
        <a id="results" class="nav-link" href="#"><i class="fa-solid fa-a"></i> My Grades</a>
        <a id="units" class="nav-link" href="#"><i class="fa-solid fa-laptop-code"></i> My Units</a>
        <a id="regUnits" class="nav-link" href="#"><i class="fa-solid fa-pen-to-square"></i> Register Units</a>
        <a id="CourseUnits" class="nav-link" href="#"><i class="fa-solid fa-book-open-reader"></i> Course Units</a>
        <a id="attendance" class="nav-link" href="#"><i class="fa-solid fa-clipboard-user"></i>My Attendance</a>
        <a id="detailed" class="nav-link" href="#"><i class="fa-solid fa-circle-info"></i>Detailed Attendance</a>
        <a id="session" class="nav-link" href="#"><i class="fa-solid fa-calendar-check"></i> Report Session</a>
        <a id="noticeBoard" class="nav-link" href="#"><i class="fas fa-thumbtack"></i> Notice Board</a>
        <a id="announcements" class="nav-link" href="#"><i class="fas fa-bullhorn"></i> My Notifications</a>
        <a id="queries" class="nav-link" href="#"><i class="fas fa-question-circle"></i> Queries</a>
        <a id="staff" class="nav-link" href="#"><i class="fas fa-user-tie"></i> All Staff</a>
        <a id="logout-link" class="nav-link" href="#"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</div>
<div class="content">
    <nav class="navbar navbar-expand-md">
        <button id="sidebarToggleSmall" class="btn btn-light d-md-none">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ml-auto">
            <i id="themeToggle" class="fas fa-moon theme-icon"></i>
            <span class="navbar-text">
                <i style="margin-right:10px;" class="fas fa-user"></i>
                <?php echo htmlspecialchars($student_name); ?>
            </span>
        </div>
    </nav>

    <div id="ElementsContainer" class="ElementsContainer">
        <div id="mainpage"></div>    
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Toggle sub-links visibility
    document.querySelectorAll(".nav-link.main-link").forEach(function (link) {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const subLinks = this.nextElementSibling;
            if (subLinks) {
                if (subLinks.style.display === "block") {
                    subLinks.style.display = "none";
                } else {
                    document.querySelectorAll(".sub-links").forEach(function (subLink) {
                        subLink.style.display = "none";
                    });
                    subLinks.style.display = "block";

                    // Timeout to close the sub-links after 20 seconds
                    setTimeout(function () {
                        subLinks.style.display = "none";
                    }, 20000);
                }
            }
        });
    });

    // Function to close sidebar when a sidebar link is clicked on small screens
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                document.querySelector('.sidebar').classList.remove('expanded');
                document.querySelector('.content').classList.remove('expanded');
                //document.querySelector('.navbar-nav').classList.remove('d-none');
            }
        });
    });

    // Toggle sidebar on small screens
    document.getElementById('sidebarToggleSmall').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('expanded');
        document.querySelector('.content').classList.toggle('expanded');
        //document.querySelector('.navbar-nav').classList.toggle('d-none');
    });
    // Close sidebar when clicking outside on small screens
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768) {
            if (!event.target.closest('.sidebar') && !event.target.closest('#sidebarToggleSmall')) {
                document.querySelector('.sidebar').classList.remove('expanded');
                document.querySelector('.content').classList.remove('expanded');
                //document.querySelector('.navbar-nav').classList.remove('d-none');
            }
        }
    });

    // Add click event to moon icon to toggle themes
    const themeToggle = document.getElementById('themeToggle');
    themeToggle.addEventListener('click', function() {
        document.body.classList.toggle('light-theme');
        if (document.body.classList.contains('light-theme')) {
            themeToggle.classList.replace('fa-moon', 'fa-sun');
        } else {
            themeToggle.classList.replace('fa-sun', 'fa-moon');
        }
    });
});
$(document).ready(function() {
    function loadContent(linkId, url) {
        $('#' + linkId).click(function(event) {
            event.preventDefault(); // Prevent default link behavior (e.g., page reload)
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if ($('#ElementsContainer').children().length > 0) {
                        $('#ElementsContainer').empty(); // or $('#ElementsContainer').html('');
                    }
                    $('#ElementsContainer').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching content:', error);
                }
            });
        });
    }

    loadContent('homeLink', 'MainPage.php');
    loadContent('profile', 'MyProfile.php');
    loadContent('feeStatement', 'MyFeeStatement.php');
    loadContent('results', 'Grades.php');
    loadContent('announcements', 'MyNotifications.php');
    loadContent('noticeBoard', 'MyNotices.php');
    loadContent('queries', 'ContactStaff.php');
    loadContent('units', 'MyUnits.php');
    loadContent('CourseUnits', 'CourseUnits.php');
    loadContent('regUnits', 'RegisterUnits.php');
    loadContent('session', 'ReportSession.php');
    loadContent('staff', 'AllStaff.php');
    loadContent('attendance', 'AttendanceViewForm.php');
    loadContent('detailed', 'DetailedAttendanceForm.php');
   
});


    $(document).ready(function() {
        // Function to load content via AJAX
        function loadContent(url, targetElement) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $(targetElement).html(response); // Load response into specified target element
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching content:', error);
                }
            });
        }

        loadContent('MainPage.php', '#mainpage');
    });

    $('#logout-link').on('click', function(event) {
  event.preventDefault();

  Swal.fire({
    title: 'Logging Out',
    text: 'You will be redirected shortly.',
    timer: 3000,
    timerProgressBar: true,
    showConfirmButton: false, 
    didOpen: () => {
         Swal.showLoading();
    },
    willClose: () => {
      const swalElement = Swal.getContainer();
    }
  }).then((result) => {
    if (result.dismiss === Swal.DismissReason.timer) {
      $.ajax({
        url: 'clearSession.php',
        type: 'POST',
        success: function(response) {
          var result = JSON.parse(response);
          if (result.status === 'success') {
            window.location.href = 'LoginForm.php';
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while logging out.',
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while logging out.',
          });
        }
      });
    }
  });
});

        </script>
    </body>
    </html>
