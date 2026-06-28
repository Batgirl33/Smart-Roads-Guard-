<?php 
include 'includes/templete/header.php'; // Include the header template

// Check if 'id_report' is set in the URL
if(isset($_GET['id_report'])){

    // Prepare SQL statement to retrieve report with related alert and obstacle data
    $sql = "SELECT report.*,alert.*,obstacle.* FROM report INNER JOIN alert ON report.alert_id= alert.alert_id INNER JOIN obstacle ON alert.obstacle_id=obstacle.obstacle_id WHERE report.report_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id_report']); // Bind report ID parameter
    $stmt->execute();
    $result = $stmt->get_result();

    // If report exists
    if ($result->num_rows == 1) {
        $report = $result->fetch_assoc(); // Fetch report data
    } else {
        // Redirect to homepage if report not found
        ?>
        <script>
            setTimeout(function(){
                location.href='homepage.php';
            });
        </script>
        <?php
    }

} else {
    // Redirect to homepage if 'id_report' not set
    ?>
    <script>
        setTimeout(function(){
            location.href='homepage.php';
        });
    </script>
    <?php
}
?>

<!-- Styles for titles and labels -->
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

<!-- Main container -->
<div class=" containerr content-wrapper">
    <div class="details" style="margin-bottom: 100px;">
        <h2 class="text-center" style="padding-bottom: 10px; border-bottom: 2px solid #ddd;">Details Of Report</h2>

        <div class="containder">
            <div class="row" style="margin-bottom: 40px;">
                <div class="col-10" style="margin: auto;">
                    <div class="row ">

                        <!-- Left column: report details -->
                        <div class="col-sm-8 ">
                            <h3 class="title">Details</h3>
                            <div>
                                <p>Report Id <span class="p2"><?php echo  $report['report_id']; ?></span></p>
                            </div>
                            <div>
                                <p>Alert Id <span class="p2"><?php echo  $report['alert_id']; ?></span></p>
                            </div>
                            <div>
                                <p>Camera <span class="p2"><?php echo  $report['camera_id']; ?></span></p>
                            </div>
                            <div>
                                <p>Obstacle Id :<dpsn class="p2"><?php echo  $report['obstacle_id']; ?></dpsn></p>
                            </div>
                            <div>
                                <p>Classification : <span class="p2"><?php echo  $report['classification']; ?></span></p>
                            </div>
                            <div>
                                <p>Status : <span class="p2"><?php echo  $report['status']; ?></span></p>
                            </div>
                            <div>
                                <p>Detection Time :<span class="p2"><?php echo  $report['detection_time']; ?></span></p>
                            </div>
                            <div>
                                <p>Report Submission Data_Time:<span class="p2"><?php echo  $report['report_submission_datetime']; ?></span></p>
                            </div>

                            <!-- Admin notes -->
                            <div>
                                <p>Administrator Notes:
                                    <span class="p2">
                                        <?php
                                        // Fetch admin note related to the report
                                        $sql = "SELECT noted_report.*,users.* FROM noted_report INNER JOIN users ON noted_report.id_user=users.user_id WHERE users.type_user='admin' AND  noted_report.id_report = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $report['report_id']);
                                        $stmt->execute();
                                        $resultt = $stmt->get_result();
                                        if ($resultt->num_rows > 0) {
                                            $info = $resultt->fetch_assoc();
                                            echo $info['noted'];
                                        } else {
                                            echo "No Commentd";
                                        }
                                        ?>
                                    </span>
                                </p>
                            </div>

                            <!-- Service department notes -->
                            <div>
                                <p>Service Department Notes:
                                    <span class="p2">
                                        <?php
                                        // Fetch service department note related to the report
                                        $sql = "SELECT noted_report.*,users.* FROM noted_report INNER JOIN users ON noted_report.id_user=users.user_id WHERE users.type_user='service' AND  noted_report.id_report = ?";
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bind_param("i", $report['report_id']);
                                        $stmt->execute();
                                        $resultt = $stmt->get_result();
                                        if ($resultt->num_rows > 0) {
                                            $info = $resultt->fetch_assoc();
                                            echo $info['noted'];
                                        } else {
                                            echo "No Commentd";
                                        }
                                        ?>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Right column: obstacle image -->
                        <div class="col-sm-4">
                            <div style="text-align: center;">
                                <h3 class="title" style="margin-bottom: 60px;">Obstacle Picture</h3>

                                <!-- Show obstacle image if available, else show logo -->
                                <img style="position: unset; width: 200px; height: 200px;"
                                    src=" <?php if(isset($report['obstacle_picture'])){echo "img/".$report['obstacle_picture'];}else{echo "img/logo-img.png";} ?> "
                                    alt="Smart Roads Guard Logo" class="logo">
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
