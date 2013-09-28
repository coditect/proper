<?php

spl_autoload_register(function($className) {

	if (substr($className, 0, 7) === 'Proper\\')
	{
		require(str_replace('\\', '/', substr($className, 7)) . '.php');
	}

});