<?php namespace Proper\Constraint;

class TypeConstraint
extends AbstractConstraint
{
	public function test()
	{
		foreach ($this->parameters as $type)
		{
			$function = 'is_' . $type;
			
			if (!function_exists($function))
			{
				if ($function === 'is_boolean')
				{
					$function = 'is_bool';
				}
				else
				{
					throw new \Proper\ConfigurationException($this->property, $type . ' is not a valid type');
				}
			}
			
			if ($function($this->value))
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	public function getErrorMessage()
	{
		$propertyName = $this->property->getPropertyIdentifier();
		$message = "$propertyName must be of type ";
		$numTypes = count($this->parameters);
		$givenType = gettype($this->value);
		
		if ($givenType === 'double')
		{
			$givenType = 'float';
		}
		else if ($givenType === 'unknown type' && is_callable($this->value))
		{
			$givenType = 'callable';
		}
		
		if ($numTypes > 2)
		{
			$message .= implode(', ', array_slice($this->parameters, 0, -1));
			$message .= ', or ' . $this->parameters[$numTypes - 1];
		}
		else
		{
			$message .= $this->parameters[0];
			
			if ($numTypes === 2)
			{
				$message .= ' or ' . $this->parameters[1];
			}
		}
		
		$message .= ", $givenType given";
		return $message;
	}
}