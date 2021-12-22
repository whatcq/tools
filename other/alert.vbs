'记得修改本文件编码格式为ANSII！

'类似msgbox
'定时停留弹出框函数
Sub Print(text,timeout,title)
    Dim WshShell
    Set WshShell = CreateObject("WScript.Shell")
    WshShell.Popup text,timeout,title
    Set WshShell = nothing
End Sub

thisTask= wscript.arguments(0)
leftTime= wscript.arguments(1)
Dim br
br= Chr(13) & Chr(10) & Chr(13) & Chr(10) & "===================" & Chr(13) & Chr(10) & Chr(13) & Chr(10)
'MsgBox  br & thisTask & Chr(9) &  "剩余:"&leftTime&"min"  & br ,vbOKOnly,Time & ": working..."
Print  br & thisTask & Chr(9) &  "剩余:"&leftTime&"min"  & br ,10,Time & ": working..."

