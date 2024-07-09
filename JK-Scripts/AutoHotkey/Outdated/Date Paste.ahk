; Define Ctrl+V hotkey
~^v::
    ; Wait for 1 second (1000 milliseconds)
    Sleep, 250
    
    ; Press Tab key
    Send, {Tab}

    ; Get today's date in the desired format
    FormatTime, CurrentDate,, MM/dd ; Adjust the format of the date as needed
    
    ; Send the date to the active window
    SendInput, %CurrentDate%
Return