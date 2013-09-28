<?php namespace Proper\Constraint;

abstract class AbstractConstraint
implements \Proper\Constraint
{
	protected $property;
	protected $parameters;
	protected $value;
	
	
	public function __construct(\Proper\Definition $property)
	{
		$this->property = $property;
	}
	
	public function getParameters()
	{
		return $this->parameters;
	}
	
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	abstract public function test();
	abstract public function getErrorMessage();
}