$('.sidebar-toggle_a').on('click', function() {

    $("body").toggleClass("sidebar-collapse").toggleClass("sidebar-open");;

});



// Function to open the sidebar navigation
function openNav() {
    // Set the sidebar width to 250px to make it visible
    document.getElementById("mySidebar").style.width = "250px";

    // Move the main content to the right by 250px
    document.getElementById("main").style.marginLeft = "250px";
    document.getElementById("main-content").style.marginLeft = "250px";

    // Hide the menu button when the sidebar is open
    document.getElementById("main").style.display = "none";
}

// Function to close the sidebar navigation
function closeNav() {
    // Collapse the sidebar by setting its width to 0
    document.getElementById("mySidebar").style.width = "0";

    // Reset the margin of the main content to align it back to the left
    document.getElementById("main").style.marginLeft = "0";

    // Show the menu button when the sidebar is closed
    document.getElementById("main").style.display = "block";
}

// Password visibility toggle feature
const eyeIcon = document.getElementById("eye"); // Get the eye icon element
const passwordField = document.getElementById("password"); // Get the password input field

// Add an event listener to toggle password visibility when clicking the eye icon
eyeIcon.addEventListener("click", () => {
    // Check if the password is currently hidden and if there's any input
    if (passwordField.type === "password" && passwordField.value) {
        passwordField.type = "text"; // Show the password
        eyeIcon.classList.remove("fa-eye"); // Change the icon from eye to eye-slash
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordField.type = "password"; // Hide the password
        eyeIcon.classList.remove("fa-eye-slash"); // Change the icon back to eye
        eyeIcon.classList.add("fa-eye");
    }
});



// Password visibility toggle feature
const eyeIcon1 = document.getElementById("eye1"); // Get the eye icon element
const passwordField1 = document.getElementById("password1"); // Get the password input field

// Add an event listener to toggle password visibility when clicking the eye icon
eyeIcon1.addEventListener("click", () => {
    // Check if the password is currently hidden and if there's any input
    if (passwordField1.type === "password" && passwordField1.value) {
        passwordField1.type = "text"; // Show the password
        eyeIcon1.classList.remove("fa-eye"); // Change the icon from eye to eye-slash
        eyeIcon1.classList.add("fa-eye-slash");
    } else {
        passwordField1.type = "password"; // Hide the password
        eyeIcon1.classList.remove("fa-eye-slash"); // Change the icon back to eye
        eyeIcon1.classList.add("fa-eye");
    }
});




// Password visibility toggle feature
const eyeIcon2 = document.getElementById("eye2"); // Get the eye icon element
const passwordField2 = document.getElementById("password2"); // Get the password input field

// Add an event listener to toggle password visibility when clicking the eye icon
eyeIcon2.addEventListener("click", () => {
    // Check if the password is currently hidden and if there's any input
    if (passwordField2.type === "password" && passwordField2.value) {
        passwordField2.type = "text"; // Show the password
        eyeIcon2.classList.remove("fa-eye"); // Change the icon from eye to eye-slash
        eyeIcon2.classList.add("fa-eye-slash");
    } else {
        passwordField2.type = "password"; // Hide the password
        eyeIcon2.classList.remove("fa-eye-slash"); // Change the icon back to eye
        eyeIcon2.classList.add("fa-eye");
    }
});