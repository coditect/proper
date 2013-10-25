<?php namespace Proper;


trait Tease
{
	protected static $propertyDefinitions = array();
	
	
	public function __get($name)
	{
		$def = $this->getPropertyDefinition($name);
		
		if ($def->readable)
		{
			return $this->$name;
		}
		else
		{
			throw new NotReadableException($def);
		}
	}
	
	
	public function __set($name, $value)
	{
		$def = $this->getPropertyDefinition($name);
		
		if ($def->writable && $def->check($value))
		{
			$this->$name = $def->filter($value);
		}
		else
		{
			throw new NotWritableException($def);
		}
	}
	
	
	protected function getPropertyDefinition($name)
	{
		if (!isset(static::$propertyDefinitions[$name]))
		{
			static::$propertyDefinitions[$name] = new Definition($name, __CLASS__);
		}
		
		return static::$propertyDefinitions[$name];
	}
}