<?php namespace Proper;

use \Exception;
use \Proper\Exception\Configuration as ConfigurationException;
use \Proper\Exception\Validation as ValidationException;


/**
	A statement of the accessiblity of an object's property and the constraints and filters that should be applied to its data.
**/
class Property
{
	/**
		The name of the propery.
		@var string
	**/
	protected $name;
	
	
	/**
		The name of the class the propery belongs to.
		@var string
	**/
	protected $class;
	
	
	/**
		Whether or not the property is publicly readable.
		@var boolean
	**/
	protected $readable = false;
	
	
	/**
		Whether or not the property is publicly writable.
		@var boolean
	**/
	protected $writable = false;
	
	
	/**
		The list of filters on the property's value.
		@var Proper\Filter[]
	**/
	protected $actions = array();
	
	
	/**
		Initializes a new propery definition for the given property in the given class.
		
		@param  string $name   The name of the propery.
		@param  string $class  The name of the class the propery belongs to.
	**/
	public function __construct($propertyName, $className)
	{
		$this->name = $propertyName;
		$this->class = $className;
	}
	
	
	/**
		Loads the property's accessibility information and filter definitions using the given loader.
		
		@param   Proper\Loader $loader           The loader to use to retreive the property's info.
		@throws  Proper\Exception\NotFound       When no property with the given name is defined in the given class.
		@throws  Proper\Exception\Configuration  When the loader is unable to parse the property's info.
		@throws  Proper\Exception\Configuration  When one of the property's filters cannot be instantiated.
	**/
	public function load(Loader $loader)
	{
		try
		{
			if ($def = $loader->load($this->class, $this->name))
			{
				$this->readable = $def->readable;
				$this->writable = $def->writable;
				
				foreach ($def->actions as $action)
				{
					try
					{
						$this->actions[] = new $action->class($action->rules);
					}
					catch (Exception $e)
					{
						throw new ConfigurationException($this, $action, $e);
					}
				}
			}
			else
			{
				throw new Exception\NotFound($this);
			}
		}
		catch (Exception $e)
		{
			throw new ConfigurationException($this, null, $e);
		}
	}
	
	
	/**
		Gets the fully-qualified name of the property.
		
		@return string The property's name.
	**/
	public function getName()
	{
		return $this->class . '::$' . $this->name;
	}
	
	
	/**
		Checks whether the property is readable.
		
		@return boolean `True` if the property is readable, `false` if not.
	**/
	public function isReadable()
	{
		return $this->readable;
	}
	
	
	/**
		Checks whether the property is writable.
		
		@return boolean `True` if the property is writable, `false` if not.
	**/
	public function isWritable()
	{
		return $this->writable;
	}
	
	
	/**
		Applies each of the property's filters to the given value.
		
		@param   mixed $value                 The value to check.
		@return  mixed                        The filtered value.
		@throws  Proper\Exception\Validation  When the provided value does not satisfy a constraint imposed by one of the filters.
	**/
	public function applyFilters($value)
	{
		foreach ($this->actions as $action)
		{
			if ($action instanceof Constraint)
			{
				if ($error = $action->apply($value))
				{
					//throw new ValidationException($this, $action, $error);
					echo $error;
				}
			}
			else
			{
				$value = $action->apply($value);
			}
		}
		
		return $value;
	}
}