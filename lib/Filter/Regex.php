<?php namespace Proper\Filter;

class Regex
extends AbstractFilter
{
	protected $pattern;
	
	
	public function init($options)
	{
		$this->pattern = $options;
	}
	
	
	public function isValid($value)
	{
		if (($numMatches = preg_match($this->pattern, $value)) === false)
		{
			$message = var_export($this->pattern, true) . ' is not a valid regular expression';
			throw new \Proper\ConfigurationException($this->property, $message);
		}
		
		return $numMatches > 0;
	}
	
	
	public function getError($value)
	{
		$property = $this->property->getPropertyIdentifier();
		$pattern = var_export($this->pattern, true);
		$value = var_export($value, true);
		return "$property must match the regular expression $pattern, $value given";
	}
}