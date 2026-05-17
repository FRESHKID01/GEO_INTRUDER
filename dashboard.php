<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];

$count_sql = "SELECT COUNT(*) as total FROM protected_devices WHERE admin_id = '$admin_id'";
$count_res = mysqli_query($conn, $count_sql);
$count_data = mysqli_fetch_assoc($count_res);
$total_assets = $count_data['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-intruder | Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta http-equiv="refresh" content="5"> 
<!-- This reloads the page every 5 seconds -->

    <style>
        body { background-color: #0b0e14; color: #fff; font-family: 'Courier New', monospace; }
        .navbar { background-color: #1a1d24; border-bottom: 1px solid #00ffcc; padding: 15px; }
        .navbar-brand { color: #00ffcc !important; font-weight: bold; letter-spacing: 2px; }
        .sidebar { background-color: #1a1d24; height: 100vh; border-right: 1px solid #333; padding: 20px; }
        .nav-link { color: #888; transition: 0.3s; margin-bottom: 10px; border-radius: 5px; text-decoration: none; display: block; padding: 10px; }
        .nav-link:hover, .nav-link.active { color: #00ffcc; background: rgba(0, 255, 204, 0.05); }
        .stat-card { background-color: #1a1d24; border: 1px solid #00ffcc; border-radius: 10px; padding: 20px; box-shadow: 0 0 15px rgba(0, 255, 204, 0.1); }
        .table-cyber { background-color: #1a1d24; color: #fff; border: 1px solid #333; }
        .table-cyber th { color: #00ffcc; border-bottom: 1px solid #00ffcc; }
        .table-cyber td { border-bottom: 1px solid #222; vertical-align: middle; }
        .status-secure { color: #00ffcc; font-weight: bold; text-shadow: 0 0 5px rgba(0, 255, 204, 0.5); }
        .status-breached { color: #ff4d4d; font-weight: bold; text-shadow: 0 0 5px rgba(255, 77, 77, 0.5); }
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
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">📊 DASHBOARD</a></li>
                <li class="nav-item"><a class="nav-link" href="enroll_device.php">➕ ENROLL ASSET</a></li>
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
            <h3 class="mb-4">SYSTEM OVERVIEW</h3>
            
            <div class="row mb-5">
                <div class="col-md-4">
                    <div class="stat-card">
                        <h6 class="text-secondary small">PROTECTED ASSETS</h6>
                        <h1 style="color: #00ffcc;"><?php echo $total_assets; ?></h1>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h6 class="text-secondary small">SYSTEM UPTIME</h6>
                        <h1 class="text-white">100%</h1>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <h6 class="text-secondary small">GLOBAL THREAT LEVEL</h6>
                        <h1 class="text-success">LOW</h1>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <h4 class="text-cyber mb-3">ACTIVE MONITORING LIST</h4>
                <div class="table-responsive">
                    <table class="table table-cyber">
                        <thead>
                            <tr>
                                <th>ASSET NAME</th>
                                <th>DEPARTMENT</th>
                                <th>MAC ADDRESS</th>
                                <th>STATUS</th>
                                <th>LAST CHECK-IN</th>
                                <th>ACTION</th> </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM protected_devices WHERE admin_id = '$admin_id'";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while($row = mysqli_fetch_assoc($result)) {
                                    $status_class = ($row['status'] == 'SECURE') ? 'status-secure' : 'status-breached';
                                    echo "<tr>";
                                    echo "<td>" . strtoupper($row['device_name']) . "</td>";
                                    echo "<td>" . $row['dept_location'] . "</td>";
                                    echo "<td><code>" . $row['mac_address'] . "</code></td>";
                                    echo "<td class='$status_class'>" . $row['status'] . "</td>";
                                    echo "<td class='small text-secondary'>" . $row['last_check_in'] . "</td>";
                                    // ADDED DELETE BUTTON HERE
                                    echo "<td>
                                            <a href='delete_device.php?id=" . $row['id'] . "' 
                                               class='btn btn-outline-danger btn-sm' 
                                               onclick='return confirm(\"PERMANENTLY DECOMMISSION THIS ASSET?\")'>
                                               <i class='fas fa-trash'></i>
                                            </a>
                                          </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center text-secondary py-4'>NO ASSETS ENROLLED IN GEOFENCE.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>