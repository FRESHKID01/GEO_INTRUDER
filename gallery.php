<?php
// Points to your database connection in the includes folder
include 'includes/db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GEO-INTRUDER | Evidence Gallery</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <style>
        body { font-family: sans-serif; background: #121212; color: #ffffff; padding: 20px; }
        .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding-top: 20px; }
        .card { background: #1e1e1e; padding: 15px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); border: 1px solid #2d2d2d; }
        img { width: 100%; height: 200px; object-fit: cover; border-radius: 5px; border: 1px solid #333; }
        .timestamp { font-size: 0.85em; color: #aaa; margin-top: 10px; }
    </style>
</head>
<body>
    <h1>🚨 Intruder Evidence Gallery</h1>
    <p class="text-secondary">Below are snapshots captured during unauthorized movements.</p>
    <hr style="border-color: #333;">
    
    <div class="mb-4 p-3 border rounded" style="background: #1e1e1e; border-color: #2d2d2d;">
    <h5 class="text-warning mb-3"><i class="fa-solid fa-screwdriver-wrench"></i> Administrative Maintenance Tools</h5>
    
    <button class="btn btn-danger me-2" onclick="purgeGallery('clear_everything')">
        <i class="fa-solid fa-dumpster-fire"></i> Purge Entire Gallery
    </button>
    
    <button class="btn btn-outline-warning" onclick="purgeGallery('clear_monthly')">
        <i class="fa-solid fa-calendar-minus"></i> Clear Logs Older Than 30 Days
    </button>
</div>

    <div class="gallery">
        <?php
        // Fetching records from your 'security_evidence' table
        $sql = "SELECT * FROM security_evidence ORDER BY capture_time DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                // Ensure this folder structure matches where your python client/backend saves images
                $image_path = "alerts/" . $row['image_path'];
                $evidence_id = $row['id'];
                
                // EVERY CARD NOW GENERATES ITS OWN INDIVIDUAL DELETE BUTTON INSIDE THE LOOP
                echo "<div class='card text-white' id='evidence-card-$evidence_id'>";
                echo "    <img src='$image_path' alt='Intruder Evidence'>";
                echo "    <div class='card-body px-0 pb-0'>";
                echo "        <p class='timestamp mb-1'><strong>Captured:</strong> " . $row['capture_time'] . "</p>";
                echo "        <p class='mb-3'><span class='badge bg-danger'>DEVICE: " . $row['device_mac'] . "</span></p>";
                
                // The Delete Button passing the specific record ID dynamically
                echo "        <button class='btn btn-sm btn-outline-danger w-100' onclick='deleteEvidence($evidence_id)'>";
                echo "            <i class='fa-solid fa-trash-can'></i> Delete Image";
                echo "        </button>";
                
                echo "    </div>";
                echo "</div>";
            }
        } else {
            echo "<div class='text-center w-100 py-5 text-secondary'>
                    <i class='fa-solid fa-camera-retro fa-3x mb-3'></i>
                    <p>NO SNAPSHOTS CAPTURED YET.</p>
                  </div>";
        }
        ?>
    </div>

    <script>
   function deleteEvidence(evidenceId) {
    if (!confirm("Are you sure you want to permanently delete this forensic evidence?")) {
        return;
    }

    let formData = new FormData();
    formData.append("id", evidenceId);

    // Using a clean relative path to match your GEO_INTRUDER folder setup
    fetch("delete_evidence.php", {
        method: "POST",
        body: formData
    })
    .then(async response => {
        // If the server returns a bad status code (like 404 or 500)
        if (!response.ok) {
            let errText = await response.text();
            throw new Error("HTTP Status " + response.status + " - " + errText);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            let targetCard = document.getElementById("evidence-card-" + evidenceId);
            if (targetCard) {
                targetCard.style.transition = "all 0.4s ease";
                targetCard.style.opacity = "0";
                targetCard.style.transform = "scale(0.8)";
                setTimeout(() => { targetCard.remove(); }, 400);
            }
        } else {
            alert("Server Error: " + data.message);
        }
    })
    .catch(error => {
        // This will now pop up and tell us the REAL underlying issue
        console.error("Detailed Error Context:", error);
        alert("System Diagnosis Error:\n" + error.message);
    });
}

function purgeGallery(actionType) {
    let confirmationMessage = "Are you sure you want to permanently clear the entire logs and media vault?";
    
    if (actionType === 'clear_monthly') {
        confirmationMessage = "Are you sure you want to optimize storage and delete all logs older than 30 days?";
    }

    if (!confirm(confirmationMessage)) {
        return;
    }

    let formData = new FormData();
    formData.append("action", actionType);

    fetch("delete_all_evidence.php", {
        method: "POST",
        body: formData
    })
    .then(async response => {
        if (!response.ok) {
            let errText = await response.text();
            throw new Error("HTTP Status " + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            alert(data.message);
            // Refresh the page automatically to show the completely clean, empty vault
            window.location.reload(); 
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => {
        console.error("Maintenance connection fault:", error);
        alert("Could not process maintenance command.");
    });
}
    </script>
</body>
</html>