<?php 
    session_start(); // Start the session to use session variables
    include '../conect.php'; // Include the database connection file

    date_default_timezone_set('Asia/Riyadh'); // Set the timezone for the application

    $imgFolder = '../img/'; // Define the folder where images will be stored

    // Check if the image has been uploaded successfully
    if (isset($_FILES['detected_image']) && $_FILES['detected_image']['error'] == 0) {
        $uploadedFile = $_FILES['detected_image']; // Get the uploaded file details

        $filename = $uploadedFile['name']; // Get the original file name
        $tempName = $uploadedFile['tmp_name']; // Get the temporary name of the uploaded file
        $uploadPath = $imgFolder . basename($filename); // Define the full upload path

        // Try to move the uploaded file to the specified path
        if (move_uploaded_file($tempName, $uploadPath)) {

            // Get the classification label from POST data or set it to 'Others' if not provided
            $classification = isset($_POST['label']) ? $_POST['label'] : 'Others';

            // Define valid classification types
            $all = ['Fallen Tree', 'Rockslide', 'Accidents', 'Animals', 'Car Parts', 'Others'];

            // If the classification is not in the valid list, set it to 'Others'
            if (!in_array($classification, $all)) {
                $classification = 'Others';
            }

            // Set a default camera ID and get the current timestamp for detection time
            $camera_id = 1;
            $detection_time = date("Y-m-d H:i:s");

            // Insert the obstacle details into the 'obstacle' table
            $sql = "INSERT INTO obstacle (classification, obstacle_picture, camera_id, detection_time) VALUE (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssis", $classification, $filename, $camera_id, $detection_time);
            $stmt->execute();

            // Prepare and execute a query to fetch the obstacle based on the detection time
            $statement = $conn->prepare("SELECT * FROM obstacle WHERE detection_time = ?");
            $statement->bind_param('s', $detection_time);
            $statement->execute();
            $result = $statement->get_result();
            $rows = $result->fetch_assoc();

            // Get the obstacle ID from the result
            $id_obsracle = $rows['obstacle_id'];

            // Prepare and execute a query to fetch the flashlight details based on the camera ID
            $st = $conn->prepare("SELECT * FROM flashlight WHERE camera_id = ?");
            $st->bind_param('i', $camera_id);
            $st->execute();
            $re = $st->get_result();
            $rows = $re->fetch_assoc();

            // Get the flashlight ID from the result
            $flashlight_id = $rows['flashlight_id'];

            // Insert an alert linking the flashlight to the obstacle
            $sql = "INSERT INTO alert (flashlight_id, obstacle_id) VALUE (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $flashlight_id, $id_obsracle);
            $stmt->execute();

            // Set the flashlight as active (1)
            $active = '1';
            $sql = "UPDATE flashlight SET active = ? WHERE flashlight_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $active, $flashlight_id);
            $stmt->execute();

            // Display success message
            echo "<div class='alert alert-success text-center' > Successful Add Obstacle </div>";

            ?>
            <script>
                // Redirect to the 'add_obstacle.php' page after 2 seconds
                setTimeout(function(){
                    location.href='add_obstacle.php';
                }, 2000);
            </script>
        <?php
        } else {
            // If the image upload fails, show error message
            echo "Failed to upload image.";
        }
    } else {
        // If no image was uploaded, show error message
        echo "Image not uploaded.";
    }
?>
