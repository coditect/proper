<?php namespace Proper;


/**
	A Constraint checks the validity of a value for assignment to a property.
	
	If a value does not conform to the constraint's rules, the constraint returns an error message stating why the value was rejected.
**/
interface Constraint
extends Action
{
	/**
		Applies the constraints's validation rules to the given value.
		
		@param   mixed $value  The value to validate.
		@return  string        An error message when the given value is invalid; `null` otherwise.
	**/
	public function apply($value);
}