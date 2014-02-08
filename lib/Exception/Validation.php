<?php namespace Proper\Exception;

use \Exception;
use \Proper\Filter;
use \Proper\Property;


class Validation
extends \Proper\Exception
{
	public function __construct(Property $property, Filter $filter, Exception $previous)
	{
		$filterName = get_class($filter);
		$propertyName = $property->getName();
		$message = "Invalid input for filter $filterName of $propertyName";
		parent::__construct($property, $filter, $message, $previous);
	}
}