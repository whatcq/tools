'�����ļ����������ļ��ŵ�ͬһ�ļ��С�
'���磺AAAA (2).jpg,AAAA (5).jpg => AAAA/..
'Cqiu@2021/3/5

If WScript.Arguments.Count<1 Then
	MsgBox "�÷������ļ����Ϸŵ��������ϡ�",vbOKOnly,"�ļ����๤��"
	WScript.Quit
End If

Dim i
Path= wscript.arguments(0)
msgbox "�ļ����࿪ʼ��" & Path


Function CreateFolderEx(fso,path)
	If fso.FolderExists(path) Then 
		Exit Function
	End If
	fso.CreateFolder(path)
End Function

Function ReplaceTest(str, patrn, replStr)
	Dim regEx, str1
	Set regEx = New RegExp
	regEx.Pattern = patrn
	regEx.IgnoreCase = True
	ReplaceTest = regEx.Replace(str, replStr)    
End Function


set fso=CreateObject("Scripting.FileSystemObject")
set objFolder=fso.GetFolder(Path)

set objFiles=objFolder.Files
for each objFile in objFiles
	If LCase(Right(objFile, 4))=".jpg" Or LCase(Right(objFile, 5))=".jpeg" Then
		subFolder = Path &"\"& RTrim(ReplaceTest(objFile.name, "\(.*", ""))
		CreateFolderEx fso,subFolder


		fso.movefile objFile, subFolder&"\"&objFile.name
	End If
Next

i=100
set objFolders=objFolder.Subfolders
for each objFile in objFolders
	If Left(objFile.name, 1)<>"(" Then
		i=i+1
		fso.MoveFolder  objFile, Path&"\("&i&")"&objFile.name
	End If
Next


set objFolder=nothing
set fso=nothing


msgbox "���!"

