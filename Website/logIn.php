<?php 
session_start(); // Start the session

// If the user is already logged in, redirect to homepage
if(isset($_SESSION['UserId'])){
    header("Location:homepage.php");
    exit();
}

$type_login='';

// Validate the 'p' parameter (must be either 'admin' or 'service')
if(!isset($_GET['p']) || ($_GET['p'] != 'admin' && $_GET['p'] != 'service' ) ){
    header("Location:index.php");
    exit();
}else{
    // Set login type based on 'p' parameter
    if($_GET['p']=='admin'){  
        $type_login ='admin'; 
    }else{
        $type_login = 'service'; 
    }
}

include "conect.php"; // Include database connection

$p = '';

// Set display name for the login type
if(isset($_GET['p']) && $_GET['p']=='admin'){ 
    $p =' In Admin '; 
}elseif(isset($_GET['p']) && $_GET['p']=='service'){ 
    $p =' In Service Department '; 
}

// Clear specific session variables if set
if(isset($_SESSION['UserId_Password'])){ unset($_SESSION['UserId_Password']);}
if(isset($_SESSION['ActivePage'])){ unset($_SESSION['ActivePage']);}
?>

<!DOCTYPE html>
<html>
    <head>
        <title> Login </title>
        <link rel="icon" type="image/png" href="img/logo2.png">

        <!-- Include CSS files -->
        <link media="all" type=text/css rel="stylesheet" href="layout/css/font-awesome.min.css">
        <link media="all" type=text/css rel="stylesheet" href="layout/css/bootstrap.min.css">
        <link rel="stylesheet" href="layout/css/styling.css" />

        <style>
        /* Styling for message box */
        .show_msg.showss {
            position: fixed;
            top: 60px;
            z-index: 1000000;
            width: 300px;
            height: 200px;
            right: 12px;
        }

        .close_msg{
            float: right;
            margin: 11px;
            font-size: 18px;
            cursor: pointer;
        }

        /* Background and form title styling */
        body{
            background: linear-gradient(to right, #e2e2e2, #43b0f1);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            padding: 1.3rem;
            margin-bottom: 0.4rem;
        }
        </style>
    </head>
    <body>
        <!-- Container to display login response messages -->
        <div class="show_msg "> </div>

        <!-- Header section -->
        <div class="container-fluid">

        <!-- Login form container -->
        <div class="container" style="min-height: 500px; margin-top: 30px;">
            <div class="row justify-content-center">
                <div class="col-md-6 col-sm-12 col-md-offset-4">
                    <div class="card list-group">

                        <!-- Login form body -->
                        <div class="card-body list-group-item">
                            <h1 class="form-title"> Login <?php echo $p; ?> </h1>
                            <form id="LogIn">
                                <!-- Hidden input to store the login type -->
                                <input type="hidden" name="type_login" value="<?php echo $type_login; ?>">

                                <!-- Email input -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-envelope"></i>
                                                
                                                <input id="email" type="email" name="email" placeholder="Email" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password input with eye icon -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-lock"></i>
                                                <input id="password" type="password" name="password" placeholder="Password" required>
                                                <i id="eye" class="fa fa-eye-slash eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Links for account recovery and activation -->
                                <p class="recover" style="margin-bottom: 0; margin-top: 11px;">
                                    <a href="forget_password.php">Forget Password?</a>
                                </p>
                                <p class="recover">
                                    <a href="activate_page.php">Your account is not activated?</a>
                                </p>

                                <!-- Submit button -->
                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Log In
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- end card-body -->
                    </div> <!-- end card -->
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- end container -->
    </div> <!-- end container-fluid -->

    <!-- Scripts -->
    <script src=layout/js/jquery-3.5.1.min.js></script>
    <script src=layout/js/bootstrap.min.js></script>
    <script src="layout/js/script.js"></script>

    <script>
    // Handle login form submission via AJAX
    $(document).on('submit','#LogIn',function(event){
        event.preventDefault(); // Prevent default form submission
        $.ajax({
            type:'POST',
            url:'includes/Login.php', // Backend script to handle login
            beforeSend:function(){
                $('.show_msg').addClass('showss'); // Show loading message container
            },
            data:new FormData(this),
            contentType:false,
            processData:false,
            success:function(data){
                $(".show_msg").html(data); // Display server response
            },
            complete:function(data){
                // Optional: post-processing
            }
        })
    });
    </script>

</body>
</html>
