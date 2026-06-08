<?php
session_start();
include 'includes/db.php'; 

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-INTRUDER | Evidence Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Matches Dashboard Style Elements Exactly */
        body { background-color: #0b0e14; color: #fff; font-family: 'Courier New', monospace; }
        .navbar { background-color: #1a1d24; border-bottom: 1px solid #00ffcc; padding: 15px; }
        .navbar-brand { color: #00ffcc !important; font-weight: bold; letter-spacing: 2px; }
        .sidebar { background-color: #1a1d24; height: 100vh; border-right: 1px solid #333; padding: 20px; }
        .nav-link { color: #888; transition: 0.3s; margin-bottom: 10px; border-radius: 5px; text-decoration: none; display: block; padding: 10px; }
        .nav-link:hover, .nav-link.active { color: #00ffcc; background: rgba(0, 255, 204, 0.05); }
        
        /* Your Exact Gallery Grid Card Layout Details */
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding-top: 20px; }
        .card { background: #1a1d24; padding: 15px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 1px solid #333; }
        img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; border: 1px solid #ff4d4d; }
        .timestamp { font-size: 0.85em; color: #aaa; margin-top: 10px; }
    </style>
</head>
<body>

<?php
$alert_query = "SELECT * FROM protected_devices WHERE status = 'BREACHED'";
$alert_result = mysqli_query($conn, $alert_query);

if (mysqli_num_rows($alert_result) > 0) {
    echo '<div style="background: #ff4d4d; color: white; padding: 10px; font-weight: bold;">';
    echo '<marquee behavior="scroll" direction="left">';
    while($row = mysqli_fetch_assoc($alert_result)) {
        echo "⚠️ SECURITY BREACH DETECTED: Device [" . $row['device_name'] . "] is outside the Safe Zone! | MAC: " . $row['mac_address'] . " | Unauthorized Network Detected! ⚠️ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    }
    echo '</marquee></div>';
}
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GEO-INTRUDER CORE</a>
        <div class="d-flex align-items-center">
            <span class="text-secondary me-3 small">OPERATOR: <span class="text-white"><?php echo $admin_name; ?></span></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">TERMINATE SESSION</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">📊 DASHBOARD</a></li>
                <li class="nav-item"><a class="nav-link" href="enroll_device.php">➕ ENROLL ASSET</a></li>
                <li class="nav-item">
                    <a class="nav-link text-danger active" href="gallery.php">
                        <i class="fas fa-exclamation-triangle"></i> BREACH GALLERY
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">🛡️ SECURITY LOGS</a></li>
                <li class="nav-item"><a class="nav-link" href="#">⚙️ SETTINGS</a></li>
            </ul>
        </div>
        
        <div class="col-md-10 p-4">
            <h3 class="mb-2 text-danger">🚨 INTRUDER EVIDENCE LOGS</h3>
            <p class="text-secondary mb-4">Visual security confirmations captured during unauthorized physical asset drift.</p>
            
            <div class="mb-4 p-3 border rounded" style="background: #1a1d24; border-color: #333;">
                <h5 class="mb-3" style="color: #00ffcc;"><i class="fa-solid fa-screwdriver-wrench"></i> MAINTENANCE ENGINE</h5>
                <button class="btn btn-danger btn-sm me-2" onclick="purgeGallery('clear_everything')">PURGE ALL ENTRIES</button>
                <button class="btn btn-sm btn-outline-warning" onclick="purgeGallery('clear_monthly')">CLEAR OLDER THAN 30 DAYS</button>
            </div>

            <div class="gallery">
                <?php
                $sql = "SELECT * FROM security_evidence ORDER BY capture_time DESC";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) {
                        $image_path = "alerts/" . $row['image_path'];
                        $evidence_id = $row['id'];
                        
                        echo "<div class='card text-white' id='evidence-card-$evidence_id'>";
                        echo "    <img src='$image_path' alt='Intruder Evidence'>";
                        echo "    <div class='card-body px-0 pb-0'>";
                        echo "        <p class='timestamp mb-1'><strong>Captured:</strong> " . $row['capture_time'] . "</p>";
                        echo "        <p class='mb-3'><span class='badge bg-danger'>MAC: " . $row['device_mac'] . "</span></p>";
                        echo "        <button class='btn btn-sm btn-outline-danger w-100' onclick='deleteEvidenceLog($evidence_id)'>";
                        echo "            <i class='fa-solid fa-trash-can'></i> PURGE FILE";
                        echo "        </button>";
                        echo "    </div>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='text-center w-100 py-5 text-secondary'>NO SNAPSHOTS CAPTURED YET.</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
function deleteEvidenceLog(evidenceId) {
    let passwordChallenge = prompt("SECURITY CHALLENGE: Enter Admin Password to authorize file drop:");
    if (passwordChallenge === null || passwordChallenge.trim() === "") return;

    let params = "id=" + encodeURIComponent(evidenceId) + "&confirm_password=" + encodeURIComponent(passwordChallenge);

    fetch('delete_evidence.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload(); 
        } else {
            alert("ERROR: " + data.message);
        }
    })
    .catch(err => alert("Could not execute connection."));
}

function purgeGallery(action) {
    // 1. Confirm intention based on action type
    let confirmationMessage = (action === 'clear_everything') 
        ? "CRITICAL WARNING: You are about to PERMANENTLY PURGE ALL evidence entries from the database! Proceed?" 
        : "WARNING: You are about to permanently clear all evidence older than 30 days. Proceed?";
        
    if (!confirm(confirmationMessage)) return;

    // 2. Trigger the Admin Password Challenge
    let passwordChallenge = prompt("ADMIN ELEVATION REQUIRED: Enter Password to authorize mass purge:");
    if (passwordChallenge === null || passwordChallenge.trim() === "") return;

    let params = "action=" + encodeURIComponent(action) + "&confirm_password=" + encodeURIComponent(passwordChallenge);

    // 3. TARGETING YOUR EXACT FILE: delete_all_evidence.php
    fetch('delete_all_evidence.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: params
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            location.reload();
        } else {
            alert("PURGE EXCEPTION: " + data.message);
        }
    })
    .catch(err => {
        console.error("Purging Error:", err);
        alert("Failed to communicate with maintenance engine utilities.");
    });
}
</script>
</body>
</html>