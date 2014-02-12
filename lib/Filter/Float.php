<?php namespace Proper\Filter;


/**
	The Float filter converts all input into floats.
**/
class Float
implements \Proper\Filter
{
	public function apply($value)
	{
		return floatval($value);
	}
}