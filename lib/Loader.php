<?php namespace Proper;


interface Loader
{
	public function load($class, $property);
}