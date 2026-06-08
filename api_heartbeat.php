<?php
// htdocs/GEO_INTRUDER/api_heartbeat.php
header("Content-Type: application/json");

// Suppress raw HTML error output to protect the Python parser pipelines
error_reporting(0);
ini_set('display_errors', 0);

// Use a strict, non-relative anchor root path to find your DB configuration safely
$db_file = __DIR__ . '/includes/db.php';

if (!file_exists($db_file)) {
    echo json_encode(["status" => "error", "message" => "Critical Path Failure: includes/db.php not found at expected node location."]);
    exit();
}

include $db_file;

if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Database link channel configuration drop structural fault."]);
    exit();
}

// Read incoming tracking parameters
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mac_address']) && isset($_POST['current_bssid'])) {
    
    $mac = mysqli_real_escape_string($conn, $_POST['mac_address']);
    $current_bssid = mysqli_real_escape_string($conn, $_POST['current_bssid']);

    // Query your exact table schema target: protected_devices
    $check_query = "SELECT * FROM protected_devices WHERE mac_address = '$mac'";
    $result = mysqli_query($conn, $check_query);

    if ($result && mysqli_num_rows($result) > 0) {
        $device = mysqli_fetch_assoc($result);
        $authorized_bssid = $device['authorized_bssid'];

        // Evaluate location bounds signature integrity
        if (strcasecmp($current_bssid, $authorized_bssid) === 0) {
            mysqli_query($conn, "UPDATE protected_devices SET status = 'SECURE', last_message = 'Heartbeat clear.' WHERE mac_address = '$mac'");
            echo json_encode(["status" => "secure", "message" => "Device verify success. Baseline tracking structural integrity maintained."]);
        } else {
            mysqli_query($conn, "UPDATE protected_devices SET status = 'BREACHED', last_message = 'Geofence Boundary Broken!' WHERE mac_address = '$mac'");
            
            // Check for incoming security camera payload uploads
            if (isset($_FILES['intruder_img'])) {
                $target_dir = "alerts/";
                
                // Safety checkpoint: Ensure folder array structural node target element layout is online
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $file_name = "breach_" . time() . "_" . uniqid() . ".jpg";
                $target_file = $target_dir . $file_name;
                
                if (move_uploaded_file($_FILES['intruder_img']['tmp_name'], $target_file)) {
                    $log_query = "INSERT INTO security_evidence (device_mac, image_path, capture_time) VALUES ('$mac', '$file_name', NOW())";
                    mysqli_query($conn, $log_query);
                }
            }
            
            echo json_encode(["status" => "breach", "message" => "CRITICAL: Geofence violation tracking threshold triggered! Deploying countermeasure capture loops."]);
        }
    } else {
        echo json_encode(["status" => "unregistered", "message" => "Device signature fingerprint target matching constraints failed. Enroll machine inside management console panel."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid communication request constraints or parameter layout validation drop."]);
}

mysqli_close($conn);
?>