<?php
require 'session_start.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Display</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .card-header {
            background-color:  #36454f;
            color: white;
            font-size: 1.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            text-align:center;
            font-weight:bold;
        }
        .jumbotron {
            width: 100%;
            height: 100px;
            margin: 10px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            border: 1px solid #ccc;
            background-color: #ffe4b5;
        }
        .jumbotron .overlay {
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            padding: 10px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }
        .jumbotron img {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header"><i class="fas fa-calendar-check"></i> Noticeboard</div>
        <div class="card-body">
            <div class="row" id="fileList">
                <!-- Files will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to fetch files using AJAX
        function fetchFiles() {
            $.ajax({
                url: 'notice_display.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.error) {
                        $('#fileList').html('<div class="col-12">No notices found.</div>');
                    } else {
                        var html = '';
                        $.each(response, function(index, file) {
                            // Determine icon or preview based on file type
                            var fileType = file.filePath.split('.').pop().toLowerCase();
                            var fileIcon = (fileType === 'pdf' || fileType === 'doc' || fileType === 'docx') ? 'file-icon.jpg' : '../images/default.png';

                            html += '<div class="col-lg-4 col-md-6 col-sm-12">';
                            html += '<div class="jumbotron" onclick="openFile(\'' + file.filePath + '\')">';
                            html += '<div class="overlay">' + file.title + '</div>';
                            html += '<img src="' + fileIcon + '" alt="File Icon" style="width: 50px; height: 50px;">';
                            html += '</div>';
                            html += '</div>';
                        });
                        $('#fileList').html(html);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    $('#fileList').html('<div class="col-12">Failed to fetch notices. Please try again.</div>');
                }
            });
        }

        // Initial load of files
        fetchFiles();

        // Function to open file in new tab
        function openFile(filePath) {
            window.open(filePath, '_blank');
        }
    });
</script>

</body>
</html>
