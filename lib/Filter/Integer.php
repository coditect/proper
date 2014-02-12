<?php namespace Proper\Filter;


/**
	The Integer filter converts all input into integers.
**/
class Integer
implements \Proper\Filter
{
	protected $radix = 0;
	
	public function __construct($radix)
	{
		if (preg_match('/\d+/', $radix, $match) === 1)
		{
			$this->radix = intval($match[0]);
		}
	}
	
	public function apply($value)
	{
		return intval($value, $this->radix);
	}
}