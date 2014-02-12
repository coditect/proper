<?php namespace Proper;


/**
	A Constraint validates values according to as set of predefined rules.
**/
interface Constraint
{
	/**
		Applies the filter's validation rules to a value.
		
		@param   mixed $value  The value to validate.
		@return  string        An error message when the given value is invalid; `null` otherwise.
	**/
	public function apply($value);
}