<?php namespace Proper\Constraint;

use \Exception;
use \Proper\Constraint;


/**
	The Regex constraint validates that a given string matches or does not match a regular expression.
**/
class Regex
implements Constraint
{
	/**
		The regular expression to check against.
		
		@var string
	**/
	protected $pattern;
	
	
	/**
		Whether to accept only those strings that match the pattern, or only those that do not.
		
		@var boolean
	**/
	protected $shouldMatch = true;
	
	
	/**
		Initializes the constraint with a string containing a Perl-compatible regular expression.  The regular expression may be prefixed with a "!" to indicate that only strings that do **not** match the pattern should be considered valid.
		
		@param   string $pattern  The regular expression to check against.
		@throws  Exception        When the regular expression is not valid.
	**/
	public function __construct($pattern)
	{
		if ($pattern[0] === '!')
		{
			$this->shouldMatch = false;
			$pattern = substr($pattern, 1);
		}
		
		if (preg_match($pattern, '') !== false)
		{
			$this->pattern = $pattern;
		}
		else
		{
			throw new Exception(var_export($pattern, true) . ' is not a valid regular expression');
		}
	}
	
	
	/**
		Validates the given string.
		
		@param   string $value  The string to be validated.
		@return  string         An error message, if the string is invalid.
	**/
	public function apply($value)
	{
		$matches = preg_match($this->pattern, $value);
		
		if ($this->shouldMatch && $matches === 0)
		{
			$pattern = var_export($this->pattern, true);
			$value = var_export($value, true);
			return "$value does not match the regular expression $pattern";
		}
		
		else if (!$this->shouldMatch && $matches === 1)
		{
			$pattern = var_export($this->pattern, true);
			$value = var_export($value, true);
			return "$value must not match the regular expression $pattern";
		}
	}
}