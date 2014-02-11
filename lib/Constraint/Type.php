<?php namespace Proper\Constraint;

use \Exception;
use \Proper\Constraint;


/**
	The Type constraint validates that a given value is of a specified type.
	
	Valid types include:
		- array
		- bool
		- boolean
		- callable
		- double
		- float
		- int
		- integer
		- long
		- null
		- numeric
		- object
		- real
		- resource
		- scalar
		- string
**/
class Type
implements Constraint
{
	/**
		The list of acceptable types.
		
		@var string[]
	**/
	protected $types = array();
	
	
	/**
		Initializes the constraint with a list of acceptable types.
		
		@param   string $types  The list of types.
		@throws  Exception      When one of the listed types is not valid.
	**/
	public function __construct($types)
	{
		foreach (preg_split('/\W+/', trim($types)) as $type)
		{
			$normalized_type = strtolower($type);
			$check_function = 'is_' . $normalized_type;
	
			if (!function_exists($check_function))
			{
				if ($check_function === 'is_str')
				{
					$normalized_type = 'string';
				}
				else if ($check_function === 'is_boolean')
				{
					$normalized_type = 'bool';
				}
				else
				{
					throw new Exception(var_export($type, true) . ' is not a valid type');
				}
			}
		
			$this->types[] = $normalized_type;
		}
	}
	
	
	/**
		Checks that the given value is of an acceptable type.
		
		@param   mixed $value  The value to be validated.
		@return  string        An error message, if the value is not acceptable.
	**/
	public function apply($value)
	{
		if (count($this->types) > 0)
		{
			foreach ($this->types as $type)
			{
				$function = 'is_' . $type;
				
				if ($function($value))
				{
					return;
				}
			}
			
			// Construct error message
			$message = 'Expecting type ';
			$numTypes = count($this->types);
			$givenType = gettype($value);
			
			if ($givenType === 'double')
			{
				$givenType = 'float';
			}
			else if (is_callable($value))
			{
				$givenType = 'callable';
			}
			
			if ($numTypes > 2)
			{
				$message .= implode(', ', array_slice($this->types, 0, -1));
				$message .= ', or ' . $this->types[$numTypes - 1];
			}
			else
			{
				$message .= $this->types[0];
			
				if ($numTypes === 2)
				{
					$message .= ' or ' . $this->types[1];
				}
			}
			
			$message .= ", $givenType given";
			return $message;
		}
	}
}