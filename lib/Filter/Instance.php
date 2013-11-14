<?php namespace Proper\Filter;

class Instance
extends AbstractFilter
{
	protected $className;
	protected $allowNull;
	
	
	public function init($options)
	{
		if (class_exists($options->class, true))
		{
			$this->className = $options->class;
		}
		else
		{
			throw new \Proper\ConfigurationException($this->property, "Class $options is not defined");
		}
		
		$this->allowNull = isset($options->null) && (bool) $options->null;
	}
	
	
	public function isValid($value)
	{
		if ($this->allowNull && is_null($value))
		{
			return true;
		}
		else
		{
			return is_object($value) && $value instanceof $this->className;
		}
	}
	
	
	public function getError($value)
	{
		$property = $this->property->getPropertyIdentifier();
		$class = is_object($value) ? get_class($value) : 'non-object';
		return "$property must be an instance of {$this->className}, $class given";
	}
}