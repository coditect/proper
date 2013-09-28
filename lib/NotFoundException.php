<?php namespace Proper;

class NotFoundException
extends AccessException
{
	public function __construct(Definition $property)
	{
		$message = $property->getPropertyIdentifier() . ' does not exist';
		parent::__construct($property, $message);
	}
}