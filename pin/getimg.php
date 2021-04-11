<?php
@set_time_limit(20);
$allowgz = true;
//$action = $_GET['action'] ? $_GET['action'] : $_POST['action'];
//$self = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], '/') + 1, strlen($_SERVER['PHP_SELF']));
//$refer = $_SERVER['HTTP_REFERER'];
//$rarray = parse_url($refer);
//$rhost = strtolower($rarray['host']);
$lhost = strtolower($_SERVER['HTTP_HOST']);
isset($_REQUEST['url']) && $url = $_REQUEST['url'];
$url = !empty($url) ? $url : ($_SERVER['QUERY_STRING'] ? $_SERVER['QUERY_STRING'] : null);
$url = trim($url);
if (!$url) exit;
$url_d = urldecode($url);
$arrUrl = @parse_url($url_d);
if ($arrUrl['scheme'] != 'http' || $arrUrl['host'] == $lhost) {
	header("Location: $url");
	exit;
}
$ext = strtolower(substr($url, strrpos($url, '.') + 1));
if (!strstr(',jpg,bmp,gif,png,', ',' . $ext . ',')) {
	header("Location: $url");
	exit;
}

// record the domain
file_put_contents('shit_domain.txt', $arrUrl['host']."|", FILE_APPEND);

$url = urldecode($url);
$ret = fetchImg($url);
if ($ret['success']) {
header('Content-type: image/jpeg');
	echo $ret['content'];
	flush();
}else {
	header($url);
}
exit;
function unTomChunked($content) {
	$pos = strpos($content, "\x0d\x0a");
	if ($pos > 0 && $pos < 20) {
		$content = substr($content, $pos + 2);
	}
	$content = preg_replace("/\x0d\x0a[0-9a-z]+?\x0d\x0a/is", '', $content);
	if (substr($content, -2) == "\r\n") $content = substr($content, 0, strlen($content)-2);
	$content = str_replace("\r\n2000\r\n", '', $content);
	return $content;
}
function unGzip($content) {
	$singal = "\x1F\x8B\x08";
	$slen = strlen($singal);
	if (substr($content, 0, $slen) == $singal) {
		$content = substr($content, 10);
		$content = gzinflate($content);
	}
	return $content;
}
function unChunked($content) {
	$pos = strpos($content, "\x0d\x0a");
	if ($pos > 0 && $pos < 20) {
		$content = substr($content, $pos + 2);
	}
	$content = preg_replace("/\x0d\x0a[0-9a-f]+?\x0d\x0a/is", '', $content);
	if (substr($content, -2) == "\r\n") $content = substr($content, 0, strlen($content)-2);
	$content = str_replace("\r\n2000\r\n", '', $content);
	return $content;
}
function fetchImg($url, $nesting = false, $skipextchk = false) {
	global $allowgz, $allowexts, $timeout;
	$allowexts = $allowexts ? ',' . $allowexts . ',' : ',jpg,bmp,gif,png,';
	$timeout = intval($timeout);
	$timeout = $timeout ? $timeout : 7;
	@$arrUrl = parse_url($url);
	$disallowedext = false;
	$status = false;
	$ext = strtolower(substr($url, strrpos($url, '.') + 1));
	if ($skipextchk) {
		$ext = 'jpg';
	}
	if (!strstr($allowexts, ',' . $ext . ',')) {
		$disallowedext = true;
	}
	$headers = '';
	if (!$disallowedext && $arrUrl['scheme'] == 'http') {
		if (strstr(strtolower($arrUrl['host']), '.163.com') || strstr(strtolower($arrUrl['host']), '.piclib.net')) {
			$strRef = '';
		}else {
			$strRef = 'http://' . $arrUrl['host'] . '/';
		}
		$arrUrl['uri'] = ($arrUrl['path'] ? $arrUrl['path']:'') . (isset($arrUrl['query']) ? '?' . $arrUrl['query']:'') . (isset($arrUrl['fragment']) ? '#' . $arrUrl['fragment']:'');
		$arrUrl['port'] = isset($arrUrl['port']) ? $arrUrl['port'] : '80';
		$strRequest = "GET " . $arrUrl['uri'] . " HTTP/1.0\r\n";
		$strRequest .= "Host: " . $arrUrl['host'] . "\r\n";
		$strRequest .= "Accept: */*\r\n";
		if ($allowgz) {
			$strRequest .= "Accept-Encoding: gzip, deflate\r\n";
		}
		if ($strRef != '') {
			$strRequest .= "Referer:$strRef\r\n";
		}
		$strRequest .= "User-Agent: Mozilla/4.0 (compatible; MSIE 4.00; Windows 2000)\r\n";
		$strRequest .= "Pragma: no-cache\r\n";
		$strRequest .= "Cache-Control: no-cache\r\n";
		$strRequest .= "Connection: close\r\n\r\n";
		@$fp = fsockopen($arrUrl['host'], $arrUrl['port'], $intError, $strError, $timeout);
		if (!$fp) {
			return false;
		}
		@fwrite($fp, $strRequest);
		$bolHeader = true;
		$removed = false;
		$headers = '';
		while ($block = fgets($fp, 1024)) {
			if ($bolHeader) {
				if ($block == "\r\n") {
					$bolHeader = false;
				}
				$headers .= $block;
				if (!$removed && preg_match("/HTTP\/1\.\d?\s+?302\s+?.*?/is", $block)) {
					$removed = true;
				}
			}else {
				break;
			}
		}
		$content = $block;
		if (!$removed) {
			while ($block = fread($fp, 10240)) {
				$content .= $block;
			}
		}
		fclose($fp);
		$status = true;
	}
	if ((strstr($arrUrl['host'], '.tom.com') || strstr($arrUrl['host'], '.52vcd.com')) && !empty($content)) {
		unTomChunked($content);
		$unced = true;
	}
	$header_t = strtolower($headers);
	if ((strstr($header_t, ' chunked') || strstr($header_t, ':chunked')) && !$unced) {
		$content = unChunked($content);
	}
	if (strstr($header_t, ' gzip') || strstr($header_t, ':gzip') || substr($content, 0, 3) == "\x1f\x8b\x08") {
		$content = unGzip($content);
	}
	if (!$nesting && $removed) {
		if (preg_match("/Location:\s+?(.*?)(?=\r|\n|\s)/is", $headers, $match)) {
			$url = $match[1];
			return fetchImg($url, true, $skipextchk);
		}else {
			return false;
		}
	}
	if ($nesting) {
		return array('url' => $url, 'success' => $status, 'headers' => $headers, 'content' => $content);
	}else {
		return array('success' => $status, 'headers' => $headers, 'content' => $content);
	}
}