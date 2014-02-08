<?php namespace Proper;

class Exception
extends \Exception
{
	protected $property;
	protected $filter;
	
	public function __construct(Property $property, Filter $filter = null, $message, $previous = null)
	{
		parent::__construct($message, null, $previous);
		$this->property = $property;
		$this->filter = $filter;
	}
}