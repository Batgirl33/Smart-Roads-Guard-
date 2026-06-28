<?php 
    session_start(); // Start the session to access session variables

    include '../conect.php'; // Include the database connection file

    if($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted via POST

        $Erros = array(); // Initialize an array to store errors
        $email = trim($_POST["email"]); // Get the email from POST data

        if (empty($email)) { // Check if email is empty
            $Erros[] = "Enter Email "; // Add error if email is empty
        }

        if (empty($Erros)) { // If no errors

            // Query to check if the email exists in the 'users' table
            $sql = "SELECT * FROM users WHERE email = ?";

            $stmt = $conn->prepare($sql); // Prepare the SQL statement
            $stmt->bind_param("s", $email); // Bind the email parameter
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result(); // Get the result of the query
            
            if ($result->num_rows == 1) { // If the email exists in the database
                $user = $result->fetch_assoc(); // Fetch the user's details

                // Generate a random 8-digit verification code
                $vere = rand(10000000, 99999999);

                // Update the user's verification code in the 'users' table
                $sql = "UPDATE users set verifi_code =? WHERE user_id = ?";
                $stmt = $conn->prepare($sql); // Prepare the update query
                $stmt->bind_param("ii", $vere, $user['user_id']); // Bind the verification code and user ID
                $stmt->execute(); // Execute the update query

                $_SESSION['UserId_Password'] = $user['user_id']; // Store the user's ID in the session

                // Display a success message and redirect the user
                echo "<div class='alert alert-success text-center' > The verification code has been sent to your registered email</div>";

                ?>
                <script>
                    // Redirect to verification page after 2 seconds
                    setTimeout(function(){
                        location.href='vereficition_code.php';
                    }, 2000);
                </script>
                <?php

            } else {
                // Add error if email is not found
                $Erros[] = "The Email Not Found";
            }

        }

        if(!empty($Erros)) { // If there are any errors

            // Display each error in an alert box
            foreach($Erros as $error) {
                echo "<div class='alert alert-danger text-center' > " . $error . "</div>";
            }

        }

    }

?>
