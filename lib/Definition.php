<?php namespace Proper;


class Definition
{
	protected $class;
	protected $name;
	public $readable = false;
	public $writable = false;
	protected $constraints = array();
	protected $filters = array();
	
	
	public function __construct($class, $name)
	{
		$this->class = $class;
		$this->name = $name;
		
		if (property_exists($class, $name))
		{
			$class = new \ReflectionClass($class);
			$property = $class->getProperty($name);
			$this->class = $property->class;
			preg_match('/@config\s+(\\{.*?\\});/s', $property->getDocComment(), $matches);
			
			if ($json = json_decode($matches[1]))
			{
				foreach (array('readable', 'writable', 'constraints', 'filters') as $key)
				{
					if (isset($json->$key))
					{
						$this->$key = $json->$key;
					}
				}
			}
			else
			{
				throw new ConfigurationException($this);
			}
		}
		else
		{
			throw new Exception\Access\NotFound($this);
		}
	}
	
	
	public function check($value)
	{
		foreach ($this->constraints as $class => $parameters)
		{
			if ($class[0] !== '\\')
			{
				$class = '\\Proper\\Constraint\\' . ucfirst($class) . 'Constraint';
			}
			
			$reflection = new \ReflectionClass($class);
			$constraint = $reflection->newInstanceArgs(array($this));
			$constraint->setParameters($parameters);
			$constraint->setValue($value);
			
			if (!$constraint->test())
			{
				throw new ConstraintViolation($this, $constraint);
			}
		}
		
		return true;
	}
	
	
	public function filter($value)
	{
		foreach ($this->filters as $class => $arguments)
		{
			if ($class[0] !== '\\')
			{
				$class = '\\Proper\\Filter\\' . ucfirst($class);
			}
			
			$reflection = new \ReflectionClass($class);
			$filter = $reflection->newInstanceArgs((array) $arguments);
			$value = $filter->filter($value);
		}
		
		return $value;
	}
	
	
	public function getPropertyIdentifier()
	{
		return $this->class . '::$' . $this->name;
	}
}