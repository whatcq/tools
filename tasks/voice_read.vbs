'版本更新，作者HSHY2020 摸鱼卷王 https://www.bilibili.com/read/cv9115688 出处：bilibili
'用脚本进行朗读0.0.0.7
'创建时间2021.1.3
' 问题：vbs需要保存为gb2312,否则不支持中文；朗读的文本也只能是gb2312...

dim a,objFSO
a=inputbox("请输入要朗读的txt文件路径","朗读工具0.0.0.7","")
Set objFSO = CreateObject("Scripting.FileSystemObject")
If objFSO.FileExists (a) Then
    ' CreateObject("SAPI.SpVoice").speak "文件路径存在"
    ' CreateObject("SAPI.SpVoice").speak "文件初始化"
    CreateObject("SAPI.SpVoice").speak "朗读开始"
    '///////////////////////////////////////
    Const ForReading = 1
    Dim message

    Dim fs, ts
    set fs = CreateObject("Scripting.FileSystemObject")
    set ts = fs.OpenTextFile(a, ForReading)
    Do Until ts.AtEndOfStream
        message = ts.ReadLine
        ' msgbox message
        if message <> "" Then
            CreateObject("SAPI.SpVoice").speak message
        End if
    Loop
    ts.Close
    set ts = Nothing
    set fs = Nothing
'///////////////////////////////////////
Else
    CreateObject("SAPI.SpVoice").speak "文件错误请重新载入文件路径以确保文件存在"
End If
