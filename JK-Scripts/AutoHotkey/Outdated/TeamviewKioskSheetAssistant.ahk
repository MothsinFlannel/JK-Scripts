#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

; Declare arrays to store SerialNumbers and TeamviewerIDs
SerialNumbers := []
TeamviewerIDs := []
BatchNumber := ""

; Hotkey to store SerialNumbers and TeamviewerIDs from multiple rows
F5::
{
    ; Prompt for Batch Number
    InputBox, BatchNumber, Batch Number, Please enter the Batch Number:
    if ErrorLevel
    {
        Tooltip, Batch Number input was cancelled.
        Sleep, 2000  ; Display the tooltip for 2 seconds
        Tooltip
        return
    }

    ; Clear previous data
    SerialNumbers := []
    TeamviewerIDs := []

    ; Copy the selected cell content to the clipboard
    Send, ^c
    Sleep, 100  ; Small delay to ensure clipboard updates

    ; Verify clipboard content
    ClipWait, 1  ; Wait for the clipboard to contain data
    clipboardContent := Clipboard
    if clipboardContent =
    {
        Tooltip, Clipboard is empty. Please try copying again.
        Sleep, 2000  ; Display the tooltip for 2 seconds
        Tooltip
        return
    }

    ; Split the clipboard content into lines
    Loop, Parse, clipboardContent, `n, `r
    {
        ; Extract each line
        line := Trim(A_LoopField) ; Trim whitespace from the line

        ; Use RegEx to extract the SerialNumber and TeamviewerID
        ; Assuming the format: "SerialNumber MiddlePart TeamviewerID"
        if RegExMatch(line, "^(.*?)\s+\S+\s+(.*?)$", match)
        {
            ; Assign extracted values to arrays
            SerialNumbers.Push(match1)
            TeamviewerIDs.Push(match2)
        }
    }

    Tooltip, % "Total Entries: " SerialNumbers.MaxIndex()
    Sleep, 2000  ; Display the tooltip for 2 seconds
    Tooltip
}
return

; Hotkey to input the stored values
F6::
{
    if (SerialNumbers.MaxIndex() > 0)
    {
        ; Get the first SerialNumber and TeamviewerID from the arrays
        SerialNumber := SerialNumbers.RemoveAt(1)
        TeamviewerID := TeamviewerIDs.RemoveAt(1)

        ; Input the TeamviewerID
        Send, %TeamviewerID%

        ; Press Tab
        Send, {Tab}

        ; Input the password
        Send, 411st{@}r23{!}jkgroup

        ; Press Tab
        Send, {Tab}

        ; Input "TBA" followed by BatchNumber, then " - " followed by the SerialNumber
        Send, TBA%BatchNumber% - %SerialNumber%

        ; Display remaining entries as a tooltip
        if (SerialNumbers.MaxIndex() > 0)
        {
            Tooltip, % "Remaining Entries: " SerialNumbers.MaxIndex()
            Sleep, 2000  ; Display the tooltip for 2 seconds
            Tooltip
        }
        else
        {
            Tooltip, No more entries to process.
            Sleep, 2000  ; Display the tooltip for 2 seconds
            Tooltip
        }
    }
    else
    {
        Tooltip, No more entries to process.
        Sleep, 2000  ; Display the tooltip for 2 seconds
        Tooltip
    }
}
return
