<?php namespace Proper;


/**
	An Action acts upon a value in preparation for its assignment to a property.
	
	There are two types of actions:
	
	A **Constraint** checks the conformance of a value to a set of rules.  If a value does not conform, the constraint returns an error message stating why the value was rejected.  Constraints do not alter values; they only report on their acceptability for assignment to the property.  Constraints implement the {@link Proper\Constraint} interface.
	
	A **Filter** transforms values to satisfy a set of criteria.  If a value already meets those criteria, the filter returns it in its original form.  Filters are not responsible for validatity their inputs; they simply return a value that is acceptable for assignment to the property.  Filters implement the {@link Proper\Filter} interface.
**/
interface Action
{
	/**
		Performs the action on a value.
		
		@param   mixed $value  The value.
		@return  mixed
	**/
	public function apply($value);
}