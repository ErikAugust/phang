<?php
require_once 'base_include.php';

echo $argv[1] . "\r\n";

generateRoutes('Nothing');


function generateRoutes($model_name) {
	$search      = "// ROUTING BEGINS";
	$lines       = file('../web/app.php');
	$line_number = false;

	while (list($key, $line) = each($lines) and !$line_number) {
   		$line_number = (strpos($line, $search) !== FALSE) ? $key + 1 : $line_number;
	}
	$append_line = $line_number + 2;

	$fp = fopen('../web/app.php', 'a');
	fseek($fp, $append_line);
	fwrite($fp, 'Example addition');
	fclose($fp);

}
