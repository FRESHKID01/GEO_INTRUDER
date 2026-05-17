<?php
// Points to your database connection in the includes folder
include 'includes/db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-INTRUDER | Evidence Gallery</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .card { background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        img { width: 100%; height: auto; border-radius: 5px; border: 1px solid #ddd; }
        .timestamp { font-size: 0.8em; color: #666; margin-top: 10px; }
        .breach-tag { color: #d9534f; font-weight: bold; }
    </style>
</head>
<body>
    <h1>🚨 Intruder Evidence Gallery</h1>
    <p>Below are snapshots captured during unauthorized movements.</p>
    <hr>
    <div class="gallery">
        <?php
        // Fetching records from your 'scans' table
        // 1. Correct the table name to security_evidence
$sql = "SELECT * FROM security_evidence ORDER BY capture_time DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        // 2. Make sure the path matches your alerts folder
        $image_path = "alerts/" . $row['image_path'];
        
        echo "<div class='col-md-4 mb-4'>";
        echo "  <div class='card shadow-sm'>";
        echo "    <img src='$image_path' class='card-img-top' alt='Intruder Evidence'>";
        echo "    <div class='card-body'>";
        echo "      <p class='card-text small'>Captured: " . $row['capture_time'] . "</p>";
        echo "      <p class='card-text badge bg-danger'>DEVICE: " . $row['device_mac'] . "</p>";
        echo "    </div>";
        echo "  </div>";
        echo "</div>";
    }
} else {
    echo "<div class='col-12 text-center py-5'>
            <i class='fas fa-camera-retro fa-3x text-secondary mb-3'></i>
            <p class='text-secondary'>NO SNAPSHOTS CAPTURED YET.</p>
          </div>";
}
        ?>
    </div>
</body>
</html>