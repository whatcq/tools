'=======alert.vbs========
'thisTask= wscript.arguments(0)
'leftTime= wscript.arguments(1)
'Dim br
'br= Chr(13) & Chr(10) & Chr(13) & Chr(10) & "===================" & Chr(13) & Chr(10) & Chr(13) & Chr(10)
'MsgBox  br & thisTask & Chr(9) &  "剩余:"&leftTime&"min"  & br ,vbOKOnly,Time & ": working..."
'=======/alert.vbs========

'important!!!注意运行时需要将本文件保存为ANSI编码
'
'一个简单的提醒脚本（like番茄工作法） by Cqiu @2010-1-12
'
'2012-1-14 Cqiu
'+时间可配置
'+分月建数据
'+任务中提醒(新进程:2013-5-23)
'TODO:+提醒方式：声音、...写消息的方式...
'这种死循环还有点考人呢！2013-5-22
'+消息队列 2017-9-27

'==============================
'配置
'==============================
Const ForReading = 1, ForWriting = 2, ForAppending = 8

'数据文件
Dim dataFile, msgFile
dataFile=".\doing\todayWorks_"&Year(Date())&"_"&Month(Date())&".txt"
msgFile=".\msg.txt"
'输入格式："task*time perAlertTime b4ExpireAlertTime"
Dim SC,SC1 'splitChar
SC="*"
SC1=" "


'==============================
'主体程序
'==============================
Dim taskTime,perAlertTime,b4ExpireAlertTime,thisTask,leftTime, defaultTask,newTask,status,br,sleepTime,overTime
taskTime=20 ' 1个番茄时间
perAlertTime=8
b4ExpireAlertTime=5 '结束前多少分最后一次提示
leftTime=0
overTime=Time
defaultTask="before die"
status="===========" '新开始
br= Chr(13) & Chr(10) & Chr(13) & Chr(10) & "===================" & Chr(13) & Chr(10) & Chr(13) & Chr(10)

' this adds the IIf() function to VBScript
Function IIf(i,j,k)
  If i Then IIf = j Else IIf = k
End Function

function Is_Int(a_str)
   if not isnumeric(a_str) or len(str) > 5 then
      Is_Int = false
      exit function
   elseif len(str) < 5 then
      Is_Int = true
      exit function
   end if
   if cint(left(a_str , 4)) > 3276 then
      Is_Int = false
      exit function
   elseif cint(left(a_str , 4)) = 3276 and cint(right(a_str , 1)) > 7 then
      Is_Int = false
      exit function
   else
      Is_Int = true
      exit function
   end if
end function

Function write(dataFile, content)
	Set fso=WScript.createobject("Scripting.FileSystemObject")
	Set f=fso.openTextFile(dataFile,ForAppending,true)
	f.writeLine content
	f.Close
	Set fso=Nothing
End Function

Dim taskInfo,timeSet

'开启监护进程
Set WshShell = WScript.CreateObject("WScript.Shell") 
WshShell.Run "wscript deamon.vbs"
Set WshShell = Nothing

While True

	'提醒
	If leftTime>0 Then
		'提醒是单独的，调用其他进程而不用MsgBox，以免中断计时
		Set WshShell = WScript.CreateObject("WScript.Shell") 
		WshShell.Run "wscript alert.vbs """& thisTask & IIf(sleepTime = leftTime,"***","") &""" """& leftTime &""""
		Set WshShell = Nothing
		status="working"
	'没有任务则创建
	ElseIf thisTask="" Then
		write msgFile, 1
		newTask=InputBox("下一个`番茄`是：", Time & "-下一个番茄",_
			defaultTask &SC& taskTime &SC1& perAlertTime & IIf(b4ExpireAlertTime>0,SC1& b4ExpireAlertTime,""))
		write msgFile, -1
		'输入为空 或 点击取消 直接退出
		If newTask="" Then
			WScript.Quit
		End If
		taskInfo=Split(newTask,SC)
		thisTask=taskInfo(0)

		If UBound(taskInfo)>0 Then
			If taskInfo(1)<>"" Then
				timeSet=Split(taskInfo(1),SC1)

				If Is_Int(timeSet(0)) Then
					taskTime=CInt(timeSet(0))
				End If

				If UBound(timeSet)>0 Then
					If Is_Int(timeSet(1)) Then
						perAlertTime=CInt(timeSet(1))
					End If
				End If

				If UBound(timeSet)>1 Then
					If Is_Int(timeSet(2)) Then
						b4ExpireAlertTime=CInt(timeSet(2))
					End If
				End If

			End If
		End If
		
		leftTime=taskTime
	'完成结果
	Else
		write msgFile, 2
		status=MsgBox ("是否已完成？" & br & thisTask & br,vbYesNoCancel,Time & "上一任务：")
		write msgFile, -2
		Select Case status
			Case vbYes
				status="已完成"
				defaultTask=thisTask
			Case vbNo
				status="未完成"
				defaultTask=thisTask
			Case Else 'vbCancel
				status="已取消"
				defaultTask="锻炼/休息"
		End Select
		leftTime=0
		thisTask=""
		overTime=Time
	End If

	If thisTask<>"" Then
		'保存新任务
		If status<>"working" Then
			Set fso=WScript.createobject("scripting.filesystemobject")
			Set f=fso.openTextFile(dataFile,ForAppending,true)
			f.writeLine SC & status & SC & overTime
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
		'b4ExpireAlertTime overwrite sleepTime
		If taskTime>b4ExpireAlertTime And b4ExpireAlertTime>0 Then
			If leftTime<=b4ExpireAlertTime Then
				sleepTime = leftTime
			ElseIf leftTime>b4ExpireAlertTime And leftTime-b4ExpireAlertTime <= sleepTime Then
				sleepTime = leftTime-b4ExpireAlertTime
			End If
		End If

		'leftTime
		leftTime=leftTime-sleepTime

		WScript.Sleep sleepTime*60000 '豪秒
	End If

Wend