<?php namespace Proper;

class ConfigurationException
extends \Exception
{
	public function __construct(Definition $property, $details = null)
	{
		$message = $property->getPropertyIdentifier() . ' is not configured correctly';
		
		if ($details !== null)
		{
			$message .= ': ' . $details;
		}
		
		parent::__construct($message);
	}
}