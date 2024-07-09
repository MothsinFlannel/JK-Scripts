; Create the GUI
Gui, Add, Text,, Select the functions to enable:
Gui, Add, Checkbox, vCheckBox1 gCheckBox1Clicked, Paste With Date (Affects Ctrl + V)
Gui, Add, Checkbox, vCheckBox2 gCheckBox2Clicked, Mapping Assistant
Gui, Add, Checkbox, vCheckBox3 gCheckBox3Clicked, Kiosk Build Assistant
Gui, Add, Checkbox, vCheckBox4 gCheckBox4Clicked, Sequential Clipboard
Gui, Add, Button, gCloseGui, Close
Gui, Show,, Function Control Panel
Return

; Kiosk Build Assistant Declarations
SendMode Input  ; Recommended for new scripts due to its superior speed and reliability.
SetWorkingDir %A_ScriptDir%  ; Ensures a consistent starting directory.

; Declare arrays to store SerialNumbers and TeamviewerIDs
SerialNumbers := []
TeamviewerIDs := []
BatchNumber := ""

CheckBox1Clicked:
Gui, Submit, NoHide
if (CheckBox1)
{
    ; Code to enable Paste With Date (Affects Ctrl + V)
    MsgBox, Paste With Date (Affects Ctrl + V) enabled
}
else
{
    ; Code to disable Paste With Date (Affects Ctrl + V)
    MsgBox, Paste With Date (Affects Ctrl + V) disabled
}
Return

CheckBox2Clicked:
Gui, Submit, NoHide
if (CheckBox2)
{
    ; Code to enable Mapping Assistant
    MsgBox, Mapping Assistant enabled
}
else
{
    ; Code to disable Mapping Assistant
    MsgBox, Mapping Assistant disabled
}
Return

CheckBox3Clicked:
Gui, Submit, NoHide
if (CheckBox3)
{
    ; Code to enable Kiosk Build Assistant
    MsgBox, Kiosk Build Assistant enabled
}
else
{
    ; Code to disable Kiosk Build Assistant
    MsgBox, Kiosk Build Assistant disabled
}
Return

CheckBox4Clicked:
Gui, Submit, NoHide
if (CheckBox4)
{
    ; Code to enable Kiosk Build Assistant
    MsgBox, Sequential Clipboard enabled
}
else
{
    ; Code to disable Kiosk Build Assistant
    MsgBox, Sequential Clipboard disabled
}
Return

CloseGui:
GuiClose:
MsgBox, Thank you for using JK Admin Panel
ExitApp

; ----------------------------SECTION BELOW FOR ACTUAL HOTKEY FUNCTIONS----------------------------

; CHECKBOX 1 - PASTE WITH DATE (AFFECTS CTRL + V)

~^v::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox1)
{
    ; Wait for 1 second (1000 milliseconds)
    Sleep, 250
    
    ; Press Tab key
    Send, {Tab}

    ; Get today's date in the desired format
    FormatTime, CurrentDate,, MM/dd ; Adjust the format of the date as needed
    
    ; Send the date to the active window
    SendInput, %CurrentDate%
}
Return

; CHECKBOX 2 - MAPPING ASSISTANT

