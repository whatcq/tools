; WARNING: 坑：文件需要是windows换行，git中弄成了UNIX！！！
; ==============================
; 功能：通过快捷键（win+n）把当前选中的内容保存到文件
; 如果没有选中内容，则是“随笔记”
; ==============================

;# Win(Windows 徽标键) 
;! Alt 
;^ Control 
;+ Shift 
;&  用于连接两个按键(含鼠标按键) 合并成一个自定义热键.  

queueFile := "queue.txt"

#n::
    InputBox, UserInput, Phone Number, Please enter a phone number., , 640, 120
    if ErrorLevel
        return

    ;MsgBox, You entered "%UserInput%"
    FileAppend, %A_YYYY%-%A_MM%-%A_DD% %A_Hour%:%A_Min%:%A_Sec% "%UserInput%" `r`n, %queueFile%,UTF-8
    return

#c::
    input := OneLineText(GetSelection())
    if (input != "") {
        FileAppend, %A_YYYY%-%A_MM%-%A_DD% %A_Hour%:%A_Min%:%A_Sec% "%input%" `r`n, %queueFile%, UTF-8

        MyTrayTip("Selected!")
    }
    return

#b::
    input = OneLineText(%clipboard%)   ; 把任何复制的文件, HTML 或其他格式的文本转换为纯文本.
    if (input != "") {
        FileAppend, %A_YYYY%-%A_MM%-%A_DD% %A_Hour%:%A_Min%:%A_Sec% "%input%" `r`n, %queueFile%, UTF-8

        MyTrayTip("Pushed!")
    }
    return

OneLineText(rawString) {
    replaced := StrReplace(rawString, "`n", "\n")
    replaced := StrReplace(replaced, "`r", "\r")
    return replaced
}


MyTrayTip(info) {
    ; 托盘提示
    TrayTip, "===", %info%
    SetTimer, HideTrayTip, -3000
}
HideTrayTip() {  ; NOTE: For Windows 10, replace this function with the one defined above.
    TrayTip
}

GetSelection(timeoutSeconds:= 0.3)
{
    Clipboard := ""  ; Clear clipboard for ClipWait to function.
    Send ^c  ; Send Ctrl+C to get selection on clipboard.
    ClipWait %timeoutSeconds% ; Wait for the copied text to arrive at the clipboard.
    return Clipboard
}

PasteText(s)
{
    Clipboard :=s  ; Put the text on the clipboard.
    Send ^v  ; Paste the text with Ctrl+V.
}
