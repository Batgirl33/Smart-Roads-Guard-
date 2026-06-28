<?php
session_start();
include "conect.php";
include 'includes/function/function.php';
if(isset($_SESSION["UserId"])){

    $UserId = $_SESSION["UserId"];

    $user = GetItemWhere( 'users' , 'user_id' , $_SESSION["UserId"]);


}else{
    header("location:login.php");

}

$currentPage = basename($_SERVER['PHP_SELF']);
$page = pathinfo($currentPage,PATHINFO_FILENAME);




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link media="all" type=text/css rel="stylesheet" href="layout/css/demo.css">
    <link media="all" type=text/css rel="stylesheet" href="layout/css/font-awesome.min.css">
    <link media="all" type=text/css rel="stylesheet" href="layout/css/bootstrap.min.css">
    <link href="layout/css/navbar.css" rel="stylesheet" type="text/css" />
    <link href="layout/css/Style_Admin.css" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="layout/css/styling.css" />
    <title><?PHP echo $page; ?></title>
    <link rel="icon" type="image/png" href="img/logo2.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            /* width: 100%;
            min-height: 500px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px; */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            border-spacing: 0;
            box-shadow: 0 2px 15px rgba(12, 24, 35, .7);
            border-radius: 12px 12px 0 0;
        }

        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }
        .theLogo{
    text-align: center;
    font-size: x-large;
    font-family: Georgia, 'Times New Roman', Times, serif;
}

    </style>
</head>
<body class="skin-blue" >
<div class="show_msg "> </div>

<div class=" wrapper">
<!-- container-fluid -->
<header class="main-header">
            <a href="alerts.php" class="logo"><b>Home</b></a>

            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle sidebar-toggle_a" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                </a>
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">


                        <!-- الاشعارات -->
                          <?php
                                $sql = "SELECT alert.*, obstacle.* FROM alert INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id ORDER BY alert.alert_id DESC";
                                $result = $conn->query($sql);
                                $count = $result->num_rows;

                                ?>
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-warning"><?php echo $count; ?> </span>
                            </a>
                            <ul class="dropdown-menu">

                                <li class="header">You have <?php echo $count; ?> notifications</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
                                    <ul class="menu">
                                        <?php
                                         if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) { ?>
                                             <li>
                                                <a href="show_alert.php?id_alert=<?php echo $row['alert_id']; ?>">
                                                    <i class="fa fa-warning text-yellow"></i>Alert Id <?php echo  $row['alert_id'] . "   "; ?> Time: <?php  echo $row['detection_time']; ?>
                                                </a>
                                            </li>
                                            <?php

                                            }
                                        }
                                        ?>

                                    </ul>
                                </li>
                                <li class="footer"><a href="alerts.php">View all</a></li>
                            </ul>

                        </li>

                        <li class="dropdown tasks-menu">
                            <a href="logout.php" >
                                <i class="fa fa-sign-out"></i>
                            </a>

                        </li>
                    </ul>
                </div>
            </nav>
</header>


        <!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="img/logo.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p>Hi:<?php echo $user['username']; ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" id="sidebar_all_href_page">
            <li class="header">Pages</li>




            <li><a href="reports.php"><i class="fa fa-circle-o text-danger"></i> Reports</a></li>
            <li><a href="alerts.php"><i class="fa fa-circle-o text-warning"></i> Alerts</a></li>
            <!-- <li><a href="#"><i class="fa fa-circle-o text-info"></i> Information</a></li> -->
            <li><a href="logout.php"><i class="fa fa-sign-out  "></i> LogOut</a></li>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

