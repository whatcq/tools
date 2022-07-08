; WARNING: 坑：文件需要是windows换行，git中弄成了UNIX！！！
; ==============================
; 功能：通过快捷键（win+n）把当前选中的内容保存到文件（通过PHP！）
; 缺点：1 需要选中文本；2 有php执行的黑框一闪。
; ==============================

;# Win(Windows 徽标键) 
;! Alt 
;^ Control 
;+ Shift 
;&  用于连接两个按键(含鼠标按键) 合并成一个自定义热键.  


#n::
	selection:= GetSelection()

	if (selection = "") {
		InputBox, UserInput, Phone Number, Please enter a phone number., , 640, 120
		if ErrorLevel
			return

		;MsgBox, You entered "%UserInput%"
		FileAppend, %A_YYYY%-%A_MM%-%A_DD% %A_Hour%:%A_Min%:%A_Sec% "%UserInput%" `n, queue.log,UTF-8
		return
	}

	FileAppend, %A_YYYY%-%A_MM%-%A_DD% %A_Hour%:%A_Min%:%A_Sec% "%selection%" `n, picker.log,UTF-8

	saveResult := "Saved!"

	; 要更精确的控制显示的时间
	; 而不使用 Sleep 的方法 (它停止了当前线程):
	#Persistent

	; 托盘提示
	TrayTip, Timed TrayTip, %saveResult%
	SetTimer, HideTrayTip, -3000

HideTrayTip() {  ; NOTE: For Windows 10, replace this function with the one defined above.
	TrayTip
}


GetSelection(timeoutSeconds:= 0.5)
{
	Clipboard:= ""  ; Clear clipboard for ClipWait to function.
	Send ^c  ; Send Ctrl+C to get selection on clipboard.
	ClipWait %timeoutSeconds% ; Wait for the copied text to arrive at the clipboard.
	return Clipboard
}

PasteText(s)
{
	Clipboard:=s  ; Put the text on the clipboard.
	Send ^v  ; Paste the text with Ctrl+V.
}
