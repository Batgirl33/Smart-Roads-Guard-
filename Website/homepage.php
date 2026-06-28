<?php 
// Include the header template
include 'includes/templete/header.php';

// Prepare SQL query to fetch all alerts with their related obstacle data
$sql = "SELECT alert.*, obstacle.* FROM alert INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id";
$result = $conn->query($sql); // Execute the query
?>

<!-- Main content container -->
<div class=" containerr content-wrapper">
    <div class="details">
        <!-- Page title -->
        <h2 class="text-center">All Alerts</h2>

        <!-- Search form aligned to the right -->
        <div class="" style="display: flex; justify-content: space-between;">
            <form id="Search" style="margin-top:0px; margin-left: auto;">
                <!-- Input to search by alert ID -->
                <input class="form-control" type="number" name="search_id" style="width: 65%; display: unset;" required>
                <!-- Submit button for search -->
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>

        <!-- Table to display alerts -->
        <table id="">
            <thead>
                <tr>
                    <th>Alert Id</th>
                    <th>Classification </th>
                    <th>Detection Time</th>
                    <th>Obstacle Id</th>
                    <th>More Details</th>
                </tr>
            </thead>
            <tbody class="all_alerts">
                <?php
                // If there are alerts in the result set, loop through and display them
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <!-- Display alert details in each row -->
                            <td><?php echo  $row['alert_id']; ?></td>
                            <td><?php echo  $row['classification']; ?></td>
                            <td><?php echo  $row['detection_time']; ?></td>
                            <td><?php echo  $row['obstacle_id']; ?></td>
                            <td>
                                <!-- Button to view detailed alert page -->
                                <a href="show_alert.php?id_alert=<?php echo $row['alert_id']; ?>" class="btn btn-success"> View</a>
                            </td>
                        </tr>
                    <?php }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include footer template -->
<?php include 'includes/templete/footer.php'; ?>

<!-- JavaScript dependencies -->
<script src=layout/js/jquery-3.5.1.min.js></script>
<script src=layout/js/bootstrap.min.js></script>
<script src="layout/js/script.js"></script>

<script>
// Handle search form submission using AJAX
$(document).on('submit', '#Search', function(event){
    event.preventDefault(); // Prevent default form submission
    $.ajax({
        type: 'POST',
        url: 'includes/Search.php?p=alert', // Send request to search script
        data: new FormData(this), // Send form data
        contentType: false,
        processData: false,
        success: function(data){
            // Replace the table body with the search results
            $(".all_alerts").html(data);
        },
        complete: function(data){
        }
    });
});
</script>

</body>
</html>
