<?php namespace Proper;


/**
	A Constraint validates values according to as set of predefined rules.
**/
interface Constraint
{
	/**
		Initializes the constraint.
		
		@param   mixed $params  A set of parameters that describe the values the constraint will allow or disallow.
		@throws  Exception      When the given parameters are incomplete, contradictory, or otherwise invalid.
	**/
	public function __construct($params);
	
	
	/**
		Applies the filter's validation rules to a value.
		
		@param   mixed $value  The value to validate.
		@return  string        An error message when the given value is invalid; `null` otherwise.
	**/
	public function apply($value);
}