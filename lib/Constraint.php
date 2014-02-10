<?php namespace Proper;


/**
	A Constraint validates values according to as set of predefined rules.
**/
interface Constraint
{
	/**
		Initializes the constraint.
		
		@param   mixed $rules  The rules that govern what values the constraint will allow and disallow.
		@throws  Exception     When the given rules are incomplete, contradictory, or otherwise invalid.
	**/
	public function __construct($rules);
	
	
	/**
		Applies the filter's validation rules to a value.
		
		@param   mixed $value  The value to validate.
		@return  string        An error message when the given value is invalid; `null` otherwise.
	**/
	public function apply($value);
}