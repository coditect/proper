<?php namespace Proper;

class NotReadableException
extends AccessException
{
	public function __construct(Definition $property)
	{
		$message = $property->getPropertyIdentifier() . ' is not readable';
		parent::__construct($property, $message);
	}
}