<?php
session_start();
include 'includes/db.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = $_SESSION['admin_id']; 
    
    // Collect and sanitize form data
    $device_name = mysqli_real_escape_string($conn, $_POST['device_name']);
    $dept        = mysqli_real_escape_string($conn, $_POST['dept_location']);
    $mac         = mysqli_real_escape_string($conn, $_POST['mac_address']);
    $bssid       = mysqli_real_escape_string($conn, $_POST['authorized_bssid']);
    $hod_email   = mysqli_real_escape_string($conn, $_POST['hod_email']);
    
    // Get admin email for alerts
    $sql_admin  = "SELECT email FROM admins WHERE id = '$admin_id'";
    $res        = mysqli_query($conn, $sql_admin);
    $admin_data = mysqli_fetch_assoc($res);
    $admin_email = $admin_data['email'];

    // Insert into protected_devices
    $sql = "INSERT INTO protected_devices (admin_id, device_name, dept_location, mac_address, authorized_bssid, admin_alert_email, hod_alert_email, status) 
            VALUES ('$admin_id', '$device_name', '$dept', '$mac', '$bssid', '$admin_email', '$hod_email', 'SECURE')
            ON DUPLICATE KEY UPDATE 
            device_name = '$device_name', 
            authorized_bssid = '$bssid',
            status = 'SECURE'";

    if (mysqli_query($conn, $sql)) {
        // SUCCESS: Redirect to dashboard
        header("Location: dashboard.php?enroll=success");
        exit();
    } else {
        // ERROR: Show the exact SQL error so we can fix it
        die("Database Error: " . mysqli_error($conn));
    }
}
?>