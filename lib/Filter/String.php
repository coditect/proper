<?php namespace Proper\Filter;

class String
implements \Proper\Filter
{
	public function filter($value)
	{
		return strval($value);
	}
}