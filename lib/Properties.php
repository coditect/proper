<?php namespace Proper;


trait Properties
{
	protected static $proper__loader = null;
	protected static $proper__properties = array();
	
	
	public function __call($name, $arguments)
	{
		$prefix = substr($name, 0, 3);
		$name = lcfirst(substr($name, 3));
		
		if ($prefix === 'get')
		{
			return $this->proper__getValue($name);
		}
		else if ($prefix === 'set')
		{
			return $this->proper__setValue($name, $arguments[0]);
		}
		else
		{
			trigger_error('Call to undefined method ' . __CLASS__ . "::$name()", E_USER_ERROR);
		}
	}
	
	
	
	
	
	protected function proper__getValue($name)
	{
		$property = $this->proper__getProperty($name);
		
		if ($property->isReadable())
		{
			return $this->$name;
		}
		else
		{
			throw new Exception\NotReadable($property);
		}
	}
	
	
	public function proper__setValue($name, $value)
	{
		$property = $this->proper__getProperty($name);
		
		if ($property->isWritable())
		{
			$this->$name = $property->applyFilters($value);
			return $this->$name;
		}
		else
		{
			throw new Exception\NotWritable($property);
		}
	}
	
	
	protected function proper__getProperty($name)
	{
		if (!isset(static::$proper__loader))
		{
			static::$proper__loader = new Loader\JSONAnnotationLoader;
		}
		
		if (!isset(static::$proper__properties[$name]))
		{
			$property = new Property($name, __CLASS__);
			$property->load(static::$proper__loader);
			static::$proper__properties[$name] = $property;
		}
		
		return static::$proper__properties[$name];
	}
}