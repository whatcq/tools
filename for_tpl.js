/*
循环生成器 -- 将数据批量填入模板
by cqiu(gdaymate@126.com) 2011-1-15

办法：用两个换行分割开，0 th，1 td，2 tpl

'使用方法：
* 全部内容被引用！
* 三段：用三个连续换行（两空行）分割开，0 th（字段），1 td（数据），2 tpl（模板）
* 分隔符为第一段最后一个换行之后的内容！！！
example:=================start
level,ztime,lastTime,title,category,description,action
,


100,#2010-12-29 10:46#,100,"打卡","上班","扣钱！！！！！61977","cmd /c start D:\f1274326501.txt"
35,#2010-12-30 19:10#,10,"打牛奶","营养","增强抵抗力！","cmd /c start D:\f1274326501.txt"


<tr>
	<td>level</td>
	<td>ztime</td>
	<td>lastTime</td>
	<td>title</td>
	<td>category</td>
	<td>description</td>
	<td>action</td>
</tr>
example:=================end
'功能todo:加入循环中的$i
bug: 处理utf-8编码文件中文会乱码，ansi的不会（本文件是utf-8编码）
*/

//程序开始
var input = "";
while(!WScript.StdIn.AtEndOfStream)
{
   input += WScript.StdIn.ReadAll();
}

var js_source = input.replace(/^\s+/, '');
var formated_code='';
if (js_source) {
   formated_code =for_tpl(js_source);
}

WScript.Echo(js_source);
if(formated_code.length)
	WScript.Echo(formated_code);
else
	WScript.Echo('办法：用两个换行分割开，0 th，1 td，2 tpl');

function for_tpl(str){
	try
	{
		var tmps=str.split("\r\n\r\n\r\n");
		var splitChar=tmps[0].substring(tmps[0].lastIndexOf("\n")+1, tmps[0].length);
		if (!splitChar){
			splitChar=' ';//空格
		}else{
			tmps[0]=tmps[0].substr(0,tmps[0].length-splitChar.length);
		}
		var ths=tmps[0].split(splitChar);

		var tdls=tmps[1].split("\r\n");

		var products=new Array();

		for (i in tdls){
			var tds=tdls[i].split(splitChar);
			products[i]=tmps[2];
			for (j in ths){
				var re=new RegExp(ths[j],"gm");//i不区分大小写
				products[i]=products[i].replace(re,tds[j]);
			}
		}

		return products.join("\r\n");
	}
	catch (e)
	{
		return false;
	}
}