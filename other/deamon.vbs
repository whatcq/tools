'deamon 1分钟读一次消息，一次性读取，按行分割；消息格式：content /待结束/待开始/已结束/已开始

'==============================
'配置
'==============================
Const ForReading = 1, ForWriting = 2, ForAppending = 8

'数据文件
Dim msgFile
msgFile=".\msg.txt"


Set fso=WScript.createobject("Scripting.FileSystemObject")

Dim status,timer,fileSize,zStatus
status=0
timer = 0
fileSize=0

While True
	Set f=fso.openTextFile(msgFile, ForReading, TRUE)

	' 读取最新消息加和
	zStatus=0
	Do While f.AtEndOfStream <> True
		tmp = f.ReadLine
		If isNumeric(tmp) Then
			zStatus=CInt(tmp) + zStatus
		End If
	loop
	fileSize=f.Column+f.Line
	f.Close
	
	' 文件不为空则重置
	If fileSize > 2 Then
		Set f=fso.openTextFile(msgFile, ForWriting, TRUE)
		f.write ""
		f.Close
	End If
	
	' 加成计算消息
	If zStatus<>0 Then
		status = status + zStatus
		zStatus = 0
	End If
	
	' 不为空则开始计时(记次数)
	If status=0 Then
		timer=0
	Else
		timer=timer+1
	End If

	' 到时间提醒处理 5min
	If timer*10>5*60 Then
		'提醒是单独的，调用其他进程而不用MsgBox，以免中断计时
		Set WshShell = WScript.CreateObject("WScript.Shell") 
		WshShell.Run "wscript alert.vbs ""有始有终，继续执行！"& status & """ ""搞啥子你？！"""
		Set WshShell = Nothing
		' 重新开始记次数
		timer=0
	End If
	
	WScript.Sleep 10*1000 '豪秒
Wend

Set fso=Nothing