F1::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox2)
{
    ; Get the clipboard content
    ClipSaved := ClipboardAll
    Clipboard := ""
    Send ^c
    ClipWait, 1
    if ErrorLevel
    {
        MsgBox, Clipboard copy failed
        Clipboard := ClipSaved
        return
    }

    clipboardText := Clipboard
    Clipboard := ClipSaved

    ; Clear previous data
    textArray := []
    
    ; Split the clipboard text into lines
    lines := StrSplit(clipboardText, "`n")
    
    ; Initialize variables for tracking extracted text
    textA := ""
    textC := ""
    textD := ""
    textE := ""
    
    ; Extract values based on the relative position
    Loop % lines.Length()
    {
        line := lines[A_Index]
        
        ; Find "TRADE NAME:" and extract "Text A"
        If InStr(line, "TRADE NAME:")
        {
            textA := Trim(StrReplace(line, "TRADE NAME:", ""))
            textA := RegexReplace(textA, "\s+BUSINESS HOURS:.*")
            textA := "" textA ""
        }
        ; Find "CITY / STATE / ZIP:" and extract "Text C" and "Text D"
        else If InStr(line, "CITY / STATE / ZIP:")
        {
            ; Extract "Text C" and "Text D" from the same line
            ; Assuming "Text C" is before the first comma and "Text D" is after the state abbreviation
            parts := StrSplit(StrReplace(line, "CITY / STATE / ZIP:", ""), ",")
            textC := Trim(parts[1])
            stateZip := Trim(parts[2])
            parts2 := StrSplit(stateZip, " ")
            textD := Trim(parts2[2])  ; Assuming "Text D" is after the state
            textD := RegexReplace(textD, "\s+STORE.*")  ; Remove trailing "STORE" and following text
            textC := "" textC ""
            textD := "" textD ""
        }
        ; Find "ADDRESS:" and extract "Text E"
        else If InStr(line, "ADDRESS:")
        {
            textE := Trim(StrReplace(line, "ADDRESS:", ""))
            textE := RegexReplace(textE, "\s+COUNTY:.*")
            textE := "" textE ""
        }
    }
    
    ; Add extracted text to the array in the correct order
    textArray.push(textA)           ; This should be "Text A"
    textArray.push("6785555555")    ; This should be "Text B"
    textArray.push(textC)           ; This should be "Text C"
    textArray.push(textD)           ; This should be "Text D"
    textArray.push(textE)           ; Placeholder for "Text E"
    textArray.push(textF)           ; This should be "Text F"
    
    ; Reset the current index for Ctrl+Shift+V pasting
    currentIndex := 0
    
    return
}
Return

F2::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox2)
{
    if (currentIndex < textArray.Length())
    {
        SendInput % textArray[currentIndex]
        currentIndex++
    }
    else
    {
        MsgBox, No more items to paste
    }
    return
}
Return

F3::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox2)
{
    ; Retrieve the current contents of the clipboard
    clipboardContent := Clipboard

    ; Extract the last 4 characters
    last4Digits := SubStr(clipboardContent, -3)

    SendInput, Kiosk Admin:          U: (provided in New Admin screen)          P: Kiosk%last4Digits%{Enter}
}
Return

F4::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox2)
{
    ; Retrieve the current contents of the clipboard
    clipboardContent := Clipboard

    SendInput, Golden Dragon POS: https://pos.goldendragoncity.com/pos/%clipboardContent%{Enter}
    SendInput, Magic City POS: https://pos.magiccity777.com/pos/login{Enter}
}
Return

; CHECKBOX 3 - KIOSK BUILD ASSISTANT

; Define the F5 hotkey
F5::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox3)
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
Return

F6::
Gui, Submit, NoHide ; Update the variables with the current state of the GUI
if (CheckBox3)
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
Return


; Riversweeps POS: https://river-pay.com																													
; Boss:                  U: LuckyHeifer2                   P: boss0531																													
; Cash:                  U: LuckyHeifer2_cashier                   P: cash0531																													
																													
; Ultrapanda: https://www.ultrapanda.mobi																													
; Boss:                  U: LuckyHeifer2                   P: boss0531																													
; Cash:                  U: LuckyHeifer2Cash                   P: cash0531																													
																													
; MAPPED WITH agtser@yahoo.com
; Please add the following store under F2WCHUCK > F2WSHOOP > (store goes here)
; {PASTE THE LOCATION DETAILS HERE FROM "TRADE NAME", "ADDRESS", AND "CITY/STATE/ZIP"}
; Fortune2Go: pos2.fortune2go20.com																													
; U: boss1879                                  P: b123456																													