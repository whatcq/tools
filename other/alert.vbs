'=======alert.vbs========
thisTask= wscript.arguments(0)
leftTime= wscript.arguments(1)
Dim br,format
br= Chr(13) & Chr(10)
format= br & br & "===================" & br & br

'��������
'MsgBox  br & thisTask & Chr(9) &  "ʣ��:"&leftTime&"min"  & br ,vbOKOnly,Time & ": working..."

'��ʱʱ��3s���Զ��ر�
Dim WshShell
Set WshShell = CreateObject("WScript.Shell")
WshShell.Popup br & thisTask & Chr(9) & "ʣ��:" & leftTime & "min" & br,3,Time & ": working..."
Set WshShell = nothing