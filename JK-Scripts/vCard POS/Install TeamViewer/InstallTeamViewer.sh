# Run these 3 commands manually to connect the Flash Drive and Activate the InstallTeamViewer.sh Script:

# 1. sudo mkdir /1AInstallMount
# 2. sudo mount /dev/sda1 /1AInstallMount
# 3. sudo /1AInstallMount/InstallTeamViewer.sh

# ---------------------------------------------------------------------------

# Changes Updates Directory

sudo sed -i "1s,.*,deb https://legacy.raspbian.org/raspbian/ stretch main contrib non-free rpi," /etc/apt/sources.list

# Gets Updates

sudo apt-get update

# Gets Teamviewer

sudo wget -P "/home/pi/Downloads" "https://download.teamviewer.com/download/linux/teamviewer-host_armhf.deb";
sudo apt -y install /home/pi/Downloads/teamviewer-host_armhf.deb

# Accepts Teamviewer EULA

teamviewer license accept