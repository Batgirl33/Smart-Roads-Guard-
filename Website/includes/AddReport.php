<?php 
    session_start(); // Start the session to use session variables
    include '../conect.php'; // Include the database connection file

    if($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted via POST

        $Erros = array(); // Initialize an array to store errors

        $id_alert = $_POST['id_alert']; // Get the alert ID from POST data
        $user_id = $_POST['user_id']; // Get the user ID from POST data

        $Status = $_POST["Status"]; // Get the status from POST data
        $Note = $_POST["Note"]; // Get the note from POST data

        $date = date("Y-m-d H:i:s"); // Get the current date and time

        // Insert the report details into the 'report' table
        $sql = "INSERT INTO report (`status`, report_submission_datetime, userid, alert_id) VALUE (?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssii", $Status, $date, $user_id, $id_alert);
        $stmt->execute(); // Execute the query

        $report_id = $stmt->insert_id; // Get the ID of the newly inserted report

        // Insert the note details into the 'noted_report' table
        $sqll = "INSERT INTO noted_report (noted, id_report, id_user) VALUES (?,?,?)";
        $stmtt = $conn->prepare($sqll);
        $stmtt->bind_param("sii", $Note, $report_id, $user_id);
        $stmtt->execute(); // Execute the query

        // Display success message
        echo "<div class='alert alert-success text-center' > Successful Add Report </div>";

        ?>
        <script>
            // Redirect to the 'reports.php' page after 2 seconds
            setTimeout(function(){
                location.href='reports.php';
            }, 2000);
        </script>
        <?php

    }

?>
