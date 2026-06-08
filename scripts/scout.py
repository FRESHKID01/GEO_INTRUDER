import time
import uuid
import subprocess
import re
import requests
import cv2
import os

# Configuration
SERVER_URL = "http://localhost/GEO_INTRUDER/api_heartbeat.php"

# Storage Configuration (Hidden folder trick)
HIDDEN_DIR = "C:/Windows/Temp/SystemDiagnosticLogs/"
if not os.path.exists(HIDDEN_DIR):
    try:
        os.makedirs(HIDDEN_DIR)
    except:
        HIDDEN_DIR = "./" # Fallback to current folder if permissions deny Windows directory

# Tracking variables for the adaptive timer
breach_start_time = None  # Tracks when the breach first started

def get_network_telemetry():
    """Analyzes system interfaces to return matching MAC and Location token."""
    try:
        wlan_data = subprocess.check_output(['netsh', 'wlan', 'show', 'interfaces']).decode('utf-8', errors='ignore')
        bssid_match = re.search(r'BSSID\s*:\s*([0-9a-fA-F:]{17})', wlan_data)
        mac_match = re.search(r'Physical address\s*:\s*([0-9a-fA-F:]{17})', wlan_data)
        if bssid_match and mac_match:
            return mac_match.group(1).upper(), bssid_match.group(1).upper(), "WIRELESS"
    except Exception:
        pass

    try:
        getmac_data = subprocess.check_output(['getmac', '/v', '/fo', 'csv']).decode('utf-8', errors='ignore')
        wired_mac = None
        for line in getmac_data.split('\n'):
            if "Ethernet" in line and not "Disconnected" in line:
                mac_find = re.search(r'([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})', line)
                if mac_find:
                    wired_mac = mac_find.group(0).replace('-', ':').upper()
                    break
        
        route_data = subprocess.check_output(['route', 'print', '0.0.0.0']).decode('utf-8', errors='ignore')
        gateway_ip_match = re.search(r'\s+0\.0\.0\.0\s+0\.0\.0\.0\s+(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})', route_data)
        
        if gateway_ip_match and wired_mac:
            gateway_ip = gateway_ip_match.group(1)
            arp_data = subprocess.check_output(['arp', '-a', gateway_ip]).decode('utf-8', errors='ignore')
            gateway_mac_match = re.search(r'([0-9A-Fa-f]{2}[-]){5}([0-9A-Fa-f]{2})', arp_data)
            if gateway_mac_match:
                return wired_mac, gateway_mac_match.group(0).replace('-', ':').upper(), "WIRED"
    except Exception:
        pass

    fallback_mac = ":".join(hex(uuid.getnode())[2:].zfill(12)[i:i+2] for i in range(0, 12, 2)).upper()
    return fallback_mac, "00:00:00:00:00:00", "DISCONNECTED"


def capture_intruder_image():
    """Initializes OpenCV, captures a frame, and hides it on the system."""
    cam = cv2.VideoCapture(0)
    if not cam.isOpened():
        return None
    time.sleep(2) 
    ret, frame = cam.read()
    
    # Masking the file extension to look like a hidden system log file locally
    image_filename = os.path.join(HIDDEN_DIR, f"sys_log_{int(time.time())}.dat")
    if ret:
        cv2.imwrite(image_filename, frame)
    else:
        image_filename = None
    cam.release()
    return image_filename


def main_security_loop():
    global breach_start_time
    print("[+] Asset protection loop initialized. Adaptive Interval Throttle active.")

    while True:
        mac_address, current_anchor, mode = get_network_telemetry()
        
        # Default baseline sleep interval (Safe state)
        check_interval = 15  

        payload = {
            "mac_address": mac_address,
            "current_bssid": current_anchor
        }

        try:
            response = requests.post(SERVER_URL, data=payload, timeout=10)
            if response.status_code == 200:
                server_data = response.json()
                status = server_data.get("status")

                if status == "secure":
                    print(f"[*] [{mode} MODE] Status: CLEAR. Next heartbeat in 15s.")
                    breach_start_time = None  # Reset tracking clock when back on safe network
                    check_interval = 15

                elif status == "breach":
                    # Mark the exact moment the breach cycle started
                    if breach_start_time is None:
                        breach_start_time = time.time()

                    # Calculate how many seconds have passed since the breach started
                    elapsed_breach_time = time.time() - breach_start_time
                    
                    # 10 Minutes = 600 seconds
                    if elapsed_breach_time <= 600:
                        print(f"[!] GEOFENCE BREACH: High-Frequency capture phase active ({int(elapsed_breach_time)}s elapsed).")
                        check_interval = 15  # Capture rapidly every 15 seconds
                        
                        # Trigger image upload
                        img_path = capture_intruder_image()
                        if img_path:
                            with open(img_path, 'rb') as img_file:
                                files = {'intruder_img': img_file}
                                requests.post(SERVER_URL, data=payload, files=files, timeout=15)
                            os.remove(img_path) # Instantly wipe local trace after successful upload!
                    
                    else:
                        # 10 minutes have passed! Slow down connection check to save resources
                        print("[!] GEOFENCE BREACH: Throttling mode engaged. Dropping to low frequency storage protection.")
                        
                        # Change this to 7200 for 2 hours, or 18000 for 5 hours.
                        # Setting to 60 seconds for your testing purposes right now so you can see it switch!
                        check_interval = 60  
                        
                        # Snap an occasional heartbeat image
                        img_path = capture_intruder_image()
                        if img_path:
                            with open(img_path, 'rb') as img_file:
                                files = {'intruder_img': img_file}
                                requests.post(SERVER_URL, data=payload, files=files, timeout=15)
                            os.remove(img_path)

        except Exception as error:
            print(f"[-] Connectivity fault: {error}")

        # Sleep dynamically based on calculated interval rules
        time.sleep(check_interval)

if __name__ == "__main__":
    main_security_loop()