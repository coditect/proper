<?php namespace Proper;

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
class Definition
{
	/**
		The tag that introduces a property definition in a doc comment.
	**/
	const TAG = '@proper';
	
	
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
	public $readable = false;
	
	
	/**
		Whether or not the property is publicly writable.
	**/
	public $writable = false;
	
	
	/**
		The list of constraints on the property's value.
	**/
	protected $constraints = array();
	
	
	/**
		The list of filters that should be applied before a new value is assigned to the property.
	**/
	protected $filters = array();
	
	
	/**
		The constraint that failed on the most recent call to Definition::check().
	**/
	protected $constraintFailed;
	
	
	
	
	/**
		Initializes a new propery definition for the given property in the given class.
		
		@param   string $name       The name of the propery.
		@param   string $class      The name of the class the propery belongs to.
		@throws  NotFoundException  When no property with the given name is defined in the given class.
	**/
	public function __construct($name, $class)
	{
		$this->name = $name;
		$this->class = $class;
		
		if (property_exists($class, $name))
		{
			$reflection = new \ReflectionClass($class);
			$property = $reflection->getProperty($name);
			$this->class = $property->class;
			$this->parseDefinition($property->getDocComment());
		}
		else
		{
			throw new NotFoundException($this);
		}
	}
	
	
	/**
		Extracts and parses the definition data contained in the given comment text.
		
		@param   string $comment         The text of the DocBlock-style comment that precedes the property's declaration in the source code.
		@throws  ConfigurationException  When the property definition block contains syntax errors or is otherwise not properly formatted.
	**/
	protected function parseDefinition($comment)
	{
		$pattern = '/' . preg_quote(self::PREFIX, '/') . '\s+(\\{.*?\\});/s';
		preg_match($pattern, $comment, $matches);
		
		if (isset($matches[1]))
		{
			if ($definition = json_decode($matches[1], true))
			{
				if (isset($definition['readable']))
				{
					$this->readable = (bool) $definition['readable'];
				}
				
				if (isset($definition['writable']))
				{
					$this->writable = (bool) $definition['writable'];
				}
				
				if (isset($definition['constraints']))
				{
					$this->parseConstraints($definition['constraints']);
				}
				
				if (isset($definition['filters']))
				{
					$this->parseFilters($definition['filters']);
				}
			}
			else
			{
				$jsonErrors = array(
					JSON_ERROR_DEPTH => 'The JSON property definition exceeds the maximum stack depth',
					JSON_ERROR_STATE_MISMATCH => 'The JSON property definition is invalid or malformed',
					JSON_ERROR_CTRL_CHAR => 'The JSON property definition contains an incorrectly encoded control character',
					JSON_ERROR_SYNTAX => 'The JSON property definition contains a syntax error',
					JSON_ERROR_UTF8 => 'The JSON property definition contains malformed UTF-8 characters'
				);
				
				$errorCode = json_last_error();
				$errorMessage = isset($jsonErrors[$errorCode]) ? $jsonErrors[$errorCode] : null;
				throw new ConfigurationException($this, $errorMessage);
			}
		}
	}
	
	
	protected function parseConstraints(array $definitions)
	{
		foreach ($definitions as $class => $parameters)
		{
			if ($class[0] !== '\\')
			{
				$class = '\\Proper\\Constraint\\' . ucfirst($class) . 'Constraint';
			}
			
			if (class_exists($class, true))
			{
				if ($class instanceof Constraint)
				{
					$reflection = new \ReflectionClass($class);
					$constraint = $reflection->newInstance($this);
					$constraint->setParameters((array) $parameters);
					$this->constraints[] = $constraint;
				}
				else
				{
					throw new ConfigurationException($this, "$class is not an instance of \\Proper\\Constraint");
				}
			}
			else
			{
				throw new ConfigurationException($this, "$class is not defined");
			}
		}
	}
	
	
	protected function parseFilters(array $definitions)
	{
		foreach ($definitions as $class => $parameters)
		{
			if ($class[0] !== '\\')
			{
				$class = '\\Proper\\Filter\\' . ucfirst($class);
			}
			
			if (class_exists($class, true))
			{
				if ($class instanceof Filter)
				{
					$reflection = new \ReflectionClass($class);
					$this->filters[] = $reflection->newInstanceArgs((array) $parameters);
				}
				else
				{
					throw new ConfigurationException($this, "$class is not an instance of \\Proper\\Filter");
				}
			}
			else
			{
				throw new ConfigurationException($this, "$class is not defined");
			}
		}
	}
	
	
	/**
		Checks the given value against the list of constraints.
		
		@param   mixed $value         The value to check.
		@param   boolean $throw       Whether or not to throw an exception if the value does not satisfy a constraint.
		@return  boolean              True when the provided value satisfies all constraints
		@throws  ConstraintViolation  When the provided value does not satisfy a constraint.
	**/
	public function check($value, $throw = true)
	{
		foreach ($this->constraints as $constraint)
		{
			$constraint->setValue($value);
			
			if (!$constraint->test())
			{
				$this->constraintFailed = $constraint;
				
				if ($throw)
				{
					throw new ConstraintViolation($this, $constraint);
				}
				else
				{
					return false;
				}
			}
		}
		
		$this->constraintFailed = null;
		return true;
	}
	
	
	/**
		Filters the given value with the defined filters.
		
		@param   mixed $value  The value to filter.
		@return  mixed         The filtered value.
	**/
	public function filter($value)
	{
		foreach ($this->filters as $filter)
		{
			$value = $filter->filter($value);
		}
		
		return $value;
	}
	
	
	/**
		Generates a string that identifies the defined property.
		
		@return  string  The property identifier.
	**/
	public function getPropertyIdentifier()
	{
		return $this->class . '::$' . $this->name;
	}
}