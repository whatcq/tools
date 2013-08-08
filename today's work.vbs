'!!!注意运行时需要将本文件保存为ANSI编码
'2012-1-14 Cqiu
'+启动确认
'+时间可配置
'+任务中提醒(新进程:2013-5-23)
'+分月建数据

'这种死循环还有点考人呢！2013-5-22

'==============================
'配置
'==============================
ForAppending=8

'数据文件
Dim dataFile
dataFile=".\todayWorks_"&Year(Date())&"_"&Month(Date())&".txt"
Dim SC 'splitChar
SC=" "

'==============================
'启动确认
'==============================
'Dim startConfirm
'startConfirm=false'true'
'If startConfirm = True Then
'	Dim start
'	If MsgBox ("启动《任务驱动》？",vbYesNo,"莫等闲，白了少年头！")=vbNo Then
'		WScript.Quit
'	End If
'End If

'==============================
'主体程序
'==============================
Dim taskTime,perAlertTime,b4ExpireAlertTime,thisTask,leftTime, defaultTask,newTask,result,br,sleepTime
taskTime=20 ' 1个番茄时间
perAlertTime=5
b4ExpireAlertTime=0 'todo
leftTime=0
'set fso=wscript.createobject("scripting.filesystemobject")
defaultTask="休息一下"
result="===========" '新开始
br= Chr(13) & Chr(10) & Chr(13) & Chr(10) & "===================" & Chr(13) & Chr(10) & Chr(13) & Chr(10)
While True

	'提醒
	If leftTime>0 Then
		'result=MsgBox ( br & thisTask & br ,vbOKOnly,Time & ": working..." & leftTime)
		'Wscript.Echo  br & thisTask & br ,vbOKOnly,Time & ": working..." & leftTime
		Set WshShell = WScript.CreateObject("WScript.Shell") 
		WshShell.Run "wscript alert.vbs """& thisTask &""" """& leftTime &""""
		Set WshShell = Nothing
		result="working"
'=======alert.vbs========
'thisTask= wscript.arguments(0)
'leftTime= wscript.arguments(1)
'Dim br
'br= Chr(13) & Chr(10) & Chr(13) & Chr(10) & "===================" & Chr(13) & Chr(10) & Chr(13) & Chr(10)
'MsgBox  br & thisTask & Chr(9) &  "剩余:"&leftTime&"min"  & br ,vbOKOnly,Time & ": working..."
'=======/alert.vbs========
	'没有任务则创建
	ElseIf thisTask="" Then
		Dim taskInfo
		newTask=InputBox("下一个任务是：", Time & "-下一个任务",defaultTask&" "&taskTime&" "&perAlertTime&" "&b4ExpireAlertTime)
		If newTask="" Then
			WScript.Quit
		End If
		taskInfo=Split(newTask,SC)
		thisTask=taskInfo(0)
		If UBound(taskInfo)>0 Then
			If taskInfo(1) <> "" Then
				taskTime=CInt(taskInfo(1))
			End If
		End If
		leftTime=taskTime
		If UBound(taskInfo)>1 Then
			If taskInfo(2) <> "" Then
				perAlertTime=CInt(taskInfo(2))
			End If
		End If
		If UBound(taskInfo)>2 Then
			If taskInfo(3) <> "" Then
				b4ExpireAlertTime=CInt(taskInfo(3))
			End If
		End If
	'完成结果
	Else
		result=MsgBox ("是否已完成？" & br & thisTask & br,vbYesNoCancel,Time & "上一任务：")
		Select Case result
			Case vbYes
				result="已完成"
				defaultTask=thisTask
			Case vbNo
				result="未完成"
				defaultTask=thisTask
			Case Else 'vbCancel
				result="已取消"
				defaultTask="锻炼"
		End Select
		leftTime=0
		thisTask=""
	End If

	If thisTask<>"" Then
		'保存新任务
		If result<>"working" Then
			Set fso=WScript.createobject("scripting.filesystemobject")
			Set f=fso.openTextFile(dataFile,ForAppending,true)
			f.writeLine SC & result & Time
			f.write Now & SC & newTask
			f.Close
			Set fso=Nothing
		End If

		'sleepTime
		If perAlertTime>leftTime Or perAlertTime<=0 Then
			sleepTime=leftTime
		Else
			sleepTime=perAlertTime
		End If

		'leftTime
		leftTime=leftTime-sleepTime

		WScript.Sleep sleepTime*60000 '豪秒
	End If

Wend