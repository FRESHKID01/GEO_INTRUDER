<?php
session_start(); // Start the session to track the user
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Search for the admin by email
    $sql = "SELECT * FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        // Check if the password matches the hashed version in the DB
        if (password_verify($password, $row['password'])) {
            // SUCCESS: Set session variables
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['full_name'];
            
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "Invalid Password.";
        }
    } else {
        echo "Admin not found.";
    }
}
?>