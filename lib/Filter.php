<?php namespace Proper;


/**
	A Filter validates and transforms values assigned to a property according to as set of predefined rules.
**/
interface Filter
{
	/**
		Initializes the filter with a set of validation and transformation rules.
		
		@param   mixed $rules  The validation and transformation rules.
		@throws  Exception     When the given rules are incomplete, contradictory, or otherwise invalid.
	**/
	public function __construct($rules);
	
	
	/**
		Applies the filter's validation and transformation rules to a value.
		
		@param   mixed $value  The original value.
		@return  mixed         The transformed value.
		@throws  Exception     When the value is not valid.
	**/
	public function apply($value);
}