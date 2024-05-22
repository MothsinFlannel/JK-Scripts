Required System - Unix Distribution

--------DESCRIPTION

vclone.sh allows you to clone vCard POS's from a flash drive or other vCard POS. In practice, this is our method for creating our new POS's by cloning a copy of the OS from a source Flash Drive to a destination Raspberry PI.

--------INSTRUCTIONS

1. Open the Terminal (Shortcut: Ctrl + Alt + T).
2. Launch the app by typing...
	sudo python3 '$INSERT_PATH_TO_SCRIPT_HERE/vclone.sh'

The program should guide you from here, but here are some tips...

- The first thing it asks for is if you want to "Detect Unlisted Drives Using RPI Boot". RPI Boot has to be run in order to recognize the Raspberry Pi's board as a drive. Always say "y" to this unless you cancelled the script halfway through last time you ran it. RPI Boot crashes if it's run while the Raspberry Pi drive is still mounted, and vclone.sh doesn't unmount and disconnect the drive unless it finishes entirely.

- When it asks for the source drive and destination drives, make sure you only type in the Tail/Ending letter of the drive. Don't type the full path or the "sd" part. Just a single letter.

- Disregard errors related to "not enough space on destination". It's just empty space that doesn't fit. All the actual content should transfer correctly.