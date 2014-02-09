<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


/**
	The Instance filter checks that an object is an instance of a class or interface.
**/
class Instance
implements Filter
{
	/**
		The fully-qualfied name of the class or interface.
		
		@var string
	**/
	protected $className;
	
	
	/**
		Whether or not to permit null values.
		
		@var boolean
	**/
	protected $allowNull = false;
	
	
	/**
		Initializes the filter with an object containing the following keys:
		- **class**: The fully-qualfied name of the expected class or interface.
		- **null**: Whether or not to permit null values.  Defaults to `false`.
		
		@param   object $rules  The filter rules.
		@throws  Exception      When the given class is not defined.
	**/
	public function __construct($rules)
	{
		if (class_exists($rules->class))
		{
			$this->className = $rules->class;
		}
		else
		{
			throw new Exception("Class {$rules->class} is not defined");
		}
		
		if (isset($rules->null))
		{
			$this->allowNull =  (bool) $rules->null;
		}
	}
	
	
	/**
		Validates the class of an object.
		
		@param   object $value  The object  to be validated.
		@return  object         The given value, if it satisfies the filter's requirements.
		@throws  Exception      When the given value is not an object or is not an instance of the expected class or interface.
	**/
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