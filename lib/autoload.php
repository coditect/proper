<?php

spl_autoload_register(function($className) {

	if (substr($className, 0, 7) === 'Proper\\')
	{
		$file = str_replace('\\', '/', substr($className, 7)) . '.php';
		
		if (file_exists(__DIR__ . '/' . $file))
		{
			require($file);
		}
	}

});