<?php namespace Proper\Exception;

use \Exception;
use \Proper\Action;
use \Proper\Property;


class Configuration
extends \Proper\Exception
{
	public function __construct(Property $property, Action $action = null, Exception $previous = null)
	{
		$message = $property->getName() . ' is not properly configured';
		
		if ($action)
		{
			$message = get_class($action) . ' for ' . $message;
		}
		
		parent::__construct($property, $action, $message, $previous);
	}
}