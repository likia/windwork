<?php
/**
 * 从Markdown生成html帮助文档的工具
 */

require dirname(dirname(__DIR__)).'/src/libs/parsedown/Parsedown.php';
global $manualDir;

$dir = 'manual/';
$manualDir = __DIR__ . '/../manual';

clearDirs($manualDir);
cpDir("{$dir}res/", "{$manualDir}/res/");

if(!$d = dir($dir)) {
	return;
}

while (false !== ($entry = @$d->read())) {
	if($entry == '.' || $entry == '..') {
		continue;
	}

	$file = $dir.$entry;
	if (is_file($file) && stripos($file, '.md')) {
		parser($file);
	}
}
	
@$d->close();

function parser($file) {
	global $manualDir;
	$text = file_get_contents($file);
	$fileName = $manualDir . '/'. substr(basename($file), 0, -3).'.html';
	$title = trim(preg_replace("/(.*?)\n.+/s", "\\1", trim($text)));
    $parsedown = new Parsedown();
	$html = $parsedown->text($text);
	$html = '<!DOCTYPE html>
<html>
  <head>
  <title>'.$title.'</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="res/style.css" />
<body>'."\n".$html."\n</body>\n</html>";

	print "make {$fileName}\n";
	file_put_contents($fileName, $html);
}

// 处理首页链接
$indexFile = "{$manualDir}/index.html";
$indexContent = file_get_contents($indexFile);
preg_match_all("/<a.*?href=\"(.+?)\".*?>/i", $indexContent, $m);
$links = array_unique($m[1]);
foreach($links as $page) {
	if (stripos($page, '#')) {
		continue;
	}
	$uri = "{$manualDir}/{$page}";
	if(!is_file($uri)) {
		$indexContent = str_replace("\"$page\"", "\"$page\" class=\"red\" onclick=\"return false;\"", $indexContent);
	}
}

file_put_contents($indexFile, $indexContent);

print "finish";


/**
 * 删除文件夹的全部内容
 *
 * @param string $dir 目录
 */
function clearDirs($dir) {
	if(!$dir || !$d = dir($dir)) {
		return;
	}

	while (false !== ($entry = @$d->read())) {
		if($entry[0] == '.') {
			continue;
		}
		if (is_dir($dir.'/'.$entry)) {
			clearDirs($dir.'/'.$entry, true);
		} else {
			@unlink($dir.'/'.$entry);
		}
	}
		
	@$d->close();
}

function cpDir($srcDir, $distDir) {
	if(!$srcDir || !$d = dir($srcDir)) {
		return;
	}

	$srcDir = rtrim($srcDir, '/\\') . '/';
	$distDir = rtrim($distDir, '/\\') . '/';
	
	if(!is_dir($distDir)) {
		@mkdir($distDir, true);
		print "mkdir $distDir\n";
	}
	
	while (false !== ($entry = @$d->read())) {
		if($entry[0] == '.') {
			continue;
		}
		if (is_dir($srcDir.$entry)) {
			cpDir($srcDir.$entry, $distDir.$entry);
		} else {
			copy($srcDir.$entry, $distDir.$entry);
		    print "copy {$srcDir}{$entry}\n";
		}
	}
		
	@$d->close();
}