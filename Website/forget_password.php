<?php
session_start();
include "conect.php";

?>

<!DOCTYPE html>
<html>
    <head>
        <title> Forget Password </title>
        <link rel="icon" type="image/png" href="img/logo2.png">

        <!-- <link href="../layout/css/bootstrap.css" rel="stylesheet"  /> -->
        <link media="all" type=text/css rel="stylesheet" href="layout/css/font-awesome.min.css">
        <link media="all" type=text/css rel="stylesheet" href="layout/css/bootstrap.min.css">
    <link rel="stylesheet" href="layout/css/styling.css" />
    <style>
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

    body{
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
        <div class="show_msg "> </div>


         <!--Header-->
         <div class="container-fluid">


        <div class="container" style=" min-height: 500px;margin-top: 30px;">
            <div class="row justify-content-center">
                <div class="col-md-6 col-sm-12 col-md-offset-4">
                    <div class="card list-group">

                        <div class="card-body list-group-item">
                        <h1  class=" form-title"> Enter Your Email To Send The Verification Code To It </h1>
                            <form id="ForgetPassword">

                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class=" input-group">
                                                <i class="fa fa-envelope"></i>
                                                <input id="email" type="email" placeholder="Email" class=" " name="email" value="" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script src=layout/js/jquery-3.5.1.min.js></script>
    <script src=layout/js/bootstrap.min.js></script>
    <script src="layout/js/script.js"></script>


<script>

$(document).on('submit','#ForgetPassword',function(event){
    event.preventDefault();
    $.ajax({
        type:'POST',
        url:'includes/ForgetPassword.php',
        beforeSend:function(){

            $('.show_msg').addClass('showss');
        },
        data:new FormData(this),
        contentType:false,
        processData:false,
        success:function(data){
            $(".show_msg").html(data);
        },
        complete:function(data){

        }
    })
});

</script>

</body>
</html>
