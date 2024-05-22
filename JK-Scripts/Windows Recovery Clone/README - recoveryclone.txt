Required System - Unix Distribution

--------DESCRIPTION

recoveryclone.py is used to clone Windows Recovery Drives in bulk. In practice, these 16gb flash drives are connected to our Golden Dragon Kiosks so that we have a boot drive to diagnose machine failures from.

--------PREREQUISITES

This script requires you to have python3 installed on your system. This can be done with the following commands...

	sudo apt update
	sudo apt install python3	
	sudo apt install python3-pip

You can verify the installation completed by using the command...

	python3 --version

It's recommended you restart your PC after the installation.

--------INSTRUCTIONS

1. Open the Terminal (Shortcut: Ctrl + Alt + T).
2. Launch the app by typing...
	sudo python3 '$INSERT_PATH_TO_SCRIPT_HERE/recoveryclone.py'

The program should guide you from here, but here are some tips...

- When it asks for the source drive and destination drives, make sure you only type in the Tail/Ending letter of the drive. Don't type the full path or the "sd" part. Just a single letter.

- Disregard errors related to "not enough space on destination". It's just empty space that doesn't fit. All the actual content should transfer correctly.
