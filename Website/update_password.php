<?php 
session_start(); // Start session

// Check if user session is set; if not, redirect to login page
if(!isset($_SESSION['UserId_Password'])){
    header("Location:login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title> Active Page </title>
    <link rel="icon" type="image/png" href="img/logo2.png"> <!-- Page favicon -->

    <!-- Stylesheets -->
    <link media="all" type=text/css rel="stylesheet" href="layout/css/font-awesome.min.css">
    <link media="all" type=text/css rel="stylesheet" href="layout/css/bootstrap.min.css">
    <link rel="stylesheet" href="layout/css/styling.css" />

    <!-- Inline CSS styling -->
    <style>
        .show_msg.showss {
            position: fixed;
            top: 60px;
            z-index: 1000000;
            width: 300px;
            height: 200px;
            right: 12px;
        }

        .close_msg {
            float: right;
            margin: 11px;
            font-size: 18px;
            cursor: pointer;
        }

        #validation-messages { display: none; }
        .valid { color: green; }
        .invalid { color: red; }

        body {
            background: linear-gradient(to right, #e2e2e2, #43b0f1);
        }

        .form-title {
            font-size: 1.1rem;
            font-weight: 700;
            text-align: center;
            padding: 1.3rem;
            margin-bottom: 0.4rem;
        }
    </style>
</head>
<body>
<!-- Message container -->
<div class="show_msg "> </div>

<!-- Main content wrapper -->
<div class="container-fluid">
    <div class="container" style="min-height: 500px; margin-top: 30px;">
        <div class="row justify-content-center">
            <div class="col-md-6 col-sm-12 col-md-offset-4">
                <div class="card list-group">
                    <div class="card-body list-group-item">
                        <h1 class="form-title"> Update Password </h1>
                        
                        <!-- Update password form -->
                        <form id="UpdatePassword">
                            <!-- Hidden input for user ID from session -->
                            <input type="hidden" name="id_user" value="<?php echo $_SESSION['UserId_Password'];?>" >

                            <!-- Password input -->
                            <div class="form-group form-group-lg">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <i class="fa fa-lock"></i>
                                            <input type="password" name="password1" id="password" placeholder="Password" onkeyup="validatePassword()" required>
                                            <i id="eye" class="fa fa-eye eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Confirm password input -->
                            <div class="form-group form-group-lg">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <i class="fa fa-lock"></i>
                                            <input type="password" name="password2" id="password1" placeholder="Confirm Password" required>
                                            <i id="eye1" class="fa fa-eye eye"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password validation messages -->
                            <ul id="validation-messages">
                                <li id="length" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must be at least 8 characters</li>
                                <li id="number" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one number</li>
                                <li id="uppercase" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one uppercase letter</li>
                                <li id="lowercase" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one lowercase letter</li>
                                <li id="special" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one special character (!@#$%^&*)</li>
                            </ul>

                            <!-- Submit button -->
                            <div class="form-group form-group-lg" style="margin-top: 15px;">
                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Activate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <!-- End form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript dependencies -->
<script src=layout/js/jquery-3.5.1.min.js></script>
<script src=layout/js/bootstrap.min.js></script>
<script src="layout/js/script.js"></script>

<!-- Inline script for password validation -->
<script>
    function validatePassword() {
        var password = document.getElementById("password").value;

        // Show validation messages if input is not empty
        if (password.length > 0) {
            $("#validation-messages").show();
        } else {
            $("#validation-messages").hide();
        }

        // Validation conditions
        var conditions = {
            length: password.length >= 8,
            number: /[0-9]/.test(password),
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            special: /[!@#$%^&*]/.test(password)
        };

        // Loop through each condition and update UI
        for (var condition in conditions) {
            var element = document.getElementById(condition);
            var icon = element.querySelector("i");

            if (conditions[condition]) {
                element.classList.add("valid");
                element.classList.remove("invalid");
                icon.classList.remove("fa-xmark");
                icon.classList.add("fa-check");
            } else {
                element.classList.add("invalid");
                element.classList.remove("valid");
                icon.classList.remove("fa-check");
                icon.classList.add("fa-xmark");
            }
        }
    }

    // AJAX form submission to update password
    $(document).on('submit', '#UpdatePassword', function(event){
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: 'includes/UpdatePassword.php',
            beforeSend: function(){
                $('.show_msg').addClass('showss'); // Show message container
            },
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function(data){
                $(".show_msg").html(data); // Display server response
            },
            complete: function(data){
                // Optional: logic after request completes
            }
        });
    });
</script>
</body>
</html>

