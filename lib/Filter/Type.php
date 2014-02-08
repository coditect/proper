<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


class Type
implements Filter
{
	protected $allowedTypes = array();
	protected $castFunction = null;
	protected $radix = null;
	
	
	public function __construct($options)
	{
		if (isset($options->allow))
		{
			foreach ((array) $options->allow as $type)
			{
				$this->allowedTypes[] = self::getNormalizedType($type);
			}
		}
		
		if (isset($options->force))
		{
			$this->castFunction = self::getCastFunctionForType($options->force);
			
			if ($this->castFunction == 'castToInteger' && isset($options->radix))
			{
				if (is_int($options->radix))
				{
					$this->radix = $options->radix;
				}
				else
				{
					throw new Exception('Radix must be an integer');
				}
			}
		}
	}
	
	
	public function apply($value)
	{
		if ($this->isValidType($value))
		{
			if (!is_null($this->castFunction))
			{
				return $this->{$this->castFunction}($value);
			}
			else
			{
				return $value;
			}
		}
		else
		{
			throw new Exception($this->getError($value));
		}
	}
	
	
	protected function isValidType($value)
	{
		if (count($this->allowedTypes) > 0)
		{
			foreach ($this->allowedTypes as $type)
			{
				$function = 'is_' . $type;
				
				if ($function($value))
				{
					return true;
				}
			}
			
			return false;
		}
		
		return true;
	}
	
	
	protected function getError($value)
	{
		//$property = $this->property->getPropertyIdentifier();
		$message = 'Expecting type ';
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
	
	
	protected function castToInteger($value)
	{
		return intval($value, $this->radix);
	}
	
	
	protected function castToFloat($value)
	{
		return floatval($value);
	}
	
	
	protected function castToString($value)
	{
		return strval($value);
	}
	
	
	protected function castToBoolean($value)
	{
		return (bool) $value;
	}
	
	
	protected static function getNormalizedType($type)
	{
		$normalized_type = strtolower($type);
		$check_function = 'is_' . $normalized_type;
	
		if (!function_exists($check_function))
		{
			if ($check_function === 'is_str')
			{
				return 'string';
			}
			else if ($check_function === 'is_boolean')
			{
				return 'bool';
			}
			else
			{
				throw new Exception($type . ' is not a valid type');
			}
		}
		
		return $normalized_type;
	}
	
	
	protected static function getCastFunctionForType($type)
	{
		switch (strtolower($type))
		{
			case 'int':
			case 'integer':
			case 'long':
				return 'castToInteger';
			
			case 'double':
			case 'float':
			case 'real':
				return 'castToFloat';
			
			case 'str':
			case 'string':
				return 'castToString';
			
			case 'bool':
			case 'boolean':
				return 'castToBoolean';
			
			default:
				throw new Exception("Cannot cast to type '$type'");
		}
	}
}