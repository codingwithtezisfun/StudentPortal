
<?php
session_start();
if (!isset($_SESSION['email'])) {

    echo "
    <script type='text/javascript'>
        // Close the current page
        window.open('', '_self', ''); // Allow window to be closed
        window.close(); // Close the current tab or window
        
        // Fallback: Redirect to login form if window cannot be closed
        setTimeout(function() {
            window.location.href = 'loginForm.php';
        }, 1000); // Redirect after 1 second if the window isn't closed
    </script>";
    exit();
}
?>
