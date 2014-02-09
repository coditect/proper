<?php namespace Proper;


/**
	The Properties trait grants classes the ability to use Proper getters and setters through the magic __call() method.
**/
trait Properties
{
	/**
		The loader responsible for retrieving and parsing information about a property.
		
		@var Proper\Loader
	**/
	protected static $proper__loader = null;
	
	
	/**
		A cache of {@link Proper\Property} objects that have been instantiated for previous invocations of {@link __call()}, indexed by the name of the property.
		
		@var Proper\Property[]
	**/
	protected static $proper__properties = array();
	
	
	/**
		Handles a call to an undefined getter or setter.
		
		@param   string $name      The name of the method that was invoked.
		@param   array $arguments  The arguments passed to the method.
		@return  mixed             The result of the method call.
		@see     http://www.php.net/manual/en/language.oop5.overloading.php#object.call
	**/
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
	
	
	/**
		Retrieves the value of the given property if it is readable.
		
		@param   string $name                  The name of the property.
		@return  mixed                         The value of the property.
		@throws  Proper\Exception\NotReadable  When the property is not readable.
	**/
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
	
	
	/**
		Sets the value of the given property if it is writable.
		
		@param   string $name                  The name of the property.
		@param   mixed $value                  The new value to assign to the property.
		@return  mixed                         The value assigned to the property.
		@throws  Proper\Exception\NotWritable  When the property is not writable.
	**/
	protected function proper__setValue($name, $value)
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
	
	
	/**
		Retrieves the {@link Proper\Property} object that corresponds to the given property name.
		
		@param   string $name     The name of the property.
		@return  Proper\Property  An object describing the accesiblity of the property and the constraints placed on its data.
	**/
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