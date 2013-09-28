<?php namespace Proper\Filter;

class Boolean
implements \Proper\Filter
{
	public function filter($value)
	{
		return (bool) $value;
	}
}