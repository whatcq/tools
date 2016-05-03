SetTitleMatchMode 2
; 作为优化，这句放外面，条件就是Chrome得先启动。
chromeId := WinExist("- Google Chrome")

; 快捷键不能再发送，否则死循环了
#v::
	currentId := WinExist("A")
	Send {Ctrl down}s{Ctrl up}
	; msgBox % currentId
	
	WinActivate, ahk_id %chromeId%
	Send {F5}
	; PostMessage, (对Chrome不灵，IE可以), VK_F5, ahk_id %chromeId%
	
	; 切换回来，会闪一下，ok，勉强用着吧
	WinActivate, ahk_id %currentId%
return

