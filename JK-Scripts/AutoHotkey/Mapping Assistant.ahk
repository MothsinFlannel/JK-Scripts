!m::
#NoEnv  ; Recommended for performance and compatibility with future AutoHotkey releases.
#SingleInstance force  ; Ensures only one instance of the script is running.
;SetTitleMatchMode, 2  ; Allows partial window title matching.

;--------------------------------------------------------------------

; Define the width and height for the Chrome window
width := 800
height := 600

;--------------------------------------------------------------------

; Define possible searches with arrays
trelloArray := ["t", "tr", "trel"]
goldenDragonCityArray := ["gd"]
helpArray := ["h", "he", "hel", "help"]

;--------------------------------------------------------------------

InputBox, userInput, Mapping Assistant, Enter a value

StringLower, userInput, userInput

matchFound := false

; Loop through each element in helpArray
for index, value in helpArray
{
    ; Check if userInput equals the current value
    if ((userInput = value) && (matchFound = false))
    {
        ; Set the flag variable to true and exit the loop
        matchFound := true
        message := ""

        message .= "Help: "

        ; Iterate through helpArray
        for index, value in helpArray
        {
            message .= value . ", "
        }

        message .= "`n"
        message .= "Trello: "

        ; Iterate through trelloArray
        for index, value in trelloArray
        {
            message .= value . ", "
        }

        message .= "`n"
        message .= "Golden Dragon/Magic City: "

        ; Iterate through goldenDragonCityArray
        for index, value in goldenDragonCityArray
        {
            message .= value . ", "
        }

        message .= "`n"

        MsgBox, % message
        break
    }
}

; Loop through each element in trelloArray
for index, value in trelloArray
{
    ; Check if userInput equals the current value
    if ((userInput = value) && (matchFound = false))
    {
        ; Set the flag variable to true and exit the loop
        matchFound := true
        Run, chrome.exe https://trello.com/b/mwtwqnSE/request-forms --new-window
        break
    }
}

; Loop through each element in goldenDragonCityArray
for index, value in goldenDragonCityArray
{
    ; Check if userInput equals the current value
    if ((userInput = value) && (matchFound = false))
    {
        ; Set the flag variable to true and exit the loop
        matchFound := true
        Run, chrome.exe https://backend.goldendragoncity.com/ --new-window
        Sleep, 500
        Run, chrome.exe https://backend.magiccity777.com/
        break
    }
}