<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


class Length
implements Filter
{
	protected $min;
	protected $max;
	
	
	public function __construct($params)
	{
		if (isset($params->min))
		{
			if (is_int($params->min) && $params->min >= 0)
			{
				$this->min = $params->min;
			}
			else
			{
				throw new Exception('Minimum length must be a nonnegative integer');
			}
		}
		
		if (isset($params->max))
		{
			if (is_int($params->max) && $params->max >= 0)
			{
				$this->max = $params->max;
			}
			else
			{
				throw new Exception('Maximum length must be a nonnegative integer');
			}
		}
	}
	
	
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