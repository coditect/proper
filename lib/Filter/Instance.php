<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


class Instance
implements Filter
{
	protected $className;
	protected $allowNull;
	
	
	public function __construct($options)
	{
		if (class_exists($options->class))
		{
			$this->className = $options->class;
		}
		else
		{
			throw new Exception("Class {$options->class} is not defined");
		}
		
		$this->allowNull = isset($options->null) && (bool) $options->null;
	}
	
	
	public function apply($value)
	{
		if (($this->allowNull && is_null($value)) || (is_object($value) && $value instanceof $this->className))
		{
			return $value;
		}
		else
		{
			$given_class = is_object($value) ? get_class($value) : 'non-object';
			throw new Exception("Expecting an instance of {$this->className}, $given_class given");
		}
	}
}