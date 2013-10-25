<?php namespace Proper\Constraint;

class RegexConstraint
extends AbstractConstraint
{
	public function test()
	{
		if (($numMatches = preg_match($this->parameters[0], $this->value)) === false)
		{
			$message = var_export($this->parameters[0], true) . ' is not a valid regular expression';
			throw new \Proper\ConfigurationException($this->property, $message);
		}
		
		return $numMatches > 0;
	}
	
	
	public function getErrorMessage()
	{
		$property = $this->property->getPropertyIdentifier();
		$pattern = var_export($this->parameters[0], true);
		$value = var_export($this->value, true);
		return "$property must match the regular expression $pattern, $value given";
	}
}