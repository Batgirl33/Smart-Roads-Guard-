<?php 
// Include the header template
include 'includes/templete/header.php';

// Check if the user is not logged in (no session UserId)
if(!isset($_SESSION['UserId'])){
    ?>
    <script>
        // Redirect to login page if not logged in
        setTimeout(function(){
            location.href='login.php';
        });
    </script>
    <?php
}

// Check if 'id_alert' is provided in the URL
if(isset($_GET['id_alert'])){
    // Fetch alert and corresponding obstacle data using inner join
    $sql = "SELECT alert.*, obstacle.* FROM alert INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id WHERE alert.alert_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id_alert']);
    $stmt->execute();
    $result = $stmt->get_result();

    // If exactly one result is found
    if ($result->num_rows == 1) {
        // Fetch alert information
        $alert_info = $result->fetch_assoc();

        // Check if there is a report related to the alert
        $check_report = CheckItem('report', 'alert_id', $_GET['id_alert']);
        if($check_report > 0){
            // Get the report details
            $report_info = GetItemWhere('report', 'alert_id', $_GET['id_alert']);
            // Get the note related to the report by this user
            $note_report = GetItemWhere2('noted_report', 'id_report', $report_info['report_id'], 'id_user', $_SESSION['UserId'],'ii');
        } else {
            ?>
            <script>
                // Redirect to alerts page if no report exists
                setTimeout(function(){
                    location.href='alerts.php';
                },1000);
            </script>
            <?php
        }

        // Get user information
        $user = GetItemWhere('users', 'user_id', $_SESSION["UserId"]);
    } else {
        ?>
        <script>
            // Redirect to alerts page if alert not found
            setTimeout(function(){
                location.href='alerts.php';
            });
        </script>
        <?php
    }
} else {
    ?>
    <script>
        // Redirect to alerts page if 'id_alert' is not set
        setTimeout(function(){
            location.href='alerts.php';
        });
    </script>
    <?php
}
?>

<!-- Custom CSS styling -->
<style>
    .title {
        color: #007bff;
        font-size: 26px;
        font-weight: bold;
    }
    .p2 {
        color: #a8a4ad;
        padding-left: 13px;
        border-bottom: 1px;
        border-style: outset;
    }
    .page_success{
        display: none;
    }
    .page_success.show{
        position: fixed;
        text-align: 0;
        top: 0;
        width: 100%;
        height: 120%;
        background: #000000ad;
        z-index: 11111;
        display: unset;
        backdrop-filter: blur(6px);
    }
    textarea.form-control {
        min-height: 200px;
        max-height: 200px;
    }
</style>

