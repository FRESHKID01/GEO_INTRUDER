<?php
include 'includes/db.php'; // Ensure this points to your DB connection file

// Check if the device is sending its MAC address
if (isset($_POST['mac'])) {
    $mac = mysqli_real_escape_string($conn, $_POST['mac']);
    $current_bssid = isset($_POST['bssid']) ? mysqli_real_escape_string($conn, $_POST['bssid']) : "NULL";

    // 1. Fetch device details from the database
    $query = "SELECT * FROM protected_devices WHERE mac_address = '$mac'";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // 2. ALTERNATIVE BSSID LOGIC
        // Turn the comma-separated list into an array
        $allowed_bssids = explode(',', str_replace(' ', '', $row['authorized_bssid']));
        
        // Check if the current BSSID is in the allowed list
        if ($current_bssid !== "NULL" && in_array($current_bssid, $allowed_bssids)) {
            $new_status = 'SECURE';
        } else {
            $new_status = 'BREACHED';
        }

        // 3. UPDATE MAIN STATUS
        mysqli_query($conn, "UPDATE protected_devices SET status='$new_status', last_check_in=NOW() WHERE mac_address='$mac'");

        // 4. FORENSIC LOGGING (Requirement: Logging out the BSSID)
        mysqli_query($conn, "INSERT INTO security_logs (device_mac, bssid_detected, status_at_time) 
                            VALUES ('$mac', '$current_bssid', '$new_status')");

        // 5. CAMERA MONITORING (Handle Image Upload)
        if ($new_status == 'BREACHED' && isset($_FILES['evidence'])) {
            $target_dir = "alerts/";
            $file_extension = pathinfo($_FILES["evidence"]["name"], PATHINFO_EXTENSION);
            $new_filename = $mac . "_" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["evidence"]["tmp_name"], $target_file)) {
                // Save the image path to the evidence table
                mysqli_query($conn, "INSERT INTO security_evidence (device_mac, image_path) 
                                    VALUES ('$mac', '$new_filename')");
                
                // Note: This is where you would call your Email Function to send the photo
                // sendAlertEmail($row['admin_email'], $target_file);
            }
        }

        echo "SUCCESS: " . $new_status;
    } else {
        echo "ERROR: Device not enrolled.";
    }
} else {
    echo "ERROR: Missing parameters.";
}
?>