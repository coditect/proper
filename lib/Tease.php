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
			throw new Exception\Access\NotReadable($def);
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
			throw new Exception\Access\NotWritable($def);
		}
	}
	
	
	protected function getPropertyDefinition($name)
	{
		if (!isset(static::$propertyDefinitions[$name]))
		{
			static::$propertyDefinitions[$name] = new Definition(__CLASS__, $name);
		}
		
		return static::$propertyDefinitions[$name];
	}
}