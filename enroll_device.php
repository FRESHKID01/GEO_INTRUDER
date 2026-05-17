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
    <style>
        body { background-color: #0b0e14; color: #fff; font-family: 'Courier New', monospace; }
        .enroll-card { background-color: #1a1d24; border: 1px solid #00ffcc; padding: 30px; border-radius: 10px; margin-top: 50px; box-shadow: 0 0 15px rgba(0, 255, 204, 0.1); }
        .form-control { background-color: #0b0e14; border: 1px solid #333; color: #00ffcc; }
        .form-control:focus { border-color: #00ffcc; color: #000000; box-shadow: none; }
        .btn-cyber { background-color: #00ffcc; color: #000; font-weight: bold; border: none; }
        .text-cyber { color: #00ffcc; }
    </style>
</head>
<body>

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

    <div class="mb-4">
        <label class="small text-secondary">HOD Alert Email</label>
        <input type="email" name="hod_email" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-cyber w-100">ACTIVATE GEOFENCE PROTECTION</button>
</form>

<script>
function autoDetect(type) {
    alert("In a production environment, this triggers the GEO-intruder local agent. For now, please enter the details manually while we build the 'Scout' script in the next phase!");
}
</script>

</body>
</html>