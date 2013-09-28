<?php namespace Proper;

class AccessException
extends \Exception
{
	protected $property;
	
	public function __construct(Definition $property, $message)
	{
		parent::__construct($message);
		$this->property = $property;
	}
}