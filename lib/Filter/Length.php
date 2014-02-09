<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


/**
	The Length filter validates the length of strings, arrays, and countable objects.
	
	When using multibyte encodings (such as UTF-8), note that this filter counts the number of *characters* in a string, not the number of bytes.
**/
class Length
implements Filter
{
	/**
		The minimum acceptable length.
		
		@var integer
	**/
	protected $min;
	
	
	/**
		The maximum acceptable length.
		
		@var integer
	**/
	protected $max;
	
	
	/**
		Initializes the filter with an object containing one or both of the following keys:
		- **min**: The minimum acceptable length, as a nonnegative integer.
		- **max**: The maximum acceptable length, as a nonnegative integer.
		
		@param   object $rules  The miniumum and/or maxiumum length.
		@throws  Exception      When either the minimum or maxiumum length is not a nonnegative integer.
		@throws  Exception      When the maximum length is less than the minimum.
	**/
	public function __construct($rules)
	{
		if (isset($rules->min))
		{
			if (is_int($rules->min) && $rules->min >= 0)
			{
				$this->min = $rules->min;
			}
			else
			{
				throw new Exception('Minimum length must be a nonnegative integer');
			}
		}
		
		if (isset($rules->max))
		{
			if (is_int($rules->max) && $rules->max >= 0)
			{
				if (!is_null($this->min) && $rules->max >= $this->min)
				{
					$this->max = $rules->max;
				}
				else
				{
					throw new Exception('Maximum length must be greater than minimum length');
				}
			}
			else
			{
				throw new Exception('Maximum length must be a nonnegative integer');
			}
		}
	}
	
	
	/**
		Validates the length of the given string, array, or countable object.
		
		@param   string|array|Countable  The string, array, or object  to be validated.
		@return  string|array|Countable  The given value, if it satisfies the filter's requirements.
		@throws  Exception               When the given value is shorter than the miminum length or longer than the maximum length.
	**/
	public function apply($value)
	{
		if (is_array($value) || (is_object($value) && $value instanceof \Countable))
		{
			$length = count($value);
		}
		else
		{
			$length = mb_strlen((string) $value);
		}
		
		if (!is_null($this->min) && $length < $this->min) 
		{
			throw new Exception(var_export($value, true) . ' is shorter than the minimum length of ' . $this->min);
		}
		else if (!is_null($this->max) && $length > $this->max)
		{
			throw new Exception(var_export($value, true) . ' is longer than the maximum length of ' . $this->max);
		}
		
		return $value;
	}
}