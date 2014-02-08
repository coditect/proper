<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


class Regex
implements Filter
{
	protected $pattern;
	
	
	public function __construct($pattern)
	{
		if (preg_match($pattern, '') !== false)
		{
			$this->pattern = $pattern;
		}
		else
		{
			throw new Exception(var_export($pattern, true) . ' is not a valid regular expression');
		}
	}
	
	
	public function apply($value)
	{
		if (preg_match($this->pattern, $value) === 0)
		{
			$pattern = var_export($this->pattern, true);
			$value = var_export($value, true);
			throw new Exception("$value does not match the regular expression $pattern");
		}
		
		return $value;
	}
}