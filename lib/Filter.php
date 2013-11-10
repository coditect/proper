<?php namespace Proper;

interface Filter
{
	public function __construct(Definition $property, $options);
	public function isValid($value);
	public function transform($value);
	public function applyTo($value);
}