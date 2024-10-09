<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    echo '<script type="text/javascript">
            window.location.href = "StaffLoginForm.php"; // Redirect to the login form
            window.close(); // Close the current window
          </script>';
    exit();
}

require 'db_connection.php';

$email = $_SESSION['username'];
$stmt = $conn->prepare("SELECT name FROM staff WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $staff = $result->fetch_assoc();
    $staff_name = $staff['name'];
} else {
    $staff_name = "Admin";
}

$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display:flex;
            flex-direction:column;
             background-color:#f2f3f4;
        }
        .navbar {
            background-color: #343a40;
            color: #ffffff;
            padding: 10px 15px; 
            
        }
        .sidebar {
            height: 100vh;
            width: 300px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            color: white;
            transition: width 0.4s;
             overflow-y: auto;
            overflow-x: hidden;
        }

                /* For Chrome, Safari, and Edge
        ::-webkit-scrollbar {
          width: 10px;
          background-color: #f5f5f5; 
        }

        ::-webkit-scrollbar-thumb {
          background-color: #555; 
          border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
          background-color: #888; 
        }
     */
        .sidebar.collapsed {
            width: 0;
            overflow: hidden;
            transition: width 0.4s;
        }

        .sidebar .nav-link {
        color: #fff;
        text-decoration: none;
        padding: 15px;
        display: block;
        margin-left:5px;
      }

      .sidebar .nav-link:hover {
        background-color: #495057;
      }

      .sidebar .sub-links {
        display: none; 
        padding-left: 20px;
      }

      .sidebar .nav-link.main-link.active + .sub-links {
        display: block; 
      }

      .sidebar .sub-link {
        padding: 10px;
        color: #adb5bd;
      }

      .sidebar .sub-link:hover {
        background-color: #6c757d;
      }


        .content {
            margin-left: 0; 
            padding: 2px;
            transition: margin-left 0.4s;
            overflow-x: hidden;
            width: 100%;
            box-sizing: border-box;
            max-width: 100vw;
        }
        .content.collapsed {
            margin-left: 0;
            width: calc(100% - 0px);
        }
        .content.expanded {
            margin-left: 300px;
            width: calc(100% - 300px);
            transition: width 0.4s;
        }
        @media (max-width: 768px) {
            .sidebar {
                display: none;
                margin-left: 0;
            }
            .sidebar.expanded {
                display: block;
                width: 300px;
            }
            .content {
                margin-left: 0;
            }
            .content.expanded {
                margin-left: 300px;
            }
            #sidebarToggleSmall {
                display: block;
            }
        }
        @media (min-width: 769px) {
            #sidebarToggleSmall {
                display: none;
            }
        }
        .ElementsContainer {
            flex-wrap: nowrap!important;
            align-items: center;
            justify-content: flex-start;
            padding: 20px;
            box-sizing: border-box;
            overflow-y:auto;
        }
        .back-to-top {
            position: absolute;
            bottom: 10px;
            right: 10px;
            position: fixed;
            transition: 2s;
        }

        .back-to-top a {
            text-decoration: none;
            border-radius: 50%; 
            display: inline-block; 
        }

    </style>
