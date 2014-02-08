<?php namespace Proper;

use \Exception;
use \Proper\Exception\Configuration as ConfigurationException;
use \Proper\Exception\Validation as ValidationException;


/**
	A statement of the accessiblity of an object's property and the constraints and filters that should be applied to its data.
	
	The Definition class uses the {@link http://www.php.net/manual/en/book.reflection.php Reflection API} to extract a textual definition of the property's accessibility and constraints from its doc comment.  The definition is formatted as a JSON object and is introduced with the `@proper` tag.  A definition consists of one or more of the following keys:
	
	<ul>
		<li><b>readable:</b> A boolean indicating whether or not entities outside of the property's class can read its value.</li>
		<li><b>writable:</b> A boolean indicating whether or not entities outside of the property's class can set its value.</li>
		<li><b>constraints:</b> An array of constraints to be checked when setting the property's value.  Each key in the array refers to a class that implements {@link Proper\Constraint}, and each value is an array of arguments to be passed to that constraint.  </li>
		<li><b>filters:</b> An array of filters to be applied when setting the property's value.  Each key in the array refers to a class that implements {@link Proper\Filter}, and each value is an array of arguments to be passed to that filter.</li>
	</ul>
	
	The definition in the example below indicates that a property is publicly readable, but not publicly writable.  No constraints or filters are defined because they are only applicable to properties that can be set by the outside world.
	
	* <code>@proper {
	*   "readable": true,
	*   "writable": false
	* };</code>
	
	If a key in the constraints or filters array references one of Proper's built-in constraint or filter classes, it can use an abbreviated form of the class name that omits the namespace.  In the example below, the key `"Type"` gets mapped to the {@link Proper\Constraint\TypeConstraint} class.  Constraint and filter classes that are not built into Proper must be referenced by their fully-qualified class names.
	
	* <code>@proper {
	*   "readable": true,
	*   "writable": true,
	*   "constraints": {
	*     "Type": ["numeric"]
	*   },
	*   "filters": {
	*     "Float": [],
	*     "Round": [2]
	*   }
	* };</code>
	
	Constraints and filters are evaluated in the order in which they are defined.  A value assigned to a property with the above definition would first be converted to a floating point number by the {@link Proper\Filter\Float} filter, and then rounded to two decimal places by the {@link Proper\Filter\Round} filter.
	
**/
class Property
{
	/**
		The name of the propery.
	**/
	protected $name;
	
	
	/**
		The name of the class the propery belongs to.
	**/
	protected $class;
	
	
	/**
		Whether or not the property is publicly readable.
	**/
	protected $readable = false;
	
	
	/**
		Whether or not the property is publicly writable.
	**/
	protected $writable = false;
	
	
	/**
		The list of filters on the property's value.
	**/
	protected $filters = array();
	
	
	
	
	/**
		Initializes a new propery definition for the given property in the given class.
		
		@param   string $name       The name of the propery.
		@param   string $class      The name of the class the propery belongs to.
		@throws  NotFoundException  When no property with the given name is defined in the given class.
	**/
	public function __construct($propertyName, $className)
	{
		$this->name = $propertyName;
		$this->class = $className;
	}
	
	
	public function load(Loader $loader)
	{
		try
		{
			if ($def = $loader->load($this->class, $this->name))
			{
				$this->readable = $def->readable;
				$this->writable = $def->writable;
				
				foreach ($def->filters as $class => $params)
				{
					try
					{
						$this->filters[] = new $class($params);
					}
					catch (Exception $e)
					{
						throw new ConfigurationException($this, $filter, $e);
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
	
	
	public function getName()
	{
		return $this->class . '::$' . $this->name;
	}
	
	
	public function isReadable()
	{
		return $this->readable;
	}
	
	
	public function isWritable()
	{
		return $this->writable;
	}
	
	
	/**
		Checks the given value against the list of constraints.
		
		@param   mixed $value         The value to check.
		@return  boolean              True when the provided value satisfies all constraints
		@throws  ConstraintViolation  When the provided value does not satisfy a constraint.
	**/
	public function applyFilters($value)
	{
		foreach ($this->filters as $filter)
		{
			try
			{
				$value = $filter->apply($value);
			}
			catch (Exception $e)
			{
				throw new ValidationException($this, $filter, $e);
			}
		}
		
		return $value;
	}
}