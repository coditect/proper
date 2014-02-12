<?php namespace Proper;


/**
	A Filter transforms values into a form appropriate for assignment to a property.
**/
interface Filter
extends Action
{
	/**
		Applies the filter's transformation rules to a value.
		
		@param   mixed $value  The original value.
		@return  mixed         The transformed value.
	**/
	public function apply($value);
}