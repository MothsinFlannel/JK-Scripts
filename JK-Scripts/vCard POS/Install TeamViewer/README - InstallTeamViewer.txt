Required System - Raspbian (Raspberry Pi OS)

--------DESCRIPTION

InstallTeamViewer.sh installs Teamviewer on the vCard POS.

--------INSTRUCTIONS

1. Place the script on a USB Drive.
2. Connect the USB Drive to your vCard POS.
3. Open the Terminal (Shortcut: Ctrl + Alt + T).
4. Run the following commands in order...
	sudo mkdir /installmount
	sudo mount /dev/sda1 /installmount
	sudo /installmount/InstallTeamViewer.sh
5. Once TeamViewer launches, make sure to configure the settings if you want a predefinied password.