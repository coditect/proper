<?php namespace Proper\Exception;

use \Exception;
use \Proper\Constraint;
use \Proper\Property;


class Validation
extends \Proper\Exception
{
	public function __construct(Property $property, Constraint $constraint, $message)
	{
		$constraintName = get_class($constraint);
		$propertyName = $property->getName();
		$message = "Invalid input for $propertyName: $message ($constraintName)";
		parent::__construct($property, $constraint, $message);
	}
}