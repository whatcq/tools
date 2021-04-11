<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transition al.dtd">
<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7"/> 
<title>xxoo</title>
<link href="picasa/style/style.css?ver=2.8" rel="stylesheet">
<style type="text/css">
	.picasaImage {
		max-width: 200px;
		max-height: 200px;
		padding: 5px;
		display: block;
		border: 1px solid #f4f4f4;
		border-radius: 5px;
		margin: auto;
		-moz-box-shadow: 3px 3px 4px #AFAFAF;
    -webkit-box-shadow: 3px 3px 4px #AFAFAF;
    box-shadow: 3px 3px 4px #AFAFAF;
	}
	.img_wrapper {
		max-width: 1180px;
		margin: 3px auto;
		width: 900px;
	}
	.img_wrapper div{
		height: 220px;
		float: left;
		display: table;
		width: 220px\9;
		max-width: 210px;
		/*display:table-cell;
		text-align:center;
		vertical-align:middle;*/
		margin: 5px;
	}
	.img_wrapper div a.getimg{
		color: white;
	}
	.img_wrapper div a.extrnal{
		color: green;
		text-decoration:none;
		font-size: 10px;
	}
	div.pager {
		display: block;
		clear: both;
		float: none !important;
		margin: auto !important;
		width: 500px;
		max-width: 100%;
	}
	.pager a {
		display: block;
		float: left;
		padding: 2px 5px;
		margin: 2px;
		border: 1px solid #669900;
		font-weight: 700;
		font-family: Georgia;
	}

	#PV_Zoom {
		width: 130px;
	}
	#PicasaView{
		background: #000000;
	}
	#PV_Loading,
	#PV_Error,
	#PV_PerHint{
		top: 20px !important;
	}
</style>

<div id="" class="img_wrapper">
<?php
$n = 50;
$p = isset($_REQUEST['p'])?intval($_REQUEST['p']):1;
if ($p < 1)$p = 1;
$d = file('pic.txt');
$total = count($d);
$lastP = ceil($total / $n);
if ($p > $lastP)$p = $lastP;

$shit_domains = file_get_contents('shit_domain.txt');
//$shit_domains = implode('|', array_unique(explode('|', $shit_domains)));
//file_put_contents('shit_domain.txt', $shit_domains);

for($start = $i = ($p-1) * $n, $end = min($start + $n, $total); $i < $end; $i++) {
	$item = json_decode($d[$i], true);

	// fix daolian tupian
	if(preg_match('#//('.$shit_domains.'xxx)/#i', $item['img'])){
		$item['img'] = "getimg.php?url={$item['img']}";
	}

	echo <<<PIC
		<div class="img-container">
		<img src="{$item['img']}" class="picasaImage" picasa="{$item['img']}" thumb="{$item['img']}" title="{$item['text']}" />
		<a href="getimg.php?url={$item['img']}" target="_blank" class="getimg">o</a>
		<a href="{$item['url']}" target="_blank" class="extrnal">{$item['title']}</a>
		<a href="http://shitu.baidu.com/n/pc_search?queryImageUrl={$item['img']}" target="_blank">…</a>
		</div>
PIC;
	echo '';
}

?>

<div class="pager">
<?php
if($p > 1) {
	if($p > 2) {
		echo '<a href="?p=1">&laquo;</a>';
	}
	echo '<a href="?p='.($p-1).'">&lsaquo;</a>';
}
$l = 2;
foreach(range(max($p-$l, 1), min($p+$l, $lastP)) as $page) {
	echo '<a href="?p='.($page).'">'.$page.'</a>';
}
if($p < $lastP) {
	echo '<a href="?p='.($p+1).'">&rsaquo;</a>';
	if($p < $lastP-1) {
		echo '<a href="?p='.($lastP).'">&raquo;</a>';
	}
}

?>
</div>

</div>

<script language="javascript" src="/test/KODExplorer/static/js/lib/jquery-1.8.0.min.js?ver=2.8"></script>
<script language="javascript" src="picasa/picasa.js?ver=2.8"></script>
<script language="javascript" src=""></script>

<script language="javascript">
/*
picasa 需要参数 attr (picasa, thumb)!!!
*/
$(function(){
	//ajax后重置数据、重新绑定事件(f5或者list更换后重新绑定)
	MyPicasa = new Picasa();
	PicasaOpen = false; //全局变量，用于标记是否有幻灯片播放
	MyPicasa.init(".picasaImage");
	MyPicasa.initData();

	$('img').click(function(){
		MyPicasa.play($(this));
	});

});
</script>