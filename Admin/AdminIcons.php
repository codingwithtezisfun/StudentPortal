<!-- This page is used to display the jumbotrons on the home page that hold information such as 
   *Total students
   *Active students
   *total units etc...

   The page requires load_icon_content.php which holds the jumbotrons loadContent
   This page is linked to main page.php where its content is loaded dynamically for display
   *uses an ajax script to dynamically load content -->
   <?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Jumbotrons in Grid Layout</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .container-fluid.icons .jumbotron {
            background-color: #e9ecef;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 0.5rem;
            cursor: pointer;
            max-height: 150px; 
            overflow: hidden;
            margin-top:10px;
        }
        .container-fluid.icons .icon-top,
        .container-fluid.icons .icon-bottom {
            text-align: center;
        }
        .container-fluid.icons .icon-bottom {
            margin-top: 1px;
        }
        .container-fluid.icons .details-link {
            text-align: center;
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }
        .container-fluid.icons {
            min-height: 200px !important;
        }
    </style>
</head>
<body>
<div class="container-fluid icons">
    <div class="row" id="contentRow">
        <!-- Dynamic content is loaded here -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function () {
        loadContent(); 

        function loadContent() {
            $.ajax({
                url: 'load_icon_content.php',
                type: 'GET',
                success: function (response) {
                    $('#contentRow').html(response); 
                },
                error: function () {
                    alert('Error loading content. Please try again.');
                }
            });
        }
        setInterval(loadContent, 60000);
    });
</script>
</body>
</html>
