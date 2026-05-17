import cv2

def list_cameras():
    index = 0
    available_indices = []
    while index < 5:
        cap = cv2.VideoCapture(index, cv2.CAP_DSHOW)
        if cap.isOpened():
            ret, frame = cap.read()
            if ret:
                print(f"Index {index} is WORKING.")
                available_indices.append(index)
            cap.release()
        index += 1
    return available_indices

print("Checking for cameras...")
list_cameras()