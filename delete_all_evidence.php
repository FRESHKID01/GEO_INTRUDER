<?php
// htdocs/GEO_INTRUDER/delete_all_evidence.php
header("Content-Type: application/json");
include 'includes/db.php'; 

if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $query = "";

    // Determine if we are deleting EVERYTHING or just data older than 30 days
    if ($action === 'clear_everything') {
        $query = "SELECT image_path FROM security_evidence";
    } elseif ($action === 'clear_monthly') {
        // Fetches records where capture_time is older than 30 days
        $query = "SELECT image_path FROM security_evidence WHERE capture_time < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid action parameter"]);
        exit();
    }

    $result = mysqli_query($conn, $query);

    // 1. Loop through all matching records and physically erase files
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $file_to_delete = $row['image_path'];
            
            if (!empty($file_to_delete) && file_exists($file_to_delete)) {
                unlink($file_to_delete);
            } else {
                $fallback_path = "alerts/" . basename($file_to_delete);
                if (file_exists($fallback_path)) {
                    unlink($fallback_path);
                }
            }
        }
    }

    // 2. Run the SQL DELETE query to clear the database records
    if ($action === 'clear_everything') {
        $delete_query = "TRUNCATE TABLE security_evidence"; // Clears the entire table cleanly
    } else {
        $delete_query = "DELETE FROM security_evidence WHERE capture_time < DATE_SUB(NOW(), INTERVAL 30 DAY)";
    }

    if (mysqli_query($conn, $delete_query)) {
        echo json_encode(["status" => "success", "message" => "Maintenance cleanup complete!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "SQL error running table cleanup"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid parameters"]);
}

mysqli_close($conn);
?>