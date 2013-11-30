<?php namespace Proper;


trait Tease
{
	protected static $propertyDefinitions = array();
	
	
	public function __call($name, $arguments)
	{
		$prefix = substr($name, 0, 3);
		$name = lcfirst(substr($name, 3));
		
		if ($prefix === 'get')
		{
			return $this->proper__get($name);
		}
		else if ($prefix === 'set')
		{
			return $this->proper__set($name, $arguments[0]);
		}
		else
		{
			trigger_error('Call to undefined method ' . __CLASS__ . "::$name()", E_USER_ERROR);
		}
	}
	
	
	
	
	
	protected function proper__get($name)
	{
		$def = $this->proper__definition($name);
		
		if ($def->readable)
		{
			return $this->$name;
		}
		else
		{
			throw new NotReadableException($def);
		}
	}
	
	
	public function proper__set($name, $value)
	{
		$def = $this->proper__definition($name);
		
		if ($def->writable)
		{
			$this->$name = $def->check($value);
			return $this->$name;
		}
		else
		{
			throw new NotWritableException($def);
		}
	}
	
	
	protected function proper__definition($name)
	{
		if (!isset(static::$propertyDefinitions[$name]))
		{
			$def = new Definition($name, __CLASS__);
			$def->parseDefinitionFromDocComment();
			static::$propertyDefinitions[$name] = $def;
		}
		
		return static::$propertyDefinitions[$name];
	}
}