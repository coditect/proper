<?php namespace Proper\Filter;

class Float
implements \Proper\Filter
{
	public function filter($value)
	{
		return floatval($value);
	}
}