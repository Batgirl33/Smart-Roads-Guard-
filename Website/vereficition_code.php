
<?php 
session_start(); // Start session

// Check if ActivePage session variable is set and assign it to $id
if(isset($_SESSION['ActivePage'])){
    $id = $_SESSION['ActivePage'];
}

// Check if UserId_Password session variable is set and assign it to $id
if(isset($_SESSION['UserId_Password'])){
    $id = $_SESSION['UserId_Password'];
}

?>

<!DOCTYPE html>
<html>
    <head>
        <title> Verification Code </title>
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
                                <h1 class="form-title"> Verification Code </h1>

                                <!-- Form to submit verification code -->
                                <form id="Code">
                                    <!-- Hidden input for user ID -->
                                    <input type="hidden" name="id_user" value="<?php echo $id; ?>">

                                    <!-- Verification code input -->
                                    <div class="form-group form-group-lg">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="input-group">
                                                    <i class="fa fa-hashtag"></i>
                                                    <input type="number" name="code_id" id="id" placeholder="Verification Code" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit button -->
                                    <div class="row mb-0">
                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                Activate
                                            </button>
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

        <!-- Inline script for form submission -->
        <script>
            // AJAX form submission for verification code
            $(document).on('submit','#Code', function(event){
                event.preventDefault(); // Prevent default form submission
                $.ajax({
                    type: 'POST',
                    url: 'includes/VereficitionCode.php', // URL for AJAX request
                    beforeSend: function() {
                        $('.show_msg').addClass('showss'); // Show message container
                    },
                    data: new FormData(this), // Send form data
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $(".show_msg").html(data); // Display server response
                    },
                    complete: function(data) {
                        // Optional: logic after request completes
                    }
                });
            });
        </script>
    </body>
</html>