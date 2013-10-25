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
	
	
	/**
		Retrieves the parameters used by the constraint when testing values.
	**/
	public function getParameters()
	{
		return $this->parameters;
	}
	
	
	/**
		Sets the parameters used by the constraint when testing values.
	**/
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;
	}
	
	
	/**
		Retrieves the last value the constraint tested.
	**/
	public function getValue()
	{
		return $this->value;
	}
	
	
	/**
		Sets the value for the constraint to test.
	**/
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	
	/**
		Tests the value provided by setValue using the parameters provided by setParameters.
	**/
	abstract public function test();
	
	
	abstract public function getErrorMessage();
}