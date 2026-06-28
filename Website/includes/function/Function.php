<?php 
// Initialize the user session variable
$idusersession = 0;
if (isset($_SESSION['userid'])) {
    // If session has a user ID, assign it to $idusersession
    $idusersession = $_SESSION['userid'];
} else {
    // If session does not have a user ID, set $idusersession to 0
    $idusersession = 0;
}

// Function to check if a specific item exists in a table based on a condition
function CheckItem($from, $where, $value, $type_string = 'i') {
    global $conn; // Access the global database connection

    // Prepare the SQL statement to check if the item exists based on the condition
    $statement = $conn->prepare("SELECT * FROM $from WHERE $where = ?");

    // Bind the value to the prepared statement with the specified type (default 'i' for integer)
    $statement->bind_param($type_string, $value);
    $statement->execute(); // Execute the query

    $result = $statement->get_result(); // Get the result of the query

    $count = $result->num_rows; // Get the number of rows returned (indicating if the item exists)

    return $count; // Return the count of rows (1 if exists, 0 if not)
}

// Function to retrieve an item from a table based on a condition
function GetItemWhere($table, $where, $value, $type_string = 'i') {
    global $conn; // Access the global database connection

    // Prepare the SQL statement to retrieve data based on the condition
    $statement = $conn->prepare("SELECT * FROM $table WHERE $where = ?");

    // Bind the value to the prepared statement with the specified type (default 'i' for integer)
    $statement->bind_param($type_string, $value);
    $statement->execute(); // Execute the query

    $result = $statement->get_result(); // Get the result of the query

    // Fetch the associative array of the result
    $rows = $result->fetch_assoc();

    return $rows; // Return the fetched row (result of the query)
}

// Function to retrieve an item from a table based on two conditions
function GetItemWhere2($table, $where, $value, $where2, $value2, $type_string = 'i') {
    global $conn; // Access the global database connection

    // Prepare the SQL statement to retrieve data based on two conditions
    $statement = $conn->prepare("SELECT * FROM $table WHERE $where = ? AND $where2 = ?");

    // Bind the values to the prepared statement with the specified type (default 'i' for integer)
    $statement->bind_param($type_string, $value, $value2);
    $statement->execute(); // Execute the query

    $result = $statement->get_result(); // Get the result of the query

    // Fetch the associative array of the result
    $rows = $result->fetch_assoc();

    return $rows; // Return the fetched row (result of the query)
}
