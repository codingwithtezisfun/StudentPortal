<?php
// Check if form is submitted with POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    include 'db_connection.php';
    // Prepare data for insertion
    $title = $_POST['title'];
    $targetAudience = $_POST['targetAudience'];

    // Handle file upload if a file is selected
    if (!empty($_FILES['file']['name'])) {
        $target_dir = "../noticeboardFiles/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is a valid type
        $allowedTypes = array('pdf', 'doc', 'docx', 'txt'); // Add more file types as needed
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Sorry, only PDF, DOC, DOCX, TXT files are allowed.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["file"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Upload file if no errors
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // Insert notice details into database
                $insert_sql = "INSERT INTO noticeboard (title, targetAudience, filePath) VALUES ('$title', '$targetAudience', '$target_file')";
                if ($conn->query($insert_sql) === TRUE) {
                    echo "Notice added successfully.";
                } else {
                    echo "Error: " . $insert_sql . "<br>" . $conn->error;
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Insert notice details into database without file
        $insert_sql = "INSERT INTO noticeboard (title, targetAudience) VALUES ('$title', '$targetAudience')";
        if ($conn->query($insert_sql) === TRUE) {
            echo "Notice added successfully.";
        } else {
            echo "Error: " . $insert_sql . "<br>" . $conn->error;
        }
    }

    // Check if noticeboard count is greater than 16
    $count_sql = "SELECT COUNT(*) AS total FROM noticeboard";
    $result = $conn->query($count_sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalNotices = $row['total'];
        if ($totalNotices > 16) {
            echo "Noticeboard is full(16 notices max). Please delete some notices.";
        }
    }

    // Close connection
    $conn->close();
}
?>
