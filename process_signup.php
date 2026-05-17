<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $org = mysqli_real_escape_string($conn, $_POST['organization']);
    $dept = mysqli_real_escape_string($conn, $_POST['department']);
    $password = $_POST['password'];

    // 1. Check if email already exists
    $checkEmail = "SELECT email FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (mysqli_num_rows($result) > 0) {
        // Redirect back with an error message
        header("Location: signup.php?error=email_exists");
        exit();
    }

    // 2. Validate Password (8 digits and numerical only)
    if (!preg_match('/^[0-9]{8}$/', $password)) {
        header("Location: signup.php?error=invalid_password");
        exit();
    }

    // 3. If all clear, hash and insert
    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (full_name, email, password, organization, department) 
            VALUES ('$name', '$email', '$hashed_pass', '$org', '$dept')";

    if (mysqli_query($conn, $sql)) {
        header("Location: login.php?success=account_created");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>