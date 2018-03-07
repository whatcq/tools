
;# Win(Windows 徽标键) 
;! Alt 
;^ Control 
;+ Shift 
;&  用于连接两个按键(含鼠标按键) 合并成一个自定义热键.  


#n::
	; 通过剪贴板获取选中内容
	WinActive("A") ; sets last found window
	ControlGetFocus, ctrl
	if (RegExMatch(ctrl, "A)Edit\d+"))
	    ControlGet, text, Selected,, %ctrl%
	else {
	    clipboardOld := Clipboard
	    Send, ^c
	    if (Clipboard != clipboardOld) {
		text := Clipboard
		Clipboard := clipboardOld
	    }
	}
	; MsgBox % text

	saveResult := RunWaitOne(StrReplace(StrReplace(StrReplace(text, "`r", "``r"), "`n", "``n"), """", """"""))

	; Run, "F:\xampp\php\php.exe" "D:\mysoft\cqiu-note.php" "%text%", , Max

	MsgBox % saveResult

	; 执行命令并返回结果
	RunWaitOne(command) {
	    ; WshShell object: http://msdn.microsoft.com/en-us/library/aew9yb99
	    shell := ComObjCreate("WScript.Shell")
	    ; Execute a single command via cmd.exe

	    exec := shell.Exec(ComSpec " /C " "php " " D:\mysoft\cqiu-note.php """ command """")
	    ; Read and return the command's output
	    return exec.StdOut.ReadAll()
	}

	RunWaitMany(commands) {
	    shell := ComObjCreate("WScript.Shell")
	    ; Open cmd.exe with echoing of commands disabled
	    exec := shell.Exec(ComSpec " /Q /K echo off")
	    ; Send the commands to execute, separated by newline
	    exec.StdIn.WriteLine(commands "`nexit")  ; Always exit at the end!
	    ; Read and return the output of all commands
	    return exec.StdOut.ReadAll()
	}

	; 要更精确的控制显示的时间
	; 而不使用 Sleep 的方法 (它停止了当前线程):
	#Persistent

	; 托盘提示
	TrayTip, Timed TrayTip, %saveResult%
	SetTimer, HideTrayTip, -5000

	HideTrayTip() {  ; NOTE: For Windows 10, replace this function with the one defined above.
	    TrayTip
	}

return