<?php

// 合并二进制文件
// copy /b *.ts xiao.mp4

PHP_SAPI === 'cli' or die('Usage: php other/rename.php D:/www/xyz');

$folder = isset($argv[1]) ? $argv[1] : '.';

foreach (glob("$folder/*.ts") as $file) {
	$newFile = preg_replace_callback('|seg-(\d+)-v1-a1\.ts|', function ($matches) {
		return sprintf('seg-%03d-v1-a1.ts', $matches[1]);
	}, $file);
	if ($file == $newFile) {
		continue;
	}

	rename($file, $newFile);
	echo $file, "\t", $newFile, "\n";
	// break;
}
