<?php 
    session_start(); // Start the session to use session variables
    include '../conect.php'; // Include the database connection file

    // Get the classification from POST or set to 'other' if not set
    $classification = isset($_POST['class_name']) ? $_POST['class_name'] : 'other';

    // Define the valid classifications
    $all = ['landslide','accident_class0','accident_class1','fallen_tree','buffle','guepard','hippopotame','lion','loup','rhinoceros','tigre','other'];

    // If the classification is not in the valid list, set it to 'other'
    if(!in_array($classification, $all)){
        $classification = 'other';
    }

    // Get all camera ids from the 'camera' table
    $sql = "SELECT camera_id FROM camera";
    $result = $conn->query($sql);

    $camera_numbers = []; // Initialize an empty array to store camera IDs

    // If cameras exist, fetch their IDs
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $camera_numbers[] = $row['camera_id'];
        }

        // Randomly pick a camera ID
        $random_key = array_rand($camera_numbers);
        $camera_id = $camera_numbers[$random_key];
    }

    // Get the image name and timestamp from POST data
    $image_name = $_POST['image_name'];
    $detection_time = $_POST['timestamp'];

    // Insert the obstacle details into the 'obstacle' table
    $sql = "INSERT INTO obstacle (classification , obstacle_picture , camera_id , detection_time) VALUE (?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssis" , $classification , $image_name , $camera_id , $detection_time);
    $stmt->execute();

    // Prepare and execute a query to fetch the obstacle details based on detection time
    $statement = $conn->prepare("SELECT * FROM obstacle WHERE detection_time = ?");
    $statement->bind_param('s', $detection_time);
    $statement->execute();
    $result =  $statement->get_result();
    $rows = $result->fetch_assoc();

    // Get the obstacle ID from the fetched data
    $id_obsracle = $rows['obstacle_id'];

    // Prepare and execute a query to get the flashlight details based on camera ID
    $st = $conn->prepare("SELECT * FROM flashlight WHERE camera_id = ?");
    $st->bind_param('i', $camera_id);
    $st->execute();
    $re =  $st->get_result();
    $rows = $re->fetch_assoc();

    // Get the flashlight ID
    $flashlight_id = $rows['flashlight_id'];

    // Insert the alert details into the 'alert' table, linking the flashlight and obstacle
    $sql = "INSERT INTO alert (flashlight_id , obstacle_id ) VALUE (?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $flashlight_id, $id_obsracle);
    $stmt->execute();

    // Set flashlight as active (1)
    $active = '1';
    $sql = "UPDATE flashlight set active = ? WHERE flashlight_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $active, $flashlight_id);
    $stmt->execute();

    // Show success message
    echo "<div class='alert alert-success text-center' > Successful Add Obstacle </div>";

    ?>
    <script>
        // Redirect to 'add_obstacle.php' page after 2 seconds
        setTimeout(function(){
            location.href='add_obstacle.php';
        }, 2000);
    </script>
<?php
?>
