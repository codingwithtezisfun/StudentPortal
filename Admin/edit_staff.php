<?php

require 'session_start.php';
require 'db_connection.php';

if(isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT * FROM staff WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $email = $row['email'];
        $phone = $row['phone'];
        $role = $row['role'];
        $picture = $row['picture'];
    } else {
        echo "Staff not found";
        exit;
    }
} else {
    echo "ID parameter not specified";
    exit;
}

?>
<form class="border p-4" style="color:blue; font-weight:bold;"id="editStaffForm" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="name">Name</label>
        </div>
        <div class="col-md-8">
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="email">Email</label>
        </div>
        <div class="col-md-8">
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="phone">Phone Number</label>
        </div>
        <div class="col-md-8">
            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="role">Role</label>
        </div>
        <div class="col-md-8">
            <select class="form-control" id="role" name="role" required>
                <option value="lecturer" <?php if ($role == 'lecturer') echo 'selected'; ?>>Lecturer</option>
                <option value="registrar" <?php if ($role == 'registrar') echo 'selected'; ?>>Registrar</option>
                <option value="accounts" <?php if ($role == 'accounts') echo 'selected'; ?>>Accounts</option>
                <option value="quality_assurance" <?php if ($role == 'quality_assurance') echo 'selected'; ?>>Quality Assurance</option>
                <option value="principal" <?php if ($role == 'principal') echo 'selected'; ?>>Principal</option>
                <option value="security" <?php if ($role == 'security') echo 'selected'; ?>>Security</option>
                <option value="front_desk" <?php if ($role == 'front_desk') echo 'selected'; ?>>Front Desk</option>
                <option value="librarian" <?php if ($role == 'librarian') echo 'selected'; ?>>Librarian</option>
                <option value="helping_staff" <?php if ($role == 'helping_staff') echo 'selected'; ?>>Helping Staff</option>
                <option value="other" <?php if ($role == 'other') echo 'selected'; ?>>Other</option>
            </select>
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="image">Current Image</label>
        </div>
        <div class="col-md-8">
            <img src="../StaffImages/<?php echo $picture; ?>" class="img-thumbnail" width="100">
        </div>
    </div>
    <div class="form-row mb-3">
        <div class="col-md-4">
            <label for="new_image">New Image</label>
        </div>
        <div class="col-md-8">
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="new_image" name="image" accept="image/*">
                <label class="custom-file-label" for="new_image">Choose file</label>
            </div>
        </div>
    </div>
    <div class="form-row">
        <div class="col-md-4"></div>
        <div class="col-md-8">
            <button style="width:100%;" type="submit" class="btn btn-primary" name="submit">Update</button>
        </div>
    </div>
</form>

<script>

        // Script to update the labels of the custom file input
        $(".custom-file-input").on("change", function() {
        var fileName = $(this).val().split("\\").pop();
        $(this).siblings(".custom-file-label").addClass("selected").html(fileName);

        // Update hidden input field with the selected file name
        $(this).closest('.custom-file').find('.custom-file-hidden').val(fileName);
    });
    // Handle form submission via AJAX
    $("#editStaffForm").submit(function(event) {
        event.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            url: 'editStaff.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var data = JSON.parse(response);
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating the record.'
                });
            }
        });
    });
</script>
