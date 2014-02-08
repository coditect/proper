<?php namespace Proper\Exception;

class NotFound
extends \Proper\Exception
{
	public function __construct(\Proper\Property $property)
	{
		$message = $property->getName() . ' does not exist';
		parent::__construct($property, $message);
	}
}