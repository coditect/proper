<?php namespace Proper;

class ConstraintViolation
extends \Exception
{
	protected $constraint;
	protected $property;
	
	
	public function __construct(Definition $property, Constraint $constraint)
	{
		$this->property = $property;
		$this->constraint = $constraint;
		$message = $constraint->getErrorMessage($property);
		parent::__construct($message);
	}
	
	
	public function getConstraint()
	{
		return $this->constraint;
	}
	
	
	public function getProperty()
	{
		return $this->property;
	}
}