<?php namespace Proper\Constraint;


/**
	The Length constraint validates the length of strings, arrays, and countable objects.
	
	When using multibyte encodings (such as UTF-8), note that this constraint counts the number of *characters* in a string, not the number of bytes.
**/
class Length
extends Range
{
	/**
		Validates the length of the given string, array, or countable object.
		
		@param   string|array|Countable  The string, array, or object  to be validated.
		@return  string|array|Countable  The given value, if it satisfies the constraint's requirements.
		@throws  Exception               When the given value is shorter than the miminum length or longer than the maximum length.
	**/
	public function apply($value)
	{
		if (is_array($value) || (is_object($value) && $value instanceof \Countable))
		{
			$length = count($value);
		}
		else
		{
			$length = mb_strlen((string) $value);
		}
		
		return str_replace('value', 'length', parent::apply($length));
	}
}