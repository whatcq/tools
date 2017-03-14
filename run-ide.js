// NetBeans
//var editor = '"C:\\Program Files\\NetBeans 6.9.1\\bin\\netbeans.exe" "%file%:%line%" --console suppress';

// PHPEd
//~ var editor = '"C:\\Program Files\\NuSphere\\PhpED\\phped.exe" "%file%" --line=%line%';

// PhpStorm
var editor = '"D:\\Program Files (x86)\\JetBrains\\PhpStorm 10.0.1\\bin\\PhpStorm.exe" --line %line% "%file%"';

// SciTE
//~ var editor = '"C:\\Program Files\\SciTE\\scite.exe" "-open:%file%" -goto:%line%';

// EmEditor
//~ var editor = '"C:\\Program Files\\EmEditor\\EmEditor.exe" "%file%" /l %line%';

// PSPad Editor
//~ var editor = '"C:\\Program Files\\PSPad editor\\PSPad.exe" -%line% "%file%"';

// gVim
//~ var editor = '"C:\\Program Files\\Vim\\vim73\\gvim.exe" "%file%" +%line%';

var url = WScript.Arguments(0);
	//alert(url);
	var myMsgBox=new ActiveXObject("wscript.shell")
//myMsgBox.Popup (url)

//like @see: yii\debug\Module::DEFAULT_IDE_TRACELINE
var match = /^ide:\/\/open\/?\?url=file:\/\/(.+)&line=(\d+)$/.exec(url);//!!!!!!!!!!!!
if (match) {
    var file = decodeURIComponent(match[1]).replace(/\+/g, ' ');
	//myMsgBox.Popup (file)
    var command = editor.replace(/%line%/g, match[2]).replace(/%file%/g, file);
    var shell = new ActiveXObject("WScript.Shell");
    //shell.Exec(command.replace(/\\/g, '\\\\'));
    shell.Run(command.replace(/\\/g, '\\\\'), 3, false);
}