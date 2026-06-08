<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer core files (installed via Composer or manual include)
require 'vendor/autoload.php'; 
include 'includes/db.php';

function dispatchCorporateAlert($breachData, $conn) {
    $mail = new PHPMailer(true);

    try {
        // ==========================================
        // 1. CORPORATE SMTP SERVER CONFIGURATION
        // ==========================================
        $mail->isSMTP();
        $mail->Host       = 'smtp.corporate-relay.com'; // Company SMTP Server (e.g., AWS SES / SendGrid)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'api-key-or-sys-email@domain.com';
        $mail->Password   = 'secure-smtp-token-or-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Mandate TLS encryption
        $mail->Port       = 587;

        // ==========================================
        // 2. RECIPIENT ROUTING (Dual Broadcast)
        // ==========================================
        $mail->setFrom('geo-intruder.core@company.com', 'GEO-INTRUDER MONITOR');
        $mail->addAddress($breachData['admin_alert_email']); // Target 1: IT Admin
        $mail->addAddress($breachData['hod_alert_email']);   // Target 2: Department Head

        // ==========================================
        // 3. INLINE IMAGE EMBEDDING (Outlook Bypass)
        // ==========================================
        // This embeds the physical image file directly into the email stream 
        // and names it 'intruder_snap' so the HTML can call it securely.
        $local_image_path = "alerts/" . $breachData['image_path'];
        $mail->addEmbeddedImage($local_image_path, 'intruder_snap', 'evidence.jpg');

        // ==========================================
        // 4. ENTERPRISE HTML PAYLOAD BUILD
        // ==========================================
        $mail->isHTML(true);
        $mail->Subject = "🚨 CRITICAL SECURITY BREACH: [" . strtoupper($breachData['device_name']) . "]";
        
        $mail->Body = "
        <body style='background-color: #0b0e14; color: #ffffff; font-family: monospace; padding: 20px;'>
            <div style='border: 2px solid #ff4d4d; padding: 25px; background-color: #1a1d24; border-radius: 8px;'>
                <h2 style='color: #ff4d4d; margin-top: 0;'>⚠️ GEOFENCE LOOP VIOLATION</h2>
                <p style='color: #888;'>An authenticated hardware asset has breached its physical link-layer boundary limits.</p>
                <hr style='border-color: #333;'>
                
                <table style='color: #fff; width: 100%; font-size: 14px; border-spacing: 0 8px;'>
                    <tr><td style='width: 200px; color: #888;'>ASSET IDENTITY:</td><td><strong style='color: #00ffcc;'>" . strtoupper($breachData['device_name']) . "</strong></td></tr>
                    <tr><td style='color: #888;'>OPERATIONAL HUB:</td><td>" . $breachData['dept_location'] . "</td></tr>
                    <tr><td style='color: #888;'>HARDWARE MAC:</td><td><code>" . $breachData['mac_address'] . "</code></td></tr>
                    <tr><td style='color: #888;'>UNAUTHORIZED BSSID:</td><td><span style='color: #ff4d4d;'>" . $breachData['current_bssid'] . "</span></td></tr>
                    <tr><td style='color: #888;'>DETECTION TIMESTAMP:</td><td>" . date('Y-m-d H:i:s') . "</td></tr>
                </table>
                
                <hr style='border-color: #333;'>
                <h4 style='color: #00ffcc; margin-bottom: 12px;'>📸 PRIMARY FORENSIC EVIDENCE</h4>
                <div style='text-align: center; background: #0b0e14; padding: 15px; border: 1px solid #ff4d4d; border-radius: 6px;'>
                    <img src='cid:intruder_snap' alt='Intruder Snapshot' style='max-width: 100%; height: auto; border-radius: 4px;'>
                </div>
                
                <p style='font-size: 11px; color: #555; margin-top: 25px; text-align: center; letter-spacing: 1px;'>
                    SECURE INSTANCE RUNNING • GEO-INTRUDER GLOBAL PROTECTION FRAMEWORK
                </p>
            </div>
        </body>";

        // ==========================================
        // 5. DISPATCH AND AUDIT TRAIL LOGGING
        // ==========================================
        $mail->send();
        
        // Log clean delivery state to security logs
        $log_msg = "Breach email successfully dispatched to Admin and HOD for device: " . $breachData['device_name'];
        mysqli_query($conn, "INSERT INTO security_logs (event_type, description, severity) VALUES ('MAIL_DISPATCH', '$log_msg', 'INFO')");
        return true;

    } catch (Exception $e) {
        // Log exact network failure message so corporate teams can diagnose the SMTP issue
        $err_msg = "Mail failure: " . $mail->ErrorInfo;
        mysqli_query($conn, "INSERT INTO security_logs (event_type, description, severity) VALUES ('MAIL_EXCEPTION', '$err_msg', 'CRITICAL')");
        return false;
    }
}
?>