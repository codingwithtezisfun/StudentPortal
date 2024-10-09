<?php
require 'session_start.php';
require 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Units</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #343a40;
        }
        .form-border {
            border: 1px solid #343a40;
            padding: 20px;
            border-radius: 5px;
        }
        .text-center .btn{
            width:100%;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="font-family: Arial, sans-serif; font-size: 24px; color: #000000;">
                    <i class="fas fa-book-open" style="color: #000000; font-size: 20px; margin-right: 10px;"></i>
                    Manage Units
                </h2>
            </div>
            <div class="card-body form-border">
                <form id="manageUnitsForm">
                    <div class="form-row mb-3">
                        <div class="col-md-12 position-relative">
                            <input type="text" class="form-control" id="searchUnits" placeholder="Filter units in the dropdown here">
                            <i class="fas fa-search position-absolute" style="top: 50%; right: 10px; transform: translateY(-50%);"></i>
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-8">
                            <label for="unitNameSelect">Select Unit to Update</label>
                            <select class="form-control" id="unitNameSelect" name="unitNameSelect">
                                <!-- Options will be populated from the server -->
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="newUnitName">New Unit Name</label>
                            <input type="text" class="form-control" id="newUnitName" name="newUnitName" placeholder="Enter new unit name">
                        </div>
                    </div>
                    <div class="form-row mb-3">
                        <div class="col-md-6 text-center">
                            <button type="submit" name="updateUnit" class="btn btn-primary">Update Unit</button>
                        </div>
                        <div class="col-md-6 text-center">
                            <button type="button" id="deleteUnitBtn" class="btn btn-danger">Delete Unit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Load units into the dropdown
            function loadUnits() {
                $.ajax({
                    url: 'manage_units.php',
                    type: 'POST',
                    data: { action: 'loadUnits' },
                    success: function(response) {
                        $('#unitNameSelect').html(response);
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Error', 'Unable to load units. Please try again later.', 'error');
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

            // Handle form submission for updating units
            $('#manageUnitsForm').on('submit', function(e) {
                e.preventDefault();
                var unitId = $('#unitNameSelect').val();
                var newUnitName = $('#newUnitName').val();

                if (unitId && newUnitName) {
                    $.ajax({
                        url: 'manage_units.php',
                        type: 'POST',
                        data: { action: 'updateUnit', unitId: unitId, newUnitName: newUnitName },
                        success: function(response) {
                            Swal.fire('Success', 'Unit updated successfully!', 'success');
                            loadUnits(); // Reload the units
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Error', 'Unable to update unit. Please try again later.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Error', 'Please select a unit and enter a new name.', 'error');
                }
            });

            // Handle delete button click
            $('#deleteUnitBtn').on('click', function() {
                var unitId = $('#unitNameSelect').val();

                if (unitId) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'No, cancel!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'manage_units.php',
                                type: 'POST',
                                data: { action: 'deleteUnit', unitId: unitId },
                                success: function(response) {
                                    if (response === 'success') {
                                        Swal.fire('Deleted!', 'Unit has been deleted.', 'success');
                                        loadUnits(); // Reload the units
                                    } else {
                                        Swal.fire('Error', 'Unit cannot be deleted because it is being used.', 'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire('Error', 'Unable to delete unit. Please try again later.', 'error');
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire('Error', 'Please select a unit to delete.', 'error');
                }
            });
        });
    </script>
</body>
</html>