<!-- Page content wrapper -->
<div class=" containerr content-wrapper">
    <div class="details">
        <h2 class="text-center" style="padding-bottom: 10px;border-bottom: 2px solid #ddd;" >Report</h2>

        <div class="containder" style="margin-bottom: 100px;">
            <!-- Display the Alert ID -->
            <h2 class="text-center title" style="margin-bottom: 25px;">Alert Id <span class="p2">  (  <?php echo  $alert_info['alert_id']; ?> ) </span></h2>
            <div class="row">
                <div class="col-10" style="margin: auto;">

                    <!-- Hidden form for editing report -->
                    <form id="FormEditeReport" style="display: none;">
                        <input type="hidden" name="id_report" value="<?php echo $report_info['report_id']; ?>">
                        <input type="hidden" name="id_alert" value="<?php echo $_GET['id_alert']; ?>">
                        <input type="hidden" name="type_user" value="<?php echo $user['type_user']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['UserId']; ?>">
                    </form>

                    <div class="row ">

                        <!-- Left column: Alert details -->
                        <div class="col-sm-4 ">
                            <h3 class="title" >Details</h3>
                            <div>
                                <p>Camera Id</p>
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

                            <!-- If the user is a service user, allow status selection -->
                            <?php if($user['type_user'] =='service'){ ?>
                                <div>
                                    <p>Status</p>
                                    <label class="p2"> 
                                        <input <?php if($report_info['status']=='In Progress'){ echo "checked";} ?> id="r1" type="radio" name="status" value="In Progress" form="FormEditeReport" > In Progress 
                                    </label>
                                    <label class="p2"> 
                                        <input <?php if($report_info['status']=='Pending'){ echo "checked";} ?> type="radio" name="status" value="Pending" form="FormEditeReport" > Pending
                                    </label>
                                    <label class="p2"> 
                                        <input <?php if($report_info['status']=='Completed'){ echo "checked";} ?> type="radio" name="status" value="Completed" form="FormEditeReport" > Completed 
                                    </label>
                                </div>
                            <?php } ?>

                            <!-- Back button -->
                            <div class="form-group form-group-lg" style="margin-top: 80px;">
                                <a href="show_alert.php?id_alert=<?php echo $alert_info['alert_id']; ?>">
                                    <button type="button" class="btn btn-primary btn-block">
                                        Previous Page
                                    </button>
                                </a>
                            </div>
                        </div>

                        <!-- Middle column: Service notes and status -->
                        <div class="col-sm-4" style="margin-top: 40px;">
                            <div>
                                <?php if($user['type_user'] !='service'){ ?>
                                    <div>
                                        <p>Service Department Notes</p>
                                        <p class="p2"><?php if(!empty($note_report)){echo $note_report['noted'];} ?></p>
                                    </div>
                                    <div>
                                        <p>Status</p>
                                        <p class="p2"><?php echo $report_info['status']; ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Right column: Obstacle image and notes -->
                        <div class="col-sm-4">
                            <div style="text-align: center;">
                                <h3 class="title" style="margin-bottom: 60px;">Obstacle Picture </h3>
                                <img style="width: 200px;height: 200px;" 
                                    src=" <?php if(isset($alert_info['obstacle_picture'])){echo "img/".$alert_info['obstacle_picture'];}else{echo "img/logo-img.png";} ?> " 
                                    alt="Smart Roads Guard Logo" class="logo">

                                <!-- Textarea for notes -->
                                <div class="form-group form-group-lg" style="margin-top: 50px;">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <textarea class="form-control" name="notes" form="FormEditeReport"><?php if(!empty($note_report)){echo $note_report['noted'];} ?> </textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit button -->
                                <div class="form-group form-group-lg" style="margin-top: 5%;">
                                    <button type="submit" form="FormEditeReport" class="btn btn-primary btn-block">
                                        Edit
                                    </button>
                                </div>
                            </div>
                        </div> <!-- End of right column -->
                    </div> <!-- End of row -->
                </div>
            </div>
        </div>
    </div>

    <!-- Include the footer template -->
    <?php include 'includes/templete/footer.php'; ?>
</div>

<!-- Success message overlay -->
<div class="page_success">
    <div class=""></div>
    <div class="containder" style="width: 90%;margin: auto;margin-top: 100px;">
        <div class="row" style="text-align: center;">
            <div class="col-10" style="margin: auto;">
                <i style="color: white;" class="fa fa-check fa-3x"></i>
                <h1 style="color: white;font-weight: bold;margin-bottom: 53px;">Success</h1>
                <p style="color: white;font-weight: bold;font-size:18px;margin-bottom: 23px;">The report has been sent successfully</p>
            </div>
            <div class="col-md-6 col-sm-10" style="margin: auto;">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group form-group-lg" >
                            <a href="alerts.php" class="btn">
                                <button type="submit" FormEditeReport class="btn btn-primary btn-block">
                                    Back to Homepage
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group form-group-lg" >
                            <a href="reports.php" class="btn">
                                <button type="submit" FormEditeReport class="btn btn-primary btn-block">
                                    View Reports Records
                                </button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>

<!-- JavaScript libraries -->
<script src=layout/js/jquery-3.5.1.min.js></script>
<script src=layout/js/bootstrap.min.js></script>
<script src="layout/js/script.js"></script>

<!-- Handle AJAX form submission for editing the report -->
<script>
$(document).on('submit','#FormEditeReport',function(event){
    event.preventDefault();
    $.ajax({
        type:'POST',
        url:'includes/EditeReport.php',
        beforeSend:function(){
            // Show success overlay while sending
            $('.page_success').addClass('show');
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
