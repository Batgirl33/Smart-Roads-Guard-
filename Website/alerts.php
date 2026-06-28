<?php 
// Include the header template (includes HTML head, session start, navigation, etc.)
include 'includes/templete/header.php';

// SQL query to retrieve all alerts and their associated obstacles
// Ordered by the most recent alert first (descending order)
$sql = "SELECT alert.*, obstacle.* 
        FROM alert 
        INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id 
        ORDER BY alert.alert_id DESC";

// Execute the query
$result = $conn->query($sql);
?>

<!-- Main container for the page -->
<div class="containerr content-wrapper">
    <div class="details">
        <h2 class="text-center">All Alerts</h2>

        <!-- Search Form for Alert by ID -->
        <div style="display: flex; justify-content: space-between;">
            <form id="Search" style="margin-top:0px; margin-left: auto;">
                <!-- Input field to enter Alert ID -->
                <input class="form-control" type="number" name="search_id" style="width: 65%; display: unset;" required>
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>

        <!-- Table displaying list of alerts -->
        <table>
            <thead>
                <tr>
                    <th>Alert Id</th>
                    <th>Classification</th>
                    <th>Detection Time</th>
                    <th>Obstacle Id</th>
                    <th>More Details</th>
                </tr>
            </thead>
            <tbody class="all_alerts">
                <?php
                // If there are results, loop through them and display each alert
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['alert_id']; ?></td>
                            <td><?php echo $row['classification']; ?></td>
                            <td><?php echo $row['detection_time']; ?></td>
                            <td><?php echo $row['obstacle_id']; ?></td>
                            <td>
                                <!-- Link to view more details of a specific alert -->
                                <a href="show_alert.php?id_alert=<?php echo $row['alert_id']; ?>" class="btn btn-success">View</a>
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

<!-- Include JavaScript files -->
<script src="layout/js/jquery-3.5.1.min.js"></script>
<script src="layout/js/bootstrap.min.js"></script>
<script src="layout/js/script.js"></script>

<!-- AJAX handler for the search form -->
<script>
$(document).on('submit', '#Search', function(event){
    event.preventDefault(); // Prevent default form submission

    $.ajax({
        type: 'POST',
        url: 'includes/Search.php?p=alert', // Call search handler for alerts
        data: new FormData(this), // Send form data
        contentType: false,
        processData: false,
        success: function(data){
            // Replace the contents of the alerts table with the search results
            $(".all_alerts").html(data);
        },
        complete: function(data){
        }
    });
});
</script>

</body>
</html>
