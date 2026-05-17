<?php
include 'includes/db.php'; // Fixed path to match your project structure

if (isset($_GET['id'])) {
    // Now $conn will be recognized because includes/db.php is loaded
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 1. Get the MAC address first
    $find_device = mysqli_query($conn, "SELECT mac_address FROM protected_devices WHERE id = '$id'");
    $device = mysqli_fetch_assoc($find_device);
    
    if ($device) {
        $mac = $device['mac_address'];

        // 2. Delete the device
        $delete_query = "DELETE FROM protected_devices WHERE id = '$id'";
        
        if (mysqli_query($conn, $delete_query)) {
            // Success: Go back to dashboard
            header("Location: dashboard.php?msg=Device Decommissioned");
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Device not found.";
    }
} else {
    echo "No ID provided.";
}
?>