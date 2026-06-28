<?php 
// Include the header template (likely includes session_start and layout)
include 'includes/templete/header.php';

// Redirect to login page if the user is not logged in (session variable 'UserId' not set)
if(!isset($_SESSION['UserId'])){
    ?>
    <script>
        // Redirect to login page after short delay using JavaScript
        setTimeout(function(){
            location.href='login.php';
        });
    </script>
    <?php
}

// If 'id_alert' is present in the URL
if(isset($_GET['id_alert'])){
    // Prepare a SQL query to fetch alert and obstacle info using alert_id
    $sql = "SELECT alert.*, obstacle.* 
            FROM alert 
            INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id 
            WHERE alert.alert_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_GET['id_alert']); // Bind alert ID as integer
    $stmt->execute();
    $result = $stmt->get_result();

    // If a single matching alert is found, fetch its data
    if ($result->num_rows == 1) {
        $alert_info = $result->fetch_assoc();
    } else {
        // Redirect to alerts page if no matching alert found
        ?>
        <script>
            setTimeout(function(){
                location.href='alerts.php';
            });
        </script>
        <?php
    }
} else {
    // Redirect to alerts page if no 'id_alert' in URL
    ?>
    <script>
        setTimeout(function(){
            location.href='alerts.php';
        });
    </script>
    <?php
}
?>

<!-- Inline CSS for UI elements and effects -->
<style>
.show_msg.showss {
    position: fixed;
    top: 60px;
    z-index: 1000000;
    width: 300px;
    height: 200px;
    right: 12px;
}
.title {
    color: #007bff;
    font-size: 26px;
    font-weight: bold;
}
.p2 {
    color: #a8a4ad;
    padding-left: 13px;
}
.page_success {
    display: none;
}
.page_success.show {
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
.form-title {
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    padding: 1.3rem;
    margin-bottom: 0.4rem;
}
</style>

<!-- Main container for the form -->
<div class="containerr content-wrapper">
    <div class="details" style="margin-bottom: 100px;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-md-offset-2">
                    <div class="card list-group">
                        <div class="card-body list-group-item">
                            <h1 class="form-title"> Add Report </h1>

                            <!-- Form to submit a new report -->
                            <form id="AddReport">
                                <!-- Hidden input to hold the current user's ID -->
                                <input type="hidden" name="user_id" value="<?php echo $_SESSION['UserId']; ?>">

                                <!-- Display alert ID (read-only) -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <label class="col-md-2 control-label" style="line-height: 3;">Id Alert</label>
                                        <div class="col-sm-10 col-md-8">
                                            <input id="name" type="number" class="form-control" name="id_alert" value="<?php echo $_GET['id_alert']; ?>" readonly>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dropdown for report status -->
                                <div class="form-group form-group-lg">
                                    <div class="row">
                                        <label class="col-md-2 control-label" style="line-height: 3;">Status</label>
                                        <div class="col-sm-10 col-md-8">
                                            <select name="Status" class="form-control">
                                                <option value="In Progress">In Progress</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Completed">Completed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Textarea for report note -->
                                <div class="form-group form-group-lg" style="margin-bottom: 20px;">
                                    <div class="row">
                                        <label class="col-md-2 control-label" style="line-height: 3;">Note</label>
                                        <div class="col-sm-10 col-md-8">
                                            <textarea style="min-height: 150px;" class="form-control" name="Note" required></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit button -->
                                <div class="row mb-0">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary btn-block">ADD</button>
                                    </div>
                                </div>
                            </form>
                        </div> <!-- card-body -->
                    </div> <!-- card -->
                </div> <!-- col -->
            </div> <!-- row -->
        </div> <!-- container -->
    </div> <!-- details -->

    <!-- Footer include -->
    <?php include 'includes/templete/footer.php'; ?>
</div> <!-- main container -->

<!-- Success message popup (hidden by default) -->
<div class="page_success">
    <div class=""></div>
    <div class="containder" style="width: 90%; margin: auto; margin-top: 100px;">
        <div class="row" style="text-align: center;">
            <div class="col-10" style="margin: auto;">
                <i style="color: white;" class="fa fa-check fa-3x"></i>
                <h1 style="color: white; font-weight: bold; margin-bottom: 53px;">Success</h1>
                <p style="color: white; font-weight: bold; font-size: 18px; margin-bottom: 23px;">The report has been sent successfully</p>
            </div>
            <!-- Buttons to go back or view reports -->
            <div class="col-md-6 col-sm-10" style="margin: auto;">
                <div class="row">
                    <div class="col-6">
                        <div class="form-group form-group-lg">
                            <a href="alerts.php" class="btn">
                                <button type="submit" class="btn btn-primary btn-block">Back to Homepage</button>
                            </a>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group form-group-lg">
                            <a href="reports.php" class="btn">
                                <button type="submit" class="btn btn-primary btn-block">View Reports Records</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- container -->
</div> <!-- success popup -->

<!-- Include necessary JS libraries -->
<script src="layout/js/jquery-3.5.1.min.js"></script>
<script src="layout/js/bootstrap.min.js"></script>
<script src="layout/js/script.js"></script>

<!-- AJAX form submission handler -->
<script>
$(document).on('submit', '#AddReport', function(event){
    event.preventDefault(); // Prevent default form submission
    $.ajax({
        type: 'POST',
        url: 'includes/AddReport.php', // Server-side handler
        beforeSend: function(){
            $('.show_msg').addClass('showss'); // Show loading message
        },
        data: new FormData(this), // Collect form data
        contentType: false,
        processData: false,
        success: function(data){
            $(".show_msg").html(data); // Show server response
        },
        complete: function(data){
        }
    });
});
</script>
</body>
</html>
