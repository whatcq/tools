'�汾���£�����HSHY2020 ������� https://www.bilibili.com/read/cv9115688 ������bilibili
'�ýű������ʶ�0.0.0.7
'����ʱ��2021.1.3
' ���⣺vbs��Ҫ����Ϊgb2312,����֧�����ģ��ʶ����ı�Ҳֻ����gb2312...

dim a,objFSO
a=inputbox("������Ҫ�ʶ���txt�ļ�·��","�ʶ�����0.0.0.7","")
Set objFSO = CreateObject("Scripting.FileSystemObject")
If objFSO.FileExists (a) Then
    ' CreateObject("SAPI.SpVoice").speak "�ļ�·������"
    ' CreateObject("SAPI.SpVoice").speak "�ļ���ʼ��"
    CreateObject("SAPI.SpVoice").speak "�ʶ���ʼ"
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
    CreateObject("SAPI.SpVoice").speak "�ļ����������������ļ�·����ȷ���ļ�����"
End If
