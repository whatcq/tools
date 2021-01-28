var $v = function (A) {
	return $(A).value
}
var $ = function (A) {
	return document.getElementById(A)
}
function toggle(id) {
	$(id).style.display = $(id).style.display != 'none' ? 'none' : 'block';
}
// toggle obj value
function x(objPath, val1, val2) {
	var o = objPath.split('.')
		, val = $(o[0])[o[1]][o[2]];
	$(o[0])[o[1]][o[2]] = val != val1 ? val1 : val2;
}

// changeurl / go_frame
function g(evt) {
	var e = evt || window.event
		, o = e.srcElement || e.target
		, old_id = $v("search_id")
		, new_id = o.id;
	if (old_id == new_id) return;

	$(old_id).className = '';
	$("url").value = o.getAttribute("value");
	$(new_id).className = 'hover';
	$("search_id").value = new_id;

	go_iframe();
}
//---------------
var prev;
function go_iframe() {
	var C = $v("text"), _prev = C;
	if (C.length == 0) {
		//alert("请输入搜索关键字");
		return;
	}
	document.title = '☺搜:' + C
	var A = $v("url");
	if (A.length == 0) {
		alert("请在后台添加搜索代码");
		return;
	}
	var url = "";
	var loader = $("load");
	var frame = $("iframe");
	var opener = $("open");
	frame.style.display = "block";
	loader.style.display = "block";
	opener.style.display = "none";

	url = A.replace(/\{key\}/g, C);

	if ($($v("search_id")).getAttribute('target') == '_blank') {
		window.open(url);
		return;
	}

	opener.style.display = "block";
	loader.style.display = 'block';
	frame.src = url;
	mn();

	if ($('iframe2').style.display != 'none' && _prev != prev) {
		prev = _prev;
		$('iframe2').src = $v('url2').replace(/\{key\}/g, prev);
	}

}
//------------
function rel() { $("sidebar").style.height = document.body.clientHeight - 55 + "px"; }
window.onresize = rel;   //改变窗口大小时调整高度

function stateChangeIE(_frame) { //iframe载入时显示loading
	var loader = $("load");
	if (_frame.readyState == "interactive") {
		loader.style.display = "none";
	}
}
function stateChangeFirefox() {
	var loader = $("load");
	loader.style.display = "none";
}

//------------
if (window.attachEvent) {
	window.attachEvent("onload", documentReady);
	//alert("ie");
}
if (window.addEventListener) {
	//alert("ff");
	window.addEventListener("load", documentReady, false);
}
function documentReady() {
	var A;
	if (document.location.href.indexOf("?") > -1) {
		var B = document.location.href.split("?")[1];
		var D = B.split("&");
		for (var C = 0; C < D.length; C++) {
			if (D[C].indexOf("q=") > -1) {
				A = D[C].split("=")[1]
			}
		}
		if (A != "" && !!A) {
			$("text").value = decodeURIComponent(A.replace(/\+/g, "%20"))
		}
	}

	rel();
	// search if keyword setted
	if ($v("text").length != 0) { go_iframe(); }
	// bind iframe event
	//$('iframe').onreadystatechange=stateChangeIE($('iframe'));
	$('iframe').onload = stateChangeFirefox;
	// bind search engines
	var links = document.getElementById('sidebar').getElementsByTagName('a');
	for (var i = 0; i < links.length; i++) {
		links[i].addEventListener('click', function () {
			g();
		}, false)
	}
}
//---------------
//google auto suggestion
var f = $('text');

function s(o) {
	if (f.value) {
		o.href += "#" + escape(f.value)
	}
}
var qu = location.hash.replace('#', '');
if (qu) f.value = unescape(qu);

var h = $('sq'),
	l = m = 0,
	gd = {},
	gw = {},
	gx = $('jz'),
	sg = {};
gd = function (c) {
	var tl = c[1].length,
		ih = '';
	if (!tl) {
		h.style.display = 'none';
		return
	}
	if (tl > 9) tl = 9;
	for (var j = 0; j < tl; ++j) {
		try {
			ih += "<tr style=cursor:default onmouseover=style.background='#D4E2FF' onclick=\"f.value='" + c[1][j][0] + "';go_iframe()\" onmouseout=style.background='#fff'><td>" + c[1][j][0] + "</td></tr>"
		} catch (err) { }
	}
	h.innerHTML = "<table width=100% cellpadding=0 cellspacing=0>" + ih + "</table>";
	h.style.display = "block"
};

function mh() {
	if (!f.value) {
		h.style.display = 'none';
		return
	}
	if (m == 38 || m == 40) {
		l = h.firstChild.rows.length;
		m == 38 ? h._i == -1 ? h._i = l - 1 : h._i-- : h._i++;
		for (var i = 0; i < l; i++) h.firstChild.rows[i].style.background = "#fff";
		if (h._i >= 0 && h._i < l) with (h.firstChild.rows[h._i]) {
			style.background = "#D4E2FF";
			f.value = cells[0].innerHTML
		} else {
			f.value = h._r;
			h._i = -1
		}
	} else {
		h._i = -1;
		h._r = f.value;
		if (gx) gx.parentNode.removeChild(gx);
		sg = document.createElement('script');
		sg.charset = 'utf-8';
		sg.src = 'http://google.cn/complete/search?callback=gd&client=serp&hl=zh-CN&js=true&q=' + encodeURIComponent(f.value);
		document.body.appendChild(sg);
		gx = $('gz')
	}
}
function mn() {
	$('sq').style.display = 'none'
}
//---the end-----
