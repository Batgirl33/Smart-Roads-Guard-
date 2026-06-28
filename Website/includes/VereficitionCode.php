<?php 
    session_start(); // Start the session to use session variables
    include '../conect.php'; // Include the database connection file

    // Check if the form is submitted via POST
    if($_SERVER['REQUEST_METHOD']=='POST')
    {
        $Erros = array(); // Initialize an array to store validation errors

        // Get and trim user input
        $id = trim($_POST["id_user"]);
        $code_id = trim($_POST["code_id"]);

        // Validate required fields
        if (empty($code_id)) { $Erros[] = " Enter verification code "; }
        if (empty($id)) { $Erros[] = "Not Found"; }

        // If no errors, proceed with checking the verification code
        if(empty($Erros)){

            // Prepare and execute the query to check if the user exists
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if exactly one user was found
            if ($result->num_rows == 1) {

                $user = $result->fetch_assoc(); // Fetch the user data

                // If the verification code matches
                if($user['verifi_code'] == $code_id){

                    // If session variable for password reset exists, show message and redirect to update password page
                    if(isset($_SESSION['UserId_Password'])){
                        echo "<div class='alert alert-success text-center' >The verification code has been successfully confirmed</div>";
                        ?>
                        <script>
                            setTimeout(function(){
                                location.href='update_password.php';
                            },2000);
                        </script>
                        <?php

                    } else {

                        // If no password reset session, activate the user account
                        $sql = "UPDATE users set `account_status` = ? WHERE user_id = ?";
                        $stmt = $conn->prepare($sql);
                        $active = 'Active';
                        $stmt->bind_param("si", $active, $id);
                        $stmt->execute();

                        // Show success message and redirect to login page
                        echo "<div class='alert alert-success text-center' >The verification code has been successfully confirmed And Active Your Account</div>";
                        ?>
                        <script>
                            setTimeout(function(){
                                location.href='login.php';
                            },3000);
                        </script>
                        <?php

                        unset($_SESSION['ActivePage']); // Clear session variable for active page
                    }

                } else {
                    // If verification code does not match, add error
                    $Erros[] = "Error Of the verification code ";
                }

            } else {
                // If no user found, add error
                $Erros[] = " Errors ";
            }

        }

        // If there are errors, display them
        if(!empty($Erros)){
            foreach($Erros as $error){
                echo "<div class='alert alert-danger text-center' > " . $error . "</div>";
            }
        }

    }
?>
