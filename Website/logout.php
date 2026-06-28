<?php 
session_start(); // Start the session to access session variables

session_unset(); // Unset all session variables

session_destroy(); // Destroy the session

header("location:index.php"); // Redirect the user to the index (login) page
exit(); // Terminate the script
?>
