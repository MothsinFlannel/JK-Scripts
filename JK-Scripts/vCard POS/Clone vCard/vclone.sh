echo " "
echo "Welcome to vClone! This script is used to clone vCard Raspberry Pi's without having to manually type each command."
echo " "
read -p "Detect unlisted drives using RPIboot? (y/n): " rpiconfirm
echo "RPIconfirm set to $rpiconfirm"

if [ "$rpiconfirm" = "y" ]; then
	echo "Running RPIboot..."
	# Update the directory below to your rpiboot location if necessary
	sudo '/home/jk/usbboot/rpiboot' &
	wait
fi

sleep 2

echo "------------------------------------------"
sudo fdisk -l | awk '!/^Disk \/dev\/loop/ && /^Disk \/dev\// {print $2,$3,$4} /Disk model/ {print $3,$4,$5,$6}'
echo "------------------------------------------"

read -p "Last Letter of Source Drive = sd(b/c/d...): " source
echo "The Source is sd$source"
echo "------------------------------------------"
read -p "Last Letter of Destination Drive = sd(b/c/d...): " destination
echo "The Destination is sd$destination"
echo "------------------------------------------"
read -p "Press Enter to Proceed (if anything looks incorrect, close the terminal and run again while skipping RPIBoot)" confirm
echo "------------------------------------------"

# Check if partitions exist and unmount partitions before modifying them
if sudo fdisk -l /dev/"sd$destination" | grep -q "/dev/sd${destination}1"; then
    sudo umount /dev/"sd$destination"1 2>/dev/null
else
	echo "No partition 1 for umount. Skipping..."
fi

if sudo fdisk -l /dev/"sd$destination" | grep -q "/dev/sd${destination}1"; then
    sudo umount /dev/"sd$destination"2 2>/dev/null
else
	echo "No partition 2 for umount. Skipping..."
fi

# Check if partitions exist before attempting to delete them
if sudo fdisk -l /dev/"sd$destination" | grep -q "/dev/sd${destination}1"; then
    sudo fdisk /dev/"sd$destination" <<EOF
d
1
w
EOF
else
	echo "No partition 1 to delete. Skipping..."
fi

sleep 3

if sudo fdisk -l /dev/"sd$destination" | grep -q "/dev/sd${destination}2"; then
    sudo fdisk /dev/"sd$destination" <<EOF
d
w
EOF
else
	echo "No partition 2 to delete. Skipping..."
fi

sleep 3

sudo dd if="/dev/sd$source" of="/dev/sd$destination" bs=64k conv=noerror,sync status=progress &

wait

sudo eject /dev/"sd$destination"
