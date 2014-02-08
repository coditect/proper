<?php namespace Proper\Exception;

use \Exception;
use \Proper\Filter;
use \Proper\Property;


class Configuration
extends \Proper\Exception
{
	public function __construct(Property $property, Filter $filter = null, Exception $previous = null)
	{
		$message = $property->getName() . ' is not properly configured';
		
		if ($filter)
		{
			$message = 'Filter ' . get_class($filter) . ' of ' . $message;
		}
		
		parent::__construct($property, $filter, $message, $previous);
	}
}