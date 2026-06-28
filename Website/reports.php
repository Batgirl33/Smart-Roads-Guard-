<?php 
include 'includes/templete/header.php'; // Include the header file

// SQL query to select all reports from the database
$sql = "SELECT * FROM report";
$result = $conn->query($sql); // Execute the query
?>

<style>
    /* Styling for the report table layout */
    table{
        width: 100%;
        table-layout: fixed;
        word-wrap: break-word;
    }
    td{
        width: 10%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Set specific width for each column */
    thead tr th:nth-child(1){ width: 10%; }
    thead tr th:nth-child(2){ width: 10%; }
    thead tr th:nth-child(3){ width: 15%; }
    thead tr th:nth-child(4){ width: 15%; }
    thead tr th:nth-child(5){ width: 15%; }
    thead tr th:nth-child(6){ width: 10%; }
</style>

<!-- Main container for displaying reports -->
<div class=" containerr content-wrapper">
    <div class="details">
        <h2 class="text-center">All Reports</h2>
        <div class="" style="display: flex; justify-content: space-between;">
            <!-- Search form for report ID -->
            <form id="Search" style="margin-top:0px; margin-left: auto;">
                <input class="form-control" type="number" name="search_id" style="width: 65%; display: unset;" required>
                <button type="submit" class="btn btn-success">Search</button>
            </form>
        </div>

        <!-- Reports table -->
        <table id="product-table">
            <thead>
                <tr>
                    <th>Report Id</th>
                    <th>Status</th>
                    <th>Report Submission Data Time</th>
                    <th>Administrator Notes</th>
                    <th>Service Department Notes</th>
                    <th>More Details</th>
                </tr>
            </thead>

            <tbody class="all_reports">
            <?php
                if ($result->num_rows > 0) {
                    // Loop through each report
                    while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo  $row['report_id']; ?></td>
                            <td><?php echo  $row['status']; ?></td>
                            <td><?php echo  $row['report_submission_datetime']; ?></td>
                            <td>
                                <?php
                                // Fetch admin note for this report
                                $sql = "SELECT noted_report.*,users.* FROM noted_report INNER JOIN users ON noted_report.id_user=users.user_id WHERE users.type_user='admin' AND noted_report.id_report = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $row['report_id']);
                                $stmt->execute();
                                $resultt = $stmt->get_result();
                                if ($resultt->num_rows > 0) {
                                    $info = $resultt->fetch_assoc();
                                    echo $info['noted']; // Display admin note
                                } else {
                                    echo "No Commentd"; // No comment found
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                // Fetch service department note for this report
                                $sql = "SELECT noted_report.*,users.* FROM noted_report INNER JOIN users ON noted_report.id_user=users.user_id WHERE users.type_user='service' AND noted_report.id_report = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $row['report_id']);
                                $stmt->execute();
                                $resultt = $stmt->get_result();
                                if ($resultt->num_rows > 0) {
                                    $info = $resultt->fetch_assoc();
                                    echo $info['noted']; // Display service note
                                } else {
                                    echo "No Commentd"; // No comment found
                                }
                                ?>
                            </td>
                            <td>
                                <!-- Link to view report details -->
                                <a href="show_reports.php?id_report=<?php echo $row['report_id']; ?>" class="btn btn-success"> View</a>
                            </td>
                        </tr>
                    <?php }
                }
            ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Include footer -->
<?php include 'includes/templete/footer.php'; ?>

<!-- Scripts -->
<script src=layout/js/jquery-3.5.1.min.js></script>
<script src=layout/js/bootstrap.min.js></script>
<script src="layout/js/script.js"></script>

<script>
// Handle AJAX search submission
$(document).on('submit','#Search',function(event){
    event.preventDefault(); // Prevent default form submit
    $.ajax({
        type:'POST',
        url:'includes/Search.php?p=report', // Search endpoint
        data:new FormData(this),
        contentType:false,
        processData:false,
        success:function(data){
            $(".all_reports").html(data); // Update the report table body
        },
        complete:function(data){
            // Optional: handle complete event
        }
    })
});
</script>

</body>
</html>

