import os
import shutil

# Set your project directory here
project_dir = r"C:\wamp64\www\PHP\Class\nalifitzone"

# Define target folders
folders = {
    "images": [".jpg", ".jpeg", ".png", ".gif", ".webp"],
    "videos": [".mp4", ".mov", ".avi"],
    "css": [".css"],
    "js": [".js"],
    # Uncomment if you want to handle fonts too
    # "fonts": [".ttf", ".woff", ".woff2"],
}

# Create folders if they don't exist
for folder in folders:
    folder_path = os.path.join(project_dir, folder)
    if not os.path.exists(folder_path):
        os.makedirs(folder_path)
        print(f"Created folder: {folder_path}")

# Move files into corresponding folders
for filename in os.listdir(project_dir):
    file_path = os.path.join(project_dir, filename)
    if os.path.isfile(file_path):
        _, ext = os.path.splitext(filename.lower())
        moved = False
        for folder, extensions in folders.items():
            if ext in extensions:
                target_path = os.path.join(project_dir, folder, filename)
                shutil.move(file_path, target_path)
                print(f"Moved '{filename}' to '{folder}/'")
                moved = True
                break
        if not moved:
            print(f"Skipped '{filename}' (no matching folder)")

print("✅ Done organizing files!")
