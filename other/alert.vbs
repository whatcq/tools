'=======alert.vbs========
thisTask= wscript.arguments(0)
leftTime= wscript.arguments(1)
Dim br,format
br= Chr(13) & Chr(10)
format= br & br & "===================" & br & br

'弹窗提醒
'MsgBox  br & thisTask & Chr(9) &  "剩余:"&leftTime&"min"  & br ,vbOKOnly,Time & ": working..."

'超时时间3s，自动关闭
Dim WshShell
Set WshShell = CreateObject("WScript.Shell")
WshShell.Popup br & thisTask & Chr(9) & "剩余:" & leftTime & "min" & br,3,Time & ": working..."
Set WshShell = nothing