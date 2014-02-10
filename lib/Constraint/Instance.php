<?php namespace Proper\Constraint;

use \Exception;
use \Proper\Constraint;


/**
	The Instance constraint checks that an object is an instance of a class or interface.
**/
class Instance
implements Constraint
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
		Initializes the constraint with an object containing the following keys:
		- **class**: The fully-qualfied name of the expected class or interface.
		- **null**: Whether or not to permit null values.  Defaults to `false`.
		
		@param   object $rules  The constraint rules.
		@throws  Exception      When the given class is not defined.
	**/
	public function __construct($rules)
	{
		preg_match('/(\S+)(.*)/', $rules, $matches);
		
		if (isset($matches[1]) && class_exists($matches[1]))
		{
			$this->className = $matches[1];
		}
		else
		{
			throw new Exception("Class {$matches[1]} is not defined");
		}
		
		if (isset($matches[2]) && strtolower(trim($matches[2])) === 'or null')
		{
			$this->allowNull = true;
		}
		
		print_r($this);
	}
	
	
	/**
		Validates the class of an object.
		
		@param   object $value  The object  to be validated.
		@return  object         The given value, if it satisfies the constraint's requirements.
		@throws  Exception      When the given value is not an object or is not an instance of the expected class or interface.
	**/
	public function apply($value)
	{
		$message = "Expecting an instance of {$this->className}, ";
		
		if (!$this->allowNull && is_null($value))
		{
			return $message . 'null given';
		}
		else if (!is_object($value))
		{
			return $message . 'non-object given';
		}
		else if (!($value instanceof $this->className))
		{
			return $message . get_class($value) . ' given';
		}
	}
}