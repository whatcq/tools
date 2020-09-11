<?php

// 合并二进制文件
// php D:\www\cqiu\cqiu-tools\other\rename.php . & copy /b *.ts %cd%.mp4

PHP_SAPI === 'cli' or die('Usage: php other/rename.php D:/www/xyz');

$folder = isset($argv[1]) ? $argv[1] : '.';

echo $folder;

foreach (glob("$folder/*.ts") as $file) {
	$newFile = preg_replace_callback('|seg-(\d+)(-f\d)?-v1-a1\.ts|', function ($matches) {
		return sprintf('seg-%03d-v1-a1.ts', $matches[1]);
	}, $file);
	if ($file == $newFile) {
		continue;
	}

	rename($file, $newFile);
	echo $file, "\t", $newFile, "\n";
	// break;
}
