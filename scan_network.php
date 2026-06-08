<?php
// htdocs/GEO_INTRUDER/scan_network.php
header("Content-Type: application/json");

$target = isset($_GET['target']) ? $_GET['target'] : '';
$response = ["status" => "error", "value" => ""];

// Helper function to extract the Wired Default Gateway MAC address via ARP table
function getWiredGatewayMac() {
    // 1. Find the active Default Gateway IP address
    $netstat = shell_exec('route print 0.0.0.0');
    if (!$netstat) return null;
    
    // Look for the gateway IP in the active routes table
    preg_match_all('/\s+0\.0\.0\.0\s+0\.0\.0\.0\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})/', $netstat, $matches);
    if (empty($matches[1][0])) return null;
    
    $gateway_ip = $matches[1][0]; // Extract the IP address (e.g., 192.168.1.1)

    // 2. Query the ARP cache to find the physical MAC address bound to that IP
    $arp_table = shell_exec('arp -a ' . $gateway_ip);
    if (!$arp_table) return null;

    // Regex to match the MAC address format (e.g., aa-bb-cc-dd-ee-ff) next to the IP
    preg_match('/([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})/', $arp_table, $arp_matches);
    
    if (!empty($arp_matches[0])) {
        return strtoupper(str_replace('-', ':', $arp_matches[0]));
    }
    return null;
}

// ==========================================
// TARGET 1: DETECT DEVICE HARDWARE MAC
// ==========================================
if ($target === 'mac') {
    $output = shell_exec('netsh wlan show interfaces');
    // Check if the wireless interface card is active and has a MAC
    preg_match('/Physical address\s*:\s*([0-9a-fA-F:]{17})/', $output, $matches);
    
    if (!empty($matches[1])) {
        $response = ["status" => "success", "value" => strtoupper($matches[1])];
    } else {
        // Fallback: If Wi-Fi is disabled, look at the active wired Ethernet interface MAC
        $fallback = shell_exec('getmac /v /fo csv');
        if ($fallback) {
            // Find the non-wireless network adapter that is active
            $lines = explode("\n", $fallback);
            foreach ($lines as $line) {
                if (stripos($line, 'Ethernet') !== false && preg_match('/([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})/', $line, $mac_match)) {
                    $response = ["status" => "success", "value" => strtoupper(str_replace('-', ':', $mac_match[0]))];
                    break;
                }
            }
        }
    }
}

// ==========================================
// TARGET 2: DETECT NETWORK ANCHOR TOKEN
// ==========================================
elseif ($target === 'bssid') {
    $output = shell_exec('netsh wlan show interfaces');
    preg_match('/BSSID\s*:\s*([0-9a-fA-F:]{17})/', $output, $matches);
    
    if (!empty($matches[1])) {
        // Mode A: Machine is actively on Wi-Fi
        $response = ["status" => "success", "value" => strtoupper($matches[1])];
    } else {
        // Mode B: No Wi-Fi found! Attempt to fallback to the Wired Network Router Gateway MAC
        $wired_gateway = getWiredGatewayMac();
        if ($wired_gateway) {
            $response = ["status" => "success", "value" => $wired_gateway];
        } else {
            $response = ["status" => "error", "value" => "No active Wi-Fi BSSID or Ethernet Gateway connection found."];
        }
    }
}

echo json_encode($response);
?>