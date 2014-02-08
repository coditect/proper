<?php namespace Proper;


/**
	A Loader is responsible for retrieving the accesiblity and filter definitions for a property.
**/
interface Loader
{
	/**
		Retrieves information about the given property of the given class.
		
		@param string $class    The class the property belongs to.
		@param string $property The name of the property.
		@return stdClass An object with two boolean properties — `readable` and `writable` — that describe the property's accesibility, as well as a `filter` property that contains an array of filter data.  Each item in the `filter` property is also be an object, with a `class` property that contains the fully-qualified name of the filter's class and an `options` property that contains configuration data to be passed to the constuctor of that class.
		
		@see Proper\Property::load()
	**/
	public function load($class, $property);
}