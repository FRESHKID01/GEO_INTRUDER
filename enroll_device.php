<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-intruder | Enroll Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #0b0e14; color: #fff; font-family: 'Courier New', monospace; }
        .navbar { background-color: #1a1d24; border-bottom: 1px solid #00ffcc; padding: 15px; }
        .navbar-brand { color: #00ffcc !important; font-weight: bold; letter-spacing: 2px; }
        .sidebar { background-color: #1a1d24; height: 100vh; border-right: 1px solid #333; padding: 20px; }
        .nav-link { color: #888; transition: 0.3s; margin-bottom: 10px; border-radius: 5px; text-decoration: none; display: block; padding: 10px; }
        .nav-link:hover, .nav-link.active { color: #00ffcc; background: rgba(0, 255, 204, 0.05); }
        .enroll-card { background-color: #1a1d24; border: 1px solid #00ffcc; padding: 30px; border-radius: 10px; margin-top: 50px; box-shadow: 0 0 15px rgba(0, 255, 204, 0.1); }
        .form-control { background-color: #0b0e14; border: 1px solid #333; color: #00ffcc; }
        .form-control:focus { border-color: #00ffcc; color: #000000; box-shadow: none; }
        .btn-cyber { background-color: #00ffcc; color: #000; font-weight: bold; border: none; }
        .text-cyber { color: #00ffcc; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">GEO-INTRUDER CORE</a>
        <div class="d-flex align-items-center">
            <span class="text-secondary me-3 small">OPERATOR: <span class="text-white"><?php echo $_SESSION['admin_name']; ?></span></span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">TERMINATE SESSION</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">📊 DASHBOARD</a></li>
                <li class="nav-item"><a class="nav-link active" href="enroll_device.php">➕ ENROLL ASSET</a></li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="gallery.php">
                        <i class="fas fa-exclamation-triangle"></i> BREACH GALLERY
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#">🛡️ SECURITY LOGS</a></li>
                <li class="nav-item"><a class="nav-link" href="#">⚙️ SETTINGS</a></li>
            </ul>
        </div>
        
        <div class="col-md-10 p-4">
            <div class="enroll-card mt-2">
                <h3 class="mb-4 text-cyber">PROVISION NEW GEOTAG PROFILE</h3>
                
                <form action="process_enroll.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-secondary">Device Name</label>
                            <input type="text" name="device_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-secondary">Department/Location</label>
                            <input type="text" name="dept_location" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="small text-secondary">Device MAC Address</label>
                            <div class="input-group">
                                <input type="text" id="mac_field" name="mac_address" class="form-control" placeholder="00-00-00-00-00-00" required>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="autoDetect('mac')">SCAN</button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="small text-secondary">Authorized BSSID</label>
                            <div class="input-group">
                                <input type="text" id="bssid_field" name="authorized_bssid" class="form-control" placeholder="00:00:00:00:00:00" required>
                                <button type="button" class="btn btn-outline-info btn-sm" onclick="autoDetect('bssid')">SCAN</button>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="small text-secondary">Admin Alert Email</label>
                            <input type="email" name="admin_alert_email" class="form-control" placeholder="admin@domain.com" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="small text-secondary">HOD Alert Email</label>
                            <input type="email" name="hod_alert_email" class="form-control" placeholder="hod.cyber@domain.com" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-cyber w-100">ACTIVATE GEOFENCE PROTECTION</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function autoDetect(type) {
    alert("In a production environment, this triggers the GEO-intruder local agent. For now, please enter the details manually while we build the 'Scout' script in the next phase!");
}
</script>
<script>
function autoDetect(targetType) {
    let targetField = (targetType === 'mac') ? document.getElementById('mac_field') : document.getElementById('bssid_field');
    
    targetField.value = "Scanning local interfaces...";
    targetField.style.color = "#0dcaf0"; 

    fetch('scan_network.php?target=' + targetType)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                targetField.value = data.value;
                targetField.style.color = "#ffffff"; 
            } else {
                alert("Scan Alert: " + data.value);
                targetField.value = "";
                targetField.placeholder = (targetType === 'mac') ? "00:00:00:00:00:00" : "00:00:00:00:00:00";
            }
        })
        .catch(err => {
            console.error("Scanning Error:", err);
            alert("Failed to communicate with local scanning utilities.");
            targetField.value = "";
        });
}
</script>
</body>
</html>