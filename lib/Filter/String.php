<?php namespace Proper\Filter;


/**
	The String filter converts all input into stings.
**/
class String
implements \Proper\Filter
{
	public function apply($value)
	{
		return strval($value);
	}
}