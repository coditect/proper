<?php namespace Proper\Filter;


/**
	The Boolean filter converts all input into booleans.
**/
class Boolean
implements \Proper\Filter
{
	public function apply($value)
	{
		return (bool) $value;
	}
}