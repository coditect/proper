<?php namespace Proper\Constraint;

class InstanceConstraint
extends AbstractConstraint
{
	public function setParameters(array $parameters)
	{
		if (!class_exists($parameters[0], true))
		{
			throw new \Proper\ConfigurationException($this->property, "Class $parameters[0] is not defined");
		}
		
		parent::setParamters($parameters);
	}
	
	
	public function test()
	{
		return $this->value instanceof $this->parameters[0];
	}
	
	
	public function getErrorMessage()
	{
		$property = $this->property->getPropertyIdentifier();
		$class = get_class($this->value);
		return "$property must be an instance of {$this->className}, $class given";
	}
}