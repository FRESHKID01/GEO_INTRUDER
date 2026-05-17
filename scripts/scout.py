import subprocess
import requests
import time
import cv2
import os

# --- CONFIG ---
# Ensure this matches your XAMPP setup
API_URL = "http://127.0.0.1/GEO_INTRUDER/api_heartbeat.php"
DEVICE_MAC = "F8-94-C2-CC-34-45"
# Change this to 1 if 0 still shows the Iriun "Black Screen"
CAMERA_INDEX = 1 
# --------------

def get_bssid():
    try:
        out = subprocess.check_output("netsh wlan show interfaces").decode()
        for line in out.split('\n'):
            if "BSSID" in line:
                return line.split(':')[1].strip()
    except:
        return "NULL"
    return "NULL"

def capture_image():
    # Added CAP_DSHOW to prevent Windows driver lag
    cam = cv2.VideoCapture(CAMERA_INDEX, cv2.CAP_DSHOW)
    
    # Critical for new webcams: Give the sensor time to adjust light/focus
    time.sleep(2) 
    
    ret, frame = cam.read()
    if ret:
        filename = "temp_capture.jpg"
        # Save the frame
        cv2.imwrite(filename, frame)
        cam.release()
        return filename
    
    cam.release()
    return None

if __name__ == "__main__":
    print(f"--- GEO-INTRUDER SCOUT ACTIVE (Using Camera {CAMERA_INDEX}) ---")
    while True:
        bssid = get_bssid()
        print(f"Network Check: {bssid}")

        photo = capture_image()
        
        try:
            payload = {'mac': DEVICE_MAC, 'bssid': bssid}
            
            if photo and os.path.exists(photo):
                with open(photo, 'rb') as img:
                    files = {'evidence': img}
                    r = requests.post(API_URL, data=payload, files=files)
            else:
                r = requests.post(API_URL, data=payload)
                
            print(f"Server says: {r.text}")
        except Exception as e:
            print(f"Communication Error: {e}")

        # Cleanup the temp file
        if photo and os.path.exists(photo):
            try:
                os.remove(photo)
            except:
                pass 

        # Changed to 60 seconds to avoid filling up your 'alerts' folder too fast
        time.sleep(60)