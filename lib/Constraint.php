<?php namespace Proper;

interface Constraint
{
	public function __construct(Definition $property);
	public function getErrorMessage();
	public function getParameters();
	public function getValue();
	public function setParameters(array $parameters);
	public function setValue($value);
	public function test();
}