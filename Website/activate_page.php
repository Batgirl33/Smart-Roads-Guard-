<?php
// Start the session to manage user session data
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title> Active Page </title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="img/logo2.png">

    <!-- Font Awesome and Bootstrap CSS -->
    <link media="all" type="text/css" rel="stylesheet" href="layout/css/font-awesome.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="layout/css/bootstrap.min.css">
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

        #validation-messages { display: none; }
        .valid { color: green; }
        .invalid { color: red; }
    </style>
</head>
<body>
    <!-- Placeholder for displaying messages -->
    <div class="show_msg"> </div>

    <!-- Main Container -->
    <div class="container-fluid">
        <div class="container" style="min-height: 500px; margin-top: 30px;">
            <div class="row justify-content-center">
                <div class="col-md-6 col-sm-12 col-md-offset-4">
                    <div class="card list-group">
                        <div class="card-body list-group-item">
                            <h1 class="form-title"> Activate Your Account </h1>

                            <!-- Activation form -->
                            <form id="ActivePage">
                                <!-- User ID input -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-hashtag"></i>
                                                <input type="number" name="id" id="id" placeholder="User ID" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Temporary password input -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-lock"></i>
                                                <input type="password" name="password_temp" id="password" placeholder="Temporary Password" required>
                                                <i id="eye" class="fa fa-eye eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Confirm password input with validation -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-lock"></i>
                                                <input type="password" name="password1" id="password1" onkeyup="validatePassword()" placeholder="New Password" required>
                                                <i id="eye1" class="fa fa-eye eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Re-enter password input -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="input-group">
                                                <i class="fa fa-lock"></i>
                                                <input type="password"  name="password2" id="password2" placeholder="Confirm Password" required>
                                                <i id="eye2" class="fa fa-eye eye"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password validation message list -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <ul id="validation-messages">
                                                <li id="length" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must be at least 8 characters</li>
                                                <li id="number" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one number</li>
                                                <li id="uppercase" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one uppercase letter</li>
                                                <li id="lowercase" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one lowercase letter</li>
                                                <li id="special" class="invalid"><i class="fa fa-solid fa-xmark"></i> Must contain at least one special character (!@#$%^&*)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form buttons -->
                                <div class="form-group form-group-lg" style="margin-top: 20px;">
                                    <div class="row">
                                        <!-- Cancel button redirects to login -->
                                        <div class="col-sm-6">
                                            <a href="login.php">
                                                <button type="button" class="btn btn-primary btn-block">
                                                    Cancel
                                                </button>
                                            </a>
                                        </div>

                                        <!-- Submit activation button -->
                                        <div class="col-sm-6">
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

    <!-- JavaScript includes -->
    <script src="layout/js/jquery-3.5.1.min.js"></script>
    <script src="layout/js/bootstrap.min.js"></script>
    <script src="layout/js/script.js"></script>

    <!-- Password validation script -->
    <script>
        function validatePassword() {
            var password = document.getElementById("password1").value;

            // Show validation box if password field is not empty
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

            // Update UI for each condition
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

        // Handle form submission using AJAX
        $(document).on('submit', '#ActivePage', function(event){
            event.preventDefault(); // Prevent default form submission

            $.ajax({
                type: 'POST',
                url: 'includes/ActivePage.php',
                beforeSend: function(){
                    // Show message box while processing
                    $('.show_msg').addClass('showss');
                },
                data: new FormData(this),
                contentType: false,
                processData: false,
                success: function(data){
                    // Display response message
                    $(".show_msg").html(data);
                },
                complete: function(data){
                }
            });
        });
    </script>
</body>
</html>
