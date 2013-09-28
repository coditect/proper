<?php namespace Proper;

class NotWritableException
extends AccessException
{
	public function __construct(Definition $property)
	{
		$message = $property->getPropertyIdentifier() . ' is not writable';
		parent::__construct($property, $message);
	}
}