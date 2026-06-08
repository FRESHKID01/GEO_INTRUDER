<?php
// htdocs/GEO_INTRUDER/delete_evidence.php
header("Content-Type: application/json");

// Pull the exact working database connection from your includes folder
include 'includes/db.php'; 

// Verify that the connection variable exists from your includes file
if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection variable missing from includes/db.php"]);
    exit();
}

// Verify that an ID was sent via the JavaScript POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $evidence_id = intval($_POST['id']);

    // 1. Fetch the image file path before removing the log entry
    $query = "SELECT image_path FROM security_evidence WHERE id = $evidence_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $file_to_delete = $row['image_path'];

        // 2. Clear out the physical image asset from your folder paths
        if (!empty($file_to_delete) && file_exists($file_to_delete)) {
            unlink($file_to_delete); 
        } else {
            // Check inside your real alerts directory if the base path didn't hit
            $fallback_path = "alerts/" . basename($file_to_delete);
            if (file_exists($fallback_path)) {
                unlink($fallback_path);
            }
        }

        // 3. Purge the row record from the database table
        $delete_query = "DELETE FROM security_evidence WHERE id = $evidence_id";
        
        if (mysqli_query($conn, $delete_query)) {
            echo json_encode(["status" => "success", "message" => "Evidence deleted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "SQL Execution Failure: " . mysqli_error($conn)]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Target log record ID not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid POST processing parameters."]);
}

mysqli_close($conn);
?>