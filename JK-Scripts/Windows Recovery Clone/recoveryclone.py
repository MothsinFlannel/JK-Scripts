import subprocess
import os

# Run the fdisk command using subprocess and capture its output
fdisk_output = subprocess.run(['sudo', 'fdisk', '-l'], capture_output=True, text=True)

# Split the output into lines
output_lines = fdisk_output.stdout.split('\n')

# Iterate over the lines and process them accordingly
for line in output_lines:
    if not line.startswith('Disk /dev/loop') and line.startswith('Disk /dev/'):
        # Split the line into fields and print the second, third, and fourth fields
        fields = line.split()
        print(fields[1], fields[2], fields[3])

def erase_usb(drive_paths):
    try:
        # Unmount the target USB drives
        for drive_path in drive_paths:
            subprocess.run(['sudo', 'umount', drive_path], check=True)
        
        # Erase the target USB drives
        for drive_path in drive_paths:
            subprocess.run(['sudo', 'dd', 'if=/dev/zero', 'of=' + drive_path, 'bs=bs=64k', 'status=progress'], check=True)
        print("Erasing successful!")
    except subprocess.CalledProcessError as e:
        print("Error occurred while erasing:", e)

def clone_usb(source_path, target_paths):
    try:
        with open(source_path, 'rb') as source_file:
            for target_path in target_paths:
                try:
                    subprocess.run(['dd', 'if=' + source_path, 'of=' + target_path, 'bs=64k', 'status=progress'], check=True)
                except subprocess.CalledProcessError as e:
                    print(f"Error occurred while cloning to {target_path}: {e}")
                    continue  # Move on to the next target drive
            print("Cloning successful!")
    except FileNotFoundError:
        print(f"Source file '{source_path}' not found.")
    except Exception as e:
        print(f"An error occurred: {e}")

# Function to convert ending letter to full device path
def get_device_path(letter):
    return '/dev/sd' + letter

# Prompt user input for source USB drive path
source_letter = input("Enter the ending letter of the source USB drive (e.g., c): ")
source_usb = get_device_path(source_letter)

# Prompt user input for target USB drive paths
num_targets = int(input("Enter the number of target USB drives: "))
target_usbs = []
for i in range(num_targets):
    target_letter = input("Enter the ending letter of target USB drive {}: ".format(i+1))
    target_usbs.append(get_device_path(target_letter))

erase_usb(target_usbs)  # Erase the target USB drives before cloning
clone_usb(source_usb, target_usbs)
