'记得修改本文件编码格式为ANSII！

' ------------------
Const soundFile = "C:\Windows\Media\ding.wav"

' 创建 Windows Media Player 对象
Set wmp = CreateObject("WMPlayer.OCX")

' 隐藏播放器界面
'wmp.settings.autoStart = False
'wmp.settings.enableContextMenu = False
'wmp.settings.volume = 100 ' 设置音量（0-100）

' 播放 WAV 文件
wmp.URL = soundFile

' 等待播放结束
Do While wmp.playState <> 1 ' 1 表示播放结束
    WScript.Sleep 100
Loop

' 释放资源
wmp.close
Set wmp = Nothing

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
