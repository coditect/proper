<?php namespace Proper\Exception;

class NotWritable
extends \Proper\Exception
{
	public function __construct(\Proper\Property $property)
	{
		$message = $property->getName() . ' is not writable';
		parent::__construct($property, $message);
	}
}