<?php namespace Proper;


/**
	A Filter transforms values according to as set of predefined rules.
**/
interface Filter
{
	/**
		Initializes the filter with a set of validation and transformation rules.
		
		@param   mixed $params  The parameters of the transformation.
		@throws  Exception      When the given parameters are incomplete, contradictory, or otherwise invalid.
	**/
	public function __construct($params);
	
	
	/**
		Applies the filter's transformation rules to a value.
		
		@param   mixed $value  The original value.
		@return  mixed         The transformed value.
	**/
	public function apply($value);
}