<?php 
include 'includes/templete/header.php'; // Include header template

// Check if 'id_alert' is set in the GET request
if(isset($_GET['id_alert'])){

    // Prepare SQL query to retrieve alert and its related obstacle information
    $sql = "SELECT alert.*, obstacle.* FROM alert INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id WHERE alert.alert_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id_alert']); // Bind alert ID to the query
    $stmt->execute();
    $result = $stmt->get_result();

    // If exactly one result is found
    if ($result->num_rows == 1) {
        $alert_info = $result->fetch_assoc(); // Fetch alert info

        // Check if a report is already linked to this alert
        $isset_report = CheckItem( 'report' , 'alert_id' ,  $_GET['id_alert']);

    } else {
        // If no alert found, redirect to homepage after short delay
        ?>
        <script>
            setTimeout(function(){
                location.href='homepage.php';
            });
        </script>
        <?php
    }

} else {
    // If 'id_alert' is not set, redirect to homepage after short delay
    ?>
    <script>
        setTimeout(function(){
            location.href='homepage.php';
        });
    </script>
    <?php
}
?>

<!-- Styles for alert page -->
<style>
    .title {
        color: #007bff;
        font-size: 26px;
        font-weight: bold;
    }
    .p2 {
        color: #a8a4ad;
        padding-left: 13px;
    }
</style>

<!-- Main container for alert details -->
<div class=" containerr content-wrapper">
    <div class="details" style="margin-bottom: 100px;">
        <h2 class="text-center" style="padding-bottom: 10px; border-bottom: 2px solid #ddd;">Alert</h2>

        <div class="containder">
            <h2 class="text-center title" style="margin-bottom: 25px;">
                Alert Id <span class="p2">( <?php echo  $alert_info['alert_id']; ?> )</span>
            </h2>

            <div class="row">
                <div class="col-10" style="margin: auto;">
                    <div class="row ">

                        <!-- Left column with alert details -->
                        <div class="col-sm-4 ">
                            <h3 class="title">Details</h3>
                            <div>
                                <p>Camera</p>
                                <p class="p2"><?php echo  $alert_info['camera_id']; ?></p>
                            </div>
                            <div>
                                <p>Obstacle Id</p>
                                <p class="p2"><?php echo  $alert_info['obstacle_id']; ?></p>
                            </div>
                            <div>
                                <p>Obstacle Classification</p>
                                <p class="p2"><?php echo  $alert_info['classification']; ?></p>
                            </div>
                            <div>
                                <p>Detection Time</p>
                                <p class="p2"><?php echo  $alert_info['detection_time']; ?></p>
                            </div>

                            <!-- Back button to return to previous page -->
                            <div class="form-group form-group-lg" style="margin-top: 60px;">
                                <a href="alerts.php">
                                    <button type="button" class="btn btn-primary btn-block">
                                        Previous Page
                                    </button>
                                </a>
                            </div>
                        </div>

                        <!-- Empty middle column -->
                        <div class="col-sm-4"></div>

                        <!-- Right column with obstacle picture and report actions -->
                        <div class="col-sm-4">
                            <div style="text-align: center;">
                                <h3 class="title" style="margin-bottom: 60px;">Obstacle Picture</h3>

                                <!-- Display obstacle image if available -->
                                <img style="position: unset; width: 200px; height: 200px;"
                                    src="<?php if(isset($alert_info['obstacle_picture'])){echo "img/".$alert_info['obstacle_picture'];}else{echo "img/logo-img.png";} ?>"
                                    alt="Smart Roads Guard Logo" class="logo">

                                <?php
                                // Check current user's type
                                $if_service = GetItemWhere( 'users' , 'user_id' , $_SESSION["UserId"]);

                                // If a report already exists, show edit button
                                if($isset_report > 0){ ?>
                                    <div class="form-group form-group-lg" style="margin-top: 113px;">
                                        <a href="edite_report.php?id_alert=<?php echo $alert_info['alert_id']; ?>">
                                            <button type="button" class="btn btn-primary btn-block">
                                                Edit
                                            </button>
                                        </a>
                                    </div>

                                <?php } else {
                                    // If no report exists and user is from service department, show add report button
                                    if($if_service['type_user'] =='service'){ ?>
                                        <div class="form-group form-group-lg" style="margin-top: 113px;">
                                            <a href="add_report.php?id_alert=<?php echo $alert_info['alert_id']; ?>">
                                                <button type="button" class="btn btn-primary btn-block">
                                                    ADD Reports Of Alert
                                                </button>
                                            </a>
                                        </div>
                                    <?php }
                                }
                                ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include footer -->
    <?php include 'includes/templete/footer.php'; ?>
</div>

<!-- Scripts -->
<script src=layout/js/jquery-3.5.1.min.js></script>
<script src=layout/js/bootstrap.min.js></script>
<script src="layout/js/script.js"></script>

<script>
// Reserved for future JavaScript
</script>

</body>
</html>

