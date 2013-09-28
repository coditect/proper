<?php namespace Proper\Filter;

class Integer
implements \Proper\Filter
{
	protected $radix;
	
	
	public function __construct($radix = 0)
	{
		$this->radix = $radix;
	}
	
	
	public function filter($value)
	{
		return intval($value, $this->radix);
	}
}