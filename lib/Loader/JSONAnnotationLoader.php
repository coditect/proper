<?php namespace Proper\Loader;

use \Exception;
use \ReflectionClass;
use \Proper\Loader;


class JSONAnnotationLoader
implements Loader
{
	/**
		The tag that marks a property as being publicly readable.
	**/
	const READABLE_TAG = '@readable';
	
	
	/**
		The tag that marks a property as being publicly writable.
	**/
	const WRITABLE_TAG = '@writable';
	
	
	/**
		The tag that introduces a property filter definition in a doc comment.
	**/
	const FILTER_TAG = '@filter';
	
	
	public function load($class, $property)
	{
		if (property_exists($class, $property))
		{
			$reflection = new \ReflectionProperty($class, $property);
			$docComment = $reflection->getDocComment();
			
			$definition = new \stdClass();
			$definition->readable = self::parseReadability($docComment);
			$definition->writable = self::parseWritability($docComment);
			$definition->filters = self::parseFilters($docComment);
			return $definition;
		}
	}
	
	
	protected static function parseReadability($docComment)
	{
		$pattern = '/' . preg_quote(self::READABLE_TAG, '/') . '/m';
		return preg_match($pattern, $docComment) === 1;
	}
	
	
	protected static function parseWritability($docComment)
	{
		$pattern = '/' . preg_quote(self::WRITABLE_TAG, '/') . '\\W/m';
		return preg_match($pattern, $docComment) === 1;
	}
	
	
	protected static function parseFilters($docComment)
	{
		$filters = array();
		$pattern = '/^[\s\*]*' . preg_quote(self::FILTER_TAG, '/') . '\s+(\S+)\s+(.*)$/m';
		preg_match_all($pattern, $docComment, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $match)
		{
			$class = self::parseFilterClass($match[1]);
			$options = self::parseFilterOptions($match[2], $class);
			$filters[$class] = $options;
		}
		
		return $filters;
	}
	
	
	protected static function parseFilterClass($class)
	{
		if ($class[0] !== '\\')
		{
			$class = '\\Proper\\Filter\\' . ucfirst($class);
		}
		
		if (class_exists($class))
		{
			$reflection = new ReflectionClass($class);
			
			if ($reflection->implementsInterface('\\Proper\\Filter'))
			{
				return $class;
			}
			else
			{
				throw new Exception("$class is not an instance of \\Proper\\Filter");
			}
		}
		else
		{
			throw new Exception("Filter $class is not defined");
		}
	}
	
	
	protected static function parseFilterOptions($json)
	{
		if ($options = json_decode($json))
		{
			return $options;
		}
		else
		{
			$jsonErrors = array(
				JSON_ERROR_DEPTH => 'exceeds the maximum stack depth',
				JSON_ERROR_STATE_MISMATCH => 'is invalid or malformed',
				JSON_ERROR_CTRL_CHAR => 'contains an incorrectly encoded control character',
				JSON_ERROR_SYNTAX => 'contains a syntax error',
				JSON_ERROR_UTF8 => 'contains malformed UTF-8 characters'
			);
			
			$errorCode = json_last_error();
			$errorMessage = isset($jsonErrors[$errorCode]) ? $jsonErrors[$errorCode] : null;
			throw new Exception('The JSON configuration ' . $errorMessage);
		}
	}
}