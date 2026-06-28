<?php
// Database connection settings
$host = "localhost";  // Host where the database is running (default: localhost)
$user = "root";       // Database username (default: root in XAMPP)
$pass = "";           // Database password (default: empty in XAMPP)
$db_name = "smartroadsguard3";  // Name of the database to connect to

// Create a connection to the MySQL database
$conn = new mysqli($host, $user, $pass, $db_name);

// Check if the connection was successful
if(!$conn) {
    // If the connection fails, display an error message and terminate the script
    die("Connection Failed:".mysqli_connect_error());
}

// Enable SSL connection using options
$conn->options(MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);// Verify server certificate
//By enabling this option, it ensures that the connection will be encrypted via SSL, and the server will not be connected if the certificate is not trusted or missing.
//MYSQLI_OPT_SSL_VERIFY_SERVER_CERT:




//Check connection
if ($conn->connect_error) {
    die('connection faild ' . $conn->connect_error);
}

?>
