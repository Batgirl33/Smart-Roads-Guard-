<?php 
    session_start(); // Start the session to use session variables

    include '../conect.php'; // Include the database connection file

    // Check if the form is submitted via POST
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        $Erros = array(); // Initialize an array to store validation errors

        // Get and trim user input
        $id = trim($_POST["id"]);
        $password_temp = trim($_POST["password_temp"]);
        $password1 = trim($_POST["password1"]);
        $password2 = trim($_POST["password2"]);

        // Validate required fields
        if (empty($id)) { $Erros[] = " Enter Number"; }
        if (empty($password_temp)) { $Erros[] = "Enter Your Password"; }
        if (empty($password1)) { $Erros[] = "Enter New Password"; }
        if (empty($password2)) { $Erros[] = "Enter the confirmation of your new Password"; }

        // Check if the new password and confirmation password match
        if( $password1 != $password2 ){$Erros[] = "The Password do not match"; }

        // If id and password are not empty, verify the user's current password
        if(!empty($id) && !empty($password_temp)){
            // Prepare and execute the query to check if the user exists
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if exactly one user was found
            if ($result->num_rows == 1)
            {
                $user = $result->fetch_assoc(); // Fetch the user data

                // Verify the entered password with the stored password
                if (password_verify($password_temp, $user['password'])) {

                    // Check if the user account is already active
                    if($user['account_status'] == 'Active'){
                        $Erros[] = "You are currently active"; // Add error if user is already active
                    }
                } else {
                    // If the password does not match, add error
                    $Erros[] = " Error Of Password ";
                }

            } else {
                // If no user is found, add error
                $Erros[] = " Not found";
            }
        }

        // If no errors, proceed to update the password
        if(empty($Erros)){

            // Hash the new password
            $p = password_hash($password1, PASSWORD_DEFAULT);
            // Generate a random verification code
            $vere = rand(10000000, 99999999);

            // Prepare and execute the update query to update the password and verification code
            $sql = "UPDATE users set `password` = ? , verifi_code =? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sii",  $p, $vere, $id);
            $stmt->execute();

            // Store the active page id in session
            $_SESSION['ActivePage'] = $id;

            // Display success message
            echo "<div class='alert alert-success text-center' > Password Changed Successfully And <br> to verify your account please enter the verification code send to your email</div>";

            ?>
            <script>
                // Redirect to the verification code page after 2 seconds
                setTimeout(function(){
                    location.href='vereficition_code.php';
                },2000);
            </script>
            <?php

        } else {
            // Loop through and display all validation errors
            foreach($Erros as  $error){
                echo "<div class='alert alert-danger text-center' > " . $error . "</div>";
            }
        }
    }
?>