</head>
<body>
    <div class="sidebar collapsed">
      <div class="d-flex align-items-center justify-content-center p-3">
        <h4 class="mb-0">ADMIN DASHBOARD</h4>
      </div>

      <nav class="nav flex-column">
        <div class="row">
          <div class="col-12">
            <a id="homeLink" class="nav-link" href="#"
              ><i class="fas fa-home"></i> Home</a
            >
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id="profile" class="nav-link" href="#"
              ><i class="fas fa-user-circle"></i> My Profile</a
            >
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-user-graduate"></i> Students</a
            >
            <div class="sub-links">
              <a id="registerStudent" class="nav-link sub-link" href="#"
                >Register Student</a
              >
              <a id="AllStudents" class="nav-link sub-link" href="#"
                >View All Students</a
              >
              <a id="UpdateStudent" class="nav-link sub-link" href="#"
                >Update Student</a
              >
              <a id="deleteStudent" class="nav-link sub-link" href="#"
                >Delete Student</a
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id="gradesLink" class="nav-link main-link" href="#"
              ><i class="fas fa-graduation-cap"></i> Grades</a
            >
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-clipboard-check"></i> Attendance</a
            >
            <div class="sub-links">
              <a id="logAttendance" class="nav-link sub-link" href="#"
                >Log Attendance</a
              >
               <a id="viewAttendance" class="nav-link sub-link" href="#"
                >Individual Student Attendance</a
              >
              <a id="groupAttendance" class="nav-link sub-link" href="#"
                >Specific Group Attendance</a
              >
               <a id="detailedAttendance" class="nav-link sub-link" href="#"
                >Detailed Individual Student Attendance</a
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fa fa-book-open"></i> Units</a
            >
            <div class="sub-links">
              <a id="addUnits" class="nav-link sub-link" href="#"
                >Register Units</a
              >
              <a id="viewUnits" class="nav-link sub-link" href="#"
                >Veiw all units</a
              >
               <a id="fetchRegisteredUnits" class="nav-link sub-link" href="#"
                >Fetch Registered Units</a
              >
            </div>
          </div>
        </div>

         <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-chalkboard"></i> Courses</a
            >
            <div class="sub-links">
              <a id="addCourse" class="nav-link sub-link" href="#"
                >Register New Course</a
              >
              <a id="viewCourses" class="nav-link sub-link" href="#"
                >Veiw all Courses</a
              >
               <a id="fetchActiveCourses" class="nav-link sub-link" href="#"
                >Fetch Active Courses</a
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-calculator"></i> Accounts</a
            >
            <div class="sub-links">
              <a id="feeRates" class="nav-link sub-link" href="#"
                >Enter Fee Rates</a
              >
              <a id="feePayment" class="nav-link sub-link" href="#"
                >Log Fee Payment</a
              >
              <a id="viewRates" class="nav-link sub-link" href="#"
                >View Fee Rates</a
              >
              <a id="statement" class="nav-link sub-link" href="#"
                >Fee Statement</a
              >
              <a id="logs" class="nav-link sub-link" href="#"
                >Accounts Logs</a
              >
              <a id="session" class="nav-link sub-link" href="#"
                >Student sessions</a
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-users-cog"></i> Users</a
            >
            <div class="sub-links">
              <a id="registerUser" class="nav-link sub-link" href="#"
                >Register User</a
              >
              <a id="viewUser" class="nav-link sub-link" href="#">View Users</a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id="noticeboard" class="nav-link main-link" href="#"
              ><i class="fas fa-thumbtack"></i> Noticeboard</a
            >
             <div class="sub-links">
              <a id="addNotice" class="nav-link sub-link" href="#"
                >Add Notice</a
              >
              <a id="viewNotice" class="nav-link sub-link" href="#">View Notices</a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id="sendMessage" class="nav-link main-link" href="#"
              ><i class="fas fa-envelope"></i> Send Notifications</a
            >
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a class="nav-link main-link" href="#"
              ><i class="fas fa-user-tie"></i> Staff</a
            >
            <div class="sub-links">
              <a id="registerStaff" class="nav-link sub-link" href="#"
                >Register Staff</a
              >
              <a id="viewStaff" class="nav-link sub-link" href="#"
                >View Staff</a
              >
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id ="inbox" class="nav-link main-link" href="#"
              ><i class="fas fa-inbox"></i> Inbox</a>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <a id="notifications" class="nav-link main-link" href="#"
              ><i class="fas fa-bell"></i> Notifications</a>
          </div>
        </div>

        <div class="row" style="margin-bottom: 20px">
          <div class="col-12">
            <a id="logout-link" class="nav-link" href="#"
              ><i class="fas fa-sign-out-alt"></i> Log Out</a
            >
          </div>
        </div>
      </nav>
    </div>
    <div class="content">
      <nav class="navbar navbar-expand-md">
        <button id="sidebarToggleSmall" class="btn btn-light d-md-none">
          <i class="fas fa-bars"></i>
        </button>
        <button
          id="sidebarToggle"
          class="btn btn-primary mr-3 d-none d-md-block"
        >
          <i class="fas fa-bars"></i>
        </button>
        <div class="ml-auto">
          <span class="navbar-text">
            <i style="margin-right: 10px; color:#1e90ff;" class="fas fa-user "></i>
            <?php echo htmlspecialchars($staff_name); ?>
          </span>
        </div>
      </nav>

      <div id="ElementsContainer" class="ElementsContainer">
        <div id="mainpage"></div>
      </div>
      <div class="back-to-top">
      <a href="#" class="btn  btn-danger" onclick="window.scrollTo(0, 0); return false;">
        <i class="fas fa-arrow-up fa-2x"></i>
    </a>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
                // Toggle sub-links visibility
            document
            .querySelectorAll(".nav-link.main-link")
            .forEach(function (link) {
                link.addEventListener("click", function (e) {
                e.preventDefault();
                const subLinks = this.nextElementSibling;
                if (subLinks) {
                    if (subLinks.style.display === "block") {
                    subLinks.style.display = "none";
                    } else {
                    document
                        .querySelectorAll(".sub-links")
                        .forEach(function (subLink) {
                        subLink.style.display = "none";
                        });
                    subLinks.style.display = "block";

                    //timeout to close the sub-links after 25 seconds
                    setTimeout(function () {
                        subLinks.style.display = "none";
                    }, 25000);
                    }
                }
                });
            });

             const sidebar = document.querySelector(".sidebar");
    const content = document.querySelector(".content");
    
    function handleResize() {
      if (window.innerWidth < 768) {
        sidebar.classList.add("collapsed");
        content.classList.remove("expanded");
        content.classList.add("collapsed");
      } else {
        sidebar.classList.add("collapsed");
        content.classList.remove("expanded");
        content.classList.add("collapsed");
      }
    }
    
    // Initial check on page load
    handleResize();

    // Adjust when window is resized
    window.addEventListener("resize", handleResize);

    // Toggle sidebar visibility on button click (for smaller screens)
    document.getElementById("sidebarToggleSmall").addEventListener("click", function () {
      sidebar.classList.toggle("expanded");
      content.classList.toggle("expanded");
    });

    // Function to toggle sidebar and content layout
        function toggleLayout() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        }

        // Attaching the toggleLayout function to multiple elements
        const elementsToToggle = ['AllStudents', 'logAttendance', 'gradesLink', 'homeLink'];

        elementsToToggle.forEach(function(id) {
            document.getElementById(id).addEventListener('click', function(event) {
                toggleLayout();
            });
        });



            // Function to close sidebar when a sidebar link is clicked on small screens
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(event) {
                if (window.innerWidth <= 768) {
                    document.querySelector('.sidebar').classList.remove('expanded');
                    document.querySelector('.content').classList.remove('expanded');
                    document.querySelector('.navbar-nav').classList.remove('d-none');
                }
            });
        });
        // Toggle sidebar on small screens
        document.getElementById('sidebarToggleSmall').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('collapsed');
        });

        // Toggle sidebar on medium and larger screens
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.content').classList.toggle('expanded');
        });

        // Close sidebar when clicking outside on small screens
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!event.target.closest('.sidebar') && !event.target.closest('#sidebarToggleSmall')) {
                    document.querySelector('.sidebar').classList.remove('expanded');
                    document.querySelector('.content').classList.remove('expanded');
                    document.querySelector('.navbar-nav').classList.remove('d-none');
                }
            }
        });

    });

    $(document).ready(function() {
    function loadContent(linkId, url) {
        $('#' + linkId).click(function(event) {
            event.preventDefault(); 
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    if ($('#ElementsContainer').children().length > 0) {
                        $('#ElementsContainer').empty(); 
                        // $('#ElementsContainer').html('');
                    }
                    $('#ElementsContainer').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching content:', error);
                }
            });
        });
    }


    loadContent('gradesLink', 'grades.php');
    loadContent('registerStudent', 'RegisterStudent.php');
    loadContent('AllStudents', 'AllStudents.php');
    loadContent('UpdateStudent', 'StudentUpdate.php');
    loadContent('deleteStudent', 'DeleteStudent.php');
    loadContent('logAttendance', 'RecordAttendances.php');
    loadContent('viewAttendance', 'AttendanceForm.php');
    loadContent('groupAttendance', 'AttendanceViewForm.php');
    loadContent('detailedAttendance', 'DetailedAttendanceForm.php');
    loadContent('viewUser', 'DeleteAdmin.php');
    loadContent('viewNotice', 'AdminNotices.php');
    loadContent('addNotice', 'NoticeBoard.php');
    loadContent('registerStaff', 'StaffRegistrationForm.php');
    loadContent('viewStaff', 'view_staff.php');
    loadContent('sendMessage', 'SendNotification.php');
    loadContent('inbox', 'Inbox.php');
    loadContent('feeRates', 'FeeStatement.php');
    loadContent('addCourse', 'RegisterCourse.php');
    loadContent('viewCourses', 'FetchAllCourses.php');
    loadContent('fetchActiveCourses', 'ActiveCourses.php');
    loadContent('feePayment', 'FeePayment.php');
    loadContent('statement', 'fetch_fee_statement.php');
    loadContent('viewRates', 'FetchAllFeeStatements.php');
    loadContent('records', 'StudentFeeRecords.php');
    loadContent('addUnits', 'Course_Units.php');
    loadContent('fetchRegisteredUnits', 'RegisteredUnits.php');
    loadContent('viewUnits', 'FetchUnits.php');
    loadContent('homeLink', 'MainDisplay.php');
    loadContent('profile', 'AdminProfile.php');
    loadContent('notifications', 'FetchNotifications.php');
    loadContent('registerUser', 'RegisterAdminForm.php');
    loadContent('logs', 'AccountReport.php');
    loadContent('session', 'SessionInfo.php');
});


    $(document).ready(function() {
        function loadContent(url, targetElement) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    $(targetElement).html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching content:', error);
                }
            });
        }

        loadContent('MainDisplay.php', '#mainpage');
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
            window.location.href = 'StaffLoginForm.php';
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
