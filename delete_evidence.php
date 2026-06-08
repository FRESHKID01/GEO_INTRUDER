<?php
// htdocs/GEO_INTRUDER/delete_evidence.php
session_start(); 
header("Content-Type: application/json");

include 'includes/db.php'; 

if (!isset($conn) || !$conn) {
    echo json_encode(["status" => "error", "message" => "Database connection missing."]);
    exit();
}

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access. Please log in."]);
    exit();
}

// Read the incoming standard POST variables
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['confirm_password'])) {
    $evidence_id = intval($_POST['id']);
    $entered_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $admin_id = $_SESSION['admin_id'];

    // 1. Get the admin's true password hash
    $admin_query = "SELECT password FROM admins WHERE id = $admin_id LIMIT 1";
    $admin_result = mysqli_query($conn, $admin_query);

    if ($admin_result && mysqli_num_rows($admin_result) > 0) {
        $admin_row = mysqli_fetch_assoc($admin_result);
        $hashed_password = $admin_row['password'];

        // 2. Check the password
        if (!password_verify($entered_password, $hashed_password)) {
            echo json_encode(["status" => "error", "message" => "Incorrect admin password! Access Denied."]);
            exit();
        }

        // 3. Password is correct! Find and delete the file
        $query = "SELECT image_path FROM security_evidence WHERE id = $evidence_id";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $file_to_delete = $row['image_path'];

            if (!empty($file_to_delete) && file_exists($file_to_delete)) {
                unlink($file_to_delete); 
            } else {
                $fallback_path = "alerts/" . basename($file_to_delete);
                if (file_exists($fallback_path)) {
                    unlink($fallback_path);
                }
            }

            // 4. Delete row from database
            $delete_query = "DELETE FROM security_evidence WHERE id = $evidence_id";
            if (mysqli_query($conn, $delete_query)) {
                echo json_encode(["status" => "success", "message" => "Evidence deleted successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Database deletion failed."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Evidence record not found."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Admin account not found."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Server Error: Missing ID or password key."]);
}

mysqli_close($conn);
?>