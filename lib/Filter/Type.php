<?php namespace Proper\Filter;

class Type
extends AbstractFilter
{
	protected $allowedTypes = array();
	protected $typeToForce = null;
	protected $radix = null;
	
	
	protected function init($options)
	{
		if (isset($options->allowed))
		{
			if (is_array($options->allowed))
			{
				$this->allowedTypes = $options->allowed;
			}
			else
			{
				$this->allowedTypes = array($options->allowed);
			}
		}
		
		if (isset($options->force))
		{
			$this->typeToForce = strtolower($options->force);
			
			if ($this->typeToForce == 'int' && isset($options->radix))
			{
				$this->radix = $options->radix;
			}
		}
	}
	
	
	public function isValid($value)
	{
		foreach ($this->allowedTypes as $type)
		{
			$function = 'is_' . strtolower($type);
			
			if (!function_exists($function))
			{
				if ($function === 'is_str')
				{
					$function = 'is_string';
				}
				else if ($function === 'is_boolean')
				{
					$function = 'is_bool';
				}
				else
				{
					throw new \Proper\ConfigurationException($this->property, $type . ' is not a valid type');
				}
			}
			
			if ($function($value))
			{
				return true;
			}
		}
		
		return false;
	}
	
	
	public function transform($value)
	{
		if (!is_null($this->typeToForce))
		{
			switch ($this->typeToForce)
			{
				case 'int':
				case 'integer':
				case 'long':
					return intval($value, $this->radix);
				
				case 'double':
				case 'float':
				case 'real':
					return floatval($value);
				
				case 'str':
				case 'string':
					return strval($value);
				
				case 'bool':
				case 'boolean':
					return (bool) $value;
				
				default:
					throw new \Proper\ConfigurationException($this->property, "Cannot cast to type {$this->typeToForce}");
			}
		}
	}
	
	
	public function getError($value)
	{
		$property = $this->property->getPropertyIdentifier();
		$message = "$property must be of type ";
		$numTypes = count($this->allowedTypes);
		$givenType = gettype($value);
		
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
			$message .= implode(', ', array_slice($this->allowedTypes, 0, -1));
			$message .= ', or ' . $this->parameters[$numTypes - 1];
		}
		else
		{
			$message .= $this->allowedTypes[0];
			
			if ($numTypes === 2)
			{
				$message .= ' or ' . $this->allowedTypes[1];
			}
		}
		
		$message .= ", $givenType given";
		return $message;
	}
}