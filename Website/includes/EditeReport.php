<?php 
    session_start(); // Start the session to access session variables

    include '../conect.php'; // Include the database connection file

    if($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted via POST

        $Erros = array(); // Initialize an array to store errors

        $id_alert = $_POST['id_alert']; // Get the alert ID from POST data
        $id_report = $_POST["id_report"]; // Get the report ID from POST data
        $type_user = $_POST['type_user']; // Get the type of user (service or other) from POST data

        $user_id = $_POST['user_id']; // Get the user ID from POST data
        $notes = $_POST["notes"]; // Get the notes from POST data

        if($type_user == 'service'){  // Check if the user is of type 'service'

            $status = $_POST["status"]; // Get the status from POST data
            if(!empty($status)) { // Check if status is not empty

                // Update the status of the report in the 'report' table
                $sql = "UPDATE report set `status` = ? WHERE report_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $status, $id_report);
                $stmt->execute(); // Execute the update query
            }

        }

        if(!empty($notes)) { // Check if notes are not empty

            // Check if there is already a note for this report and user
            $sql = "SELECT * FROM noted_report WHERE id_report = ? AND id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $id_report, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) { // If a note exists, update it
                // Update the existing note in the 'noted_report' table
                $sql = "UPDATE noted_report set `noted` = ? WHERE id_report = ? AND id_user = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $notes, $id_report, $user_id);
                $stmt->execute(); // Execute the update query

            } else { // If no note exists, insert a new one
                // Insert a new note into the 'noted_report' table
                $sql = "INSERT INTO noted_report (noted, id_report, id_user) VALUE (?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sii", $notes, $id_report, $user_id);
                $stmt->execute(); // Execute the insert query

            }

        }

    }

?>
