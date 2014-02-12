<?php namespace Proper;

class Exception
extends \Exception
{
	protected $property;
	protected $action;
	
	public function __construct(Property $property, Action $action = null, $message, $previous = null)
	{
		parent::__construct($message, null, $previous);
		$this->property = $property;
		$this->action = $action;
	}
}