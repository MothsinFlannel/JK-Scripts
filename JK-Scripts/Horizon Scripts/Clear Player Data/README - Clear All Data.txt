--------DESCRIPTION

"Clear All Data.sql" deletes all of the previous user and device information from a Horizon Server.

--------INSTRUCTIONS

1. On the Horizon Server, open "SQL Server Management Studio".
2. Connect with the following settings...
	Server Type: Database Engine
	Server Name: (local)
	Authentication: Windows Authentication
3. Go to File > Open > File
4. Open "Clear All Data.sql"
5. Click the "! Execute" button around the top-left. Once it finishes, execute a second time just to be safe (some of the data you're deleting fails since it relies on OTHER data being deleted first, which thankfully has now been deleted in the first cycle!).
6. Exit SQL Server Managment Studio.
7. Use the Desktop Shortcut "Stop Game Service"
8. Use the Desktop Shortcut "Start Game Service"