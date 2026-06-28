<?php 
    session_start(); // Start the session

    include '../conect.php'; // Include the database connection file

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Check if the form is submitted via POST

        // Check if the 'p' parameter in the URL is set to 'alert'
        if (isset($_GET['p']) && $_GET['p'] == 'alert') {

            $search_id = $_POST['search_id']; // Get the search ID from the POST data

            if (!empty($search_id)) { // If search ID is not empty

                // Query to fetch alert and obstacle data based on the alert ID
                $sql = "SELECT alert.*, obstacle.* FROM alert INNER JOIN obstacle ON alert.obstacle_id = obstacle.obstacle_id WHERE alert_id = ?";
                $stmt = $conn->prepare($sql); // Prepare the SQL statement
                $stmt->bind_param("i", $search_id); // Bind the search ID parameter
                $stmt->execute(); // Execute the query
                $result = $stmt->get_result(); // Get the result

                // If a matching alert is found
                if ($result->num_rows == 1) {
                    $alert = $result->fetch_assoc(); // Fetch the alert data
                    ?>
                        <tr>
                            <td><?php echo $alert['alert_id']; ?></td>
                            <td><?php echo $alert['classification']; ?></td>
                            <td><?php echo $alert['detection_time']; ?></td>
                            <td><?php echo $alert['obstacle_id']; ?></td>
                            <td>
                                <a href="show_alert.php?id_alert=<?php echo $alert['alert_id']; ?>" class="btn btn-success"> View</a>
                            </td>
                        </tr>
                    <?php
                } else {
                    // If no data is found, display "Not Data" message
                    echo "<tr> <td colspan='6'> Not Data </td> </tr>";
                }

            } else {
                // If search ID is empty, display "Not Data" message
                echo "<tr> <td colspan='6'> Not Data </td> </tr>";
            }

        // Check if the 'p' parameter in the URL is set to 'report'
        } elseif (isset($_GET['p']) && $_GET['p'] == 'report') {

            $search_id = $_POST['search_id']; // Get the search ID from the POST data

            if (!empty($search_id)) { // If search ID is not empty

                // Query to fetch report data based on the report ID
                $sql = "SELECT * FROM report WHERE report_id = ?";
                $stmt = $conn->prepare($sql); // Prepare the SQL statement
                $stmt->bind_param("i", $search_id); // Bind the search ID parameter
                $stmt->execute(); // Execute the query
                $result = $stmt->get_result(); // Get the result

                // If a matching report is found
                if ($result->num_rows == 1) {
                    $alert = $result->fetch_assoc(); // Fetch the report data
                    ?>
                        <tr>
                            <td><?php echo $alert['report_id']; ?></td>
                            <td><?php echo $alert['status']; ?></td>
                            <td><?php echo $alert['report_submission_datetime']; ?></td>
                            <td><?php
                                // Query to fetch comments from the admin about the report
                                $sql = "SELECT noted_report.*, users.* FROM noted_report INNER JOIN users ON noted_report.id_user = users.user_id WHERE users.type_user = 'admin' AND noted_report.id_report = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $search_id);
                                $stmt->execute();
                                $rest = $stmt->get_result();
                                // If admin comments are found
                                if ($rest->num_rows > 0) {
                                    $info = $rest->fetch_assoc();
                                    echo $info['noted']; // Display the admin comment
                                } else {
                                    echo "No Comments"; // If no comments, display message
                                }
                                ?>
                            </td>
                            <td><?php
                                // Query to fetch comments from the service team about the report
                                $sql = "SELECT noted_report.*, users.* FROM noted_report INNER JOIN users ON noted_report.id_user = users.user_id WHERE users.type_user = 'service' AND noted_report.id_report = ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $search_id);
                                $stmt->execute();
                                $resultt = $stmt->get_result();
                                // If service comments are found
                                if ($resultt->num_rows > 0) {
                                    $info = $resultt->fetch_assoc();
                                    echo $info['noted']; // Display the service comment
                                } else {
                                    echo "No Comments"; // If no comments, display message
                                }
                                ?>
                            </td>
                            <td>
                                <a href="show_reports.php?id_report=<?php echo $alert['report_id']; ?>" class="btn btn-success"> View</a>
                            </td>
                        </tr>
                    <?php
                } else {
                    // If no data is found, display "Not Data" message
                    echo "<tr> <td colspan='6'> Not Data </td> </tr>";
                }

            } else {
                // If search ID is empty, display "Not Data" message
                echo "<tr> <td colspan='6'> Not Data </td> </tr>";
            }
        }

    }

?>
