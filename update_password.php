<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['user_id'];
    $new_pass = password_hash($_POST['new_pass'], PASSWORD_DEFAULT);

    $sql = "UPDATE admins SET password = '$new_pass' WHERE id = '$uid'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Password Updated Successfully!'); window.location='login.php';</script>";
    } else {
        echo "Error updating password.";
    }
}
?>