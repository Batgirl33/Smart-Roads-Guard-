<?php 
// PHP block – currently empty, can be used for backend logic if needed in the future
?>

<!DOCTYPE html>
<html>
<head>
  <!-- Page title shown in the browser tab -->
  <title>Alerts</title>

  <!-- Favicon for the tab icon -->
  <link rel="icon" type="image/png" href="img/logo2.png">

  <!-- Meta tag to ensure responsive design on mobile devices -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Alternative favicon format -->
  <link rel="icon" href="logo2.png" type="image/x-icon" />

  <!-- Link to external CSS stylesheet -->
  <link rel="stylesheet" href="style.css">
</head>

<style>
/* Inline CSS styles for the page layout and design */
body {
  font-family: Arial, sans-serif;
  text-align: center;
  margin: 0;
  padding: 0;
  background-color: #f9f9f9;
}
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background-color: #ffffff;
  border-bottom: 2px solid #ddd;
}
.logo {
  height: 60px;
}
.login-buttons {
  display: flex;
  gap: 10px;
}
.btn {
  padding: 10px 15px;
  border: none;
  background-color: #007bff;
  color: white;
  font-size: 16px;
  cursor: pointer;
  border-radius: 5px;
  text-decoration: none;
}
.btn:hover {
  background-color: #0056b3;
}
main {
  margin-top: 50px;
}
h1 {
  font-size: 24px;
  color: #333;
}
p {
  font-size: 18px;
  color: #666;
}
.features {
  display: flex;
  justify-content: center;
  gap: 50px;
  margin-top: 30px;
}
.feature {
  text-align: center;
  max-width: 300px;
}
.feature img {
  width: 50px;
  height: 50px;
}
.feature h3 {
  font-size: 20px;
  color: #333;
}
.feature p {
  font-size: 16px;
  color: #666;
}
</style>

<body>

<!-- Header section with logo and login buttons -->
<header>
    <!-- Website logo -->
    <img src="img/logo2.png" alt="Smart Roads Guard Logo" class="logo">
    <!-- Login buttons for different user types -->
    <div class="login-buttons">
        <a href="logIn.php?p=admin" class="btn">Administrator</a>
        <a href="logIn.php?p=service" class="btn">Service Department</a>
    </div>
</header>

<!-- Main content section -->
<main>
    <!-- Welcome message -->
    <h1>WELCOME In Smart Roads Guard System</h1>
    <p>Designed to enhance safety and efficiency in roads.</p>

    <!-- Features section showing benefits of the system -->
    <div class="features">
        <!-- Feature 1: Advanced Detection -->
        <div class="feature">
            <img src="img/logo2.png" alt="Advanced Detection">
            <h3>Advanced Detection</h3>
            <p>Our system detects obstacles with precision using advanced devices.</p>
        </div>
        <!-- Feature 2: Real-time Alerts -->
        <div class="feature">
            <img src="img/logo2.png" alt="Real-time Alerts">
            <h3>Real-time Alerts</h3>
            <p>Receive instant notifications to take timely actions and ensure safety.</p>
        </div>
    </div>
</main>

</body>
</html>
