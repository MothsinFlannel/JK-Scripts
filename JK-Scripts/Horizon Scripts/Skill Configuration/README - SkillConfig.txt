--------DESCRIPTION
Horizon's Skill Mode can be enabled/disabled by going to a specific file on the Horizon Server and changing the word "True" to "False" (or vice-versa) based on what mode you want it in.

Normally, you'd have to navigate to "C:\Gateway\CABFiles\HelpFiles\SkillConfig.xml" on the Horizon Server, right click it, and open it with Notepad to edit this. Since we like making things easy though, I've created this shortcut to take you straight there.

--------INSTRUCTIONS

1. On the Horizon Server: use the SkillConfig.xml shortcut or navigate to "C:\Gateway\CABFiles\HelpFiles\SkillConfig.xml" on the Horizon Server, right click it, and open it with Notepad.
2. Go to where the file says...

	<RequireSkill>____</RequireSkill>

3. Fill in the blank with "true" or "false" based on whether this location wants the Skill Slider or not. Save and exit.

4. Use VNC Viewer to access each Game Terminal. Log out and log back into each one (or restart them all) so they'll pull the new configuration settings from the Server.