<?php 
    session_start(); // Start the session to access session variables

    include '../conect.php'; // Include the database connection file

    if($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted via POST

        $Erros = array(); // Initialize an array to store errors

        // Get the email, password, and login type from the POST data
        $email = $_POST['email'];
        $password = $_POST['password'];
        $type_login = $_POST['type_login'];

        // Validate if email and password are provided
        if (empty($email)) { $Erros[] = "Enter Email"; }
        if (empty($password)) { $Erros[] = "Enter Password"; }

        // Check if email and password are not empty
        if (!empty($email) && !empty($password)) {

            // Query to check if the email and type_user match
            $sql = "SELECT * FROM users WHERE email = ? AND type_user = ?";
            $stmt = $conn->prepare($sql); // Prepare the SQL statement
            $stmt->bind_param("ss", $email, $type_login); // Bind email and type_user parameters
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result(); // Get the result of the query

            if ($result->num_rows > 0) { // If a user is found
                $user = $result->fetch_assoc(); // Fetch the user's details

                // Verify the provided password against the stored password hash
                if (password_verify($password, $user['password'])) {
                    // Check if the user's account is active
                    if ($user['account_status'] != 'Active') {
                        $Erros[] = "Your Account Is Not Active"; // Add error if account is not active
                    }
                } else {
                    $Erros[] = "Password Is Incorrect "; // Add error if password is incorrect
                }

            } else {
                $Erros[] = "Email Or Password Error "; // Add error if no matching email is found
            }

        }

        // If no errors, proceed with logging in the user
        if (empty($Erros)) {

            // Query to fetch user data based on email
            $sql = "SELECT * FROM users WHERE email = ?";
            $stmt = $conn->prepare($sql); // Prepare the SQL statement
            $stmt->bind_param("s", $email); // Bind the email parameter
            $stmt->execute(); // Execute the query
            $result = $stmt->get_result(); // Get the result of the query
            $user = $result->fetch_assoc(); // Fetch the user's details

            // Store the user's ID in the session
            $_SESSION['UserId'] = $user['user_id'];

            // Display success message and redirect to homepage
            echo "<div class='alert alert-success text-center' > Login Successful </div>";

            ?>
            <script>
                // Redirect to homepage after 2 seconds
                setTimeout(function(){
                    location.href='homepage.php';
                }, 2000);
            </script>
            <?php

        } else {
            // Display each error in an alert box if there are errors
            foreach($Erros as $error) {
                echo "<div class='alert alert-danger text-center' > " . $error . "</div>";
            }

        }

    }

?>
