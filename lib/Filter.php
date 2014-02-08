<?php namespace Proper;

interface Filter
{
	public function __construct($options);
	public function apply($value);
}