<?php namespace Proper;


/**
	A Filter transforms values according to as set of predefined rules.
**/
interface Filter
{
	/**
		Applies the filter's transformation rules to a value.
		
		@param   mixed $value  The original value.
		@return  mixed         The transformed value.
	**/
	public function apply($value);
}