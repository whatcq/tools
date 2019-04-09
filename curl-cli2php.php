<?php

#php cli2php.php curl 'https://github.com/nemanjan00/curl-cli2php/blob/master/cli2php.php' -H 'Accept-Encoding: gzip, deflate, sdch' -H 'Accept-Language: en-US,en;q=0.8' -H 'Upgrade-Insecure-Requests: 1' -H 'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36' -H 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8' -H 'Referer: https://github.com/new' -H 'Connection: keep-alive' -H 'Cache-Control: max-age=0' --compressed
<<<EOF
<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://github.com/nemanjan00/curl-cli2php/blob/master/cli2php.php");

$headers = [
	"Accept-Encoding: gzip, deflate, sdch",
	"Accept-Language: en-US,en;q=0.8",
	"Upgrade-Insecure-Requests: 1",
	"User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36",
	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
	"Referer: https://github.com/new",
	"Connection: keep-alive",
	"Cache-Control: max-age=0",
];

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);
curl_close($ch);

echo $server_output;
EOF;

//---------------------

$state = 0;
$url = "";
$headers = [];
$compressed = false;
$data = false;
for ($i = 0; $i < count($argv); $i++) {
    if ($state == 0 && $argv[$i] == "curl") {
        $state = 1;
    } else if ($state == 1) {
        $url = $argv[$i];

        $state = 2;
    } else if ($state == 2) {
        if ($argv[$i] == "-H") {
            $state = 3;
        } else if ($argv[$i] == "--data") {
            $state = 4;
        } else if ($argv[$i] == "--compressed") {
            $compressed = true;
        } else {
            echo "Error...  {$argv[$i]} \n";
        }
    } else if ($state == 3) {
        $headers[] = $argv[$i];
        $state = 2;
    } else if ($state == 4) {
        $data = $argv[$i];
        $state = 2;
    }
}

$output = "<?php\n";
$output .= "\$ch = curl_init();\n";
$output .= "curl_setopt(\$ch, CURLOPT_URL, \"$url\");\n\n";
if (count($headers) > 0) {
    $output .= "\$headers = [\n";
    foreach ($headers as $header) {
        $output .= "\t\"$header\",\n";
    }
    $output .= "];\n\n";
    $output .= "curl_setopt(\$ch, CURLOPT_HTTPHEADER, \$headers);\n";
}
if ($data !== false) {
    $output .= "curl_setopt(\$ch, CURLOPT_POST, 1);\n";
    $output .= "curl_setopt(\$ch, CURLOPT_POSTFIELDS, \"$data\");\n";
}
if ($compressed) {
    $output .= "curl_setopt(\$ch, CURLOPT_ENCODING, '');\n";
}
$output .= "curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);\n\n";
$output .= "\$server_output = curl_exec (\$ch);\n";
$output .= "curl_close(\$ch);\n\n";
$output .= "echo \$server_output; \n";
echo $output;
