<?php namespace Proper\Exception;

class NotReadable
extends \Proper\Exception
{
	public function __construct(\Proper\Property $property)
	{
		$message = $property->getName() . ' is not readable';
		parent::__construct($property, null, $message);
	}
}