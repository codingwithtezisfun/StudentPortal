<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Notice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">

    <style>
        
        .card {
            max-width: 800px;
            margin: auto;
        }
        .sml{
            color:red;
        }
        .card-header{
            background-color: #343a40;
            text-align:center;
        }
        .button{
            width: 100%;
        }
        .card-body{
            color:blue;
            font-weight:bold;
        }

    </style>
</head>
<body>

<div class="card">
    <div class="card-header text-white">
        <h5 class="card-title h5 mb-0"><i class="fas fa-bell"></i>  Add Notice</h5>
    </div>
    <div class="card-body">
        <form id="addNoticeForm" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" class="form-control" required placeholder="eg Timetable">
            </div>
            <div class="form-group">
                <label for="file">File:</label>
                <input type="file" id="file" name="file" class="form-control-file" required>
                <small class="sml">**Upload your files mainly as pdf if not possible upload as doc,docx or txt</small><br>
            </div>
            <div class="form-group">
                <label for="targetAudience">Target Audience:</label>
                <select id="targetAudience" name="targetAudience" class="form-control" required>
                    <option value="">Select Recipient</option>
                    <option value="all">All</option>
                    <option value="students">Students</option>
                    <option value="staff">Staff</option>
                    
                </select>
            </div>
             
            <button type="submit" class="btn button btn-primary">Add Notice</button><br>
             <small class="sml">**NoticeBoard carries 16 notices max</small>
           
        </form>
    </div>
</div>

<div class="card mt-1">
    <div class="card-header  text-white">
        <h5 class="card-title h5 mb-0"><i class='bi bi-trash'></i>Delete Notice</h5>
    </div>
    <div class="card-body">
        <form id="deleteNoticeForm">
            <div class="form-group">
                <label for="noticeToDelete">Select Notice to Delete:</label>
                <select id="noticeToDelete" name="noticeToDelete" class="form-control">
                                    <?php
                   include 'db_connection.php';

                    $sql = "SELECT id, title, filePath FROM noticeboard";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                       echo '<option value="">Select Notice</option>'; 
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["id"] . '">' . $row["title"] . '</option>';
                        }
                    }

                    $conn->close();
                    ?>

                </select>
            </div>
            <button type="button" id="deleteNoticeBtn" class="btn button btn-danger">Delete Notice</button>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function () {
        // Form submission for adding a notice
        $('#addNoticeForm').submit(function (event) {
            event.preventDefault(); 
            var formData = new FormData(this);

            $.ajax({
                url: 'submitNotice.php', 
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    // Show SweetAlert success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                        confirmButtonText: 'OK'
                    });

                    // Reset the form
                    $('#addNoticeForm')[0].reset();
                },
                error: function () {
                    // Show SweetAlert error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Error adding notice. Please try again.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Click handler for deleting a notice
        $('#deleteNoticeBtn').click(function () {
            var noticeId = $('#noticeToDelete').val();

            if (noticeId === "") {
                // Show SweetAlert warning if no notice is selected
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Please select a notice to delete.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Confirmation prompt before deleting
            Swal.fire({
                title: 'Are you sure?',
                text: 'This action cannot be undone!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_notice.php',
                        type: 'POST',
                        data: { id: noticeId },
                        success: function (response) {
                            // Show SweetAlert success message after deletion
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response,
                                confirmButtonText: 'OK'
                            });

                            // Reset the delete form
                            $('#deleteNoticeForm')[0].reset();
                        },
                        error: function () {
                            // Show SweetAlert error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Error deleting notice. Please try again.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });
    });
</script>


</body>
</html>
