<?php
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "SELECT id FROM admins WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // In a real system, you'd send an email here.
        // For now, we redirect to the reset page with the user ID.
        header("Location: reset_password.php?uid=" . $row['id']);
    } else {
        header("Location: forgot_password.php?msg=not_found");
    }
}
?>