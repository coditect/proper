<?php namespace Proper\Filter;

abstract class AbstractFilter
implements \Proper\Filter
{
	protected $property;
	
	
	public function __construct(\Proper\Definition $property, $options)
	{
		$this->property = $property;
		$this->init($options);
	}
	
	
	abstract protected function init($options);
	
	
	/**
		Tests the value provided by setValue using the parameters provided by setParameters.
	**/
	public function isValid($value)
	{
		return true;
	}
	
	
	public function transform($value)
	{
		return $value;
	}
	
	
	public function applyTo($value)
	{
		if ($this->isValid($value))
		{
			return $this->transform($value);
		}
		else
		{
			throw new \Exception($this->getError($value));
		}
	}
	
	
	abstract public function getError($value);
}