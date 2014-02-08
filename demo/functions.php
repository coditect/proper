<?php

define('CLI', PHP_SAPI === 'cli');


function demo($text, $statements)
{
	if (CLI) {
		echo PHP_EOL, wordwrap($text, OUTPUT_WIDTH), PHP_EOL, PHP_EOL;
	} else {
		echo "<p>$text</p><pre style='margin-bottom: 3em'>";
	}
	
	foreach ($statements as $statement)
	{
		tryIt($statement);
	}
	
	echo CLI ? PHP_EOL : '</pre>';
}


function tryIt($statement)
{
	echo '  ', str_pad($statement, CODE_COLUMN_WIDTH - 2);
	
	try
	{
		$result = eval('global $demo; return ' . $statement . ';');
		echo color(wrap(var_export($result, true)), 'green');
	}
	catch (Exception $e)
	{
		if ($e->getPrevious())
		{
			$e = $e->getPrevious();
		}
		
		echo color(wrap($e->getMessage()), 'red');
	}
	
	echo PHP_EOL;
}


function wrap($text, $indent = 0) {
	$text = wordwrap($text, OUTPUT_WIDTH - CODE_COLUMN_WIDTH, PHP_EOL);
	$indent = str_repeat(' ', CODE_COLUMN_WIDTH);
	return str_replace(PHP_EOL, PHP_EOL . $indent, $text);
}


function color($text, $color) {
	if (CLI) {
		$esc = chr(27);
		$code = $color === 'red' ? '31m' : '32m';
		return $esc . '[' . $code . $text . $esc . '[0m';
	} else {
		return "<span style='color: $color'>$text</span>";
	}
}