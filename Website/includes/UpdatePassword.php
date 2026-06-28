<?php 
    session_start(); // Start the session to use session variables

    include '../conect.php'; // Include the database connection file

    // Check if the form is submitted via POST
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        $Erros = array(); // Initialize an array to store validation errors

        // Get and trim user input
        $id_user = trim($_POST["id_user"]);
        $password1 = trim($_POST["password1"]);
        $password2 = trim($_POST["password2"]);

        // Validate required fields
        if (empty($id_user)) { $Erros[] = "Error, Please Try Again"; }
        if (empty($password1)) { $Erros[] = "Enter Your New Password"; }
        if (empty($password2)) { $Erros[] = "Enter Your Confirm Password"; }

        // Check if passwords match
        if( $password1 != $password2 ){$Erros[] = "The Password do not match"; }

        // Validate password strength using regex pattern
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($pattern, $password1)) {
            $Erros[] = "Must be at least 8 characters";
            $Erros[] = "Must contain at least one uppercase letter";
            $Erros[] = "Must contain at least one lowercase letter";
            $Erros[] = "Must contain at least one number";
            $Erros[] = 'Must contain at least one special character (!@#$%^&*)';
        }

        // If no errors, proceed with updating the password
        if(empty($Erros)){
            // Hash the new password
            $p = password_hash($password1, PASSWORD_DEFAULT);

            // Prepare and execute the update query
            $sql = "UPDATE users set `password` = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si",  $p, $id_user);
            $stmt->execute();

            // Display success message
            echo "<div class='alert alert-success text-center' > Password Changed Successfully</div>";

            // Clear the session variable used for password reset
            unset($_SESSION['UserId_Password']);

            // Redirect to login page after 2 seconds
            ?>
            <script>
                setTimeout(function(){
                    location.href='login.php';
                },2000);
            </script>
            <?php

        } else {
            // Loop through and display all validation errors
            foreach($Erros as $error){
                echo "<div class='alert alert-danger text-center' > " . $error . "</div>";
            }
        }
    }
?>
