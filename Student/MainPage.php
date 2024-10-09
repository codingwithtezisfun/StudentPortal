<?php
require 'session_start.php';
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
            max-height: 150px; /* Limit the maximum height */
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
        #footerRow{
            margin-top:100px;
        }
    </style>
</head>
<body>

<div class="container-fluid no-padding icons mt-3">
    <div class="row" id="iconRow"></div>
    <div class="row" id="profileRow"></div>
    <div class="row" id="sessionRow"></div>
    <div class="row" id="messageRow"></div>
    <!-- <div class="row" id="unitsRow"></div> -->
    <div class="row" id="footerRow"></div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function () {
        loadIcons();
        loadProfile();
        loadSession();
        loadUnitRegistration();
        loadSession();
        loadMessanger();
        loadFooter();
       
    });

    function loadIcons() {
        $.ajax({
            url: 'load_icons.php',
            type: 'GET',
            success: function (response) {
                $('#iconRow').html(response);
            },
            error: function () {
                alert('Error loading icons. Please try again.');
            }
        });
    }

    function loadProfile() {
        $.ajax({
            url: 'MyProfile.php',
            type: 'GET',
            success: function (response) {
                $('#profileRow').html(response);
            },
            error: function () {
                alert('Error loading profile. Please try again.');
            }
        });
    }

    function loadSession() {
        $.ajax({
            url: 'ReportSession.php',
            type: 'GET',
            success: function (response) {
                $('#sessionRow').html(response);
            },
            error: function () {
                alert('Error loading session. Please try again.');
            }
        });
    }

    function loadUnitRegistration() {
        $.ajax({
            url: 'RegisterUnits.php',
            type: 'GET',
            success: function (response) {
                $('#unitsRow').html(response);
            },
            error: function () {
                alert('Error loading units form. Please try again.');
            }
        });
    }


    function loadFooter() {
        $.ajax({
            url: 'Footer.php', 
            type: 'GET',
            success: function (response) {
                $('#footerRow').html(response);
            },
            error: function () {
                alert('Error loading footer. Please try again.');
            }
        });
    }
    function loadMessanger() {
        $.ajax({
            url: 'ContactStaff.php', 
            type: 'GET',
            success: function (response) {
                $('#messageRow').html(response);
            },
            error: function () {
                alert('Error loading footer. Please try again.');
            }
        });
    }
</script>
</body>
</html>
