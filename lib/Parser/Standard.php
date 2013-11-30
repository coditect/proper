<?php namespace Proper\Parser;

use \Proper\Parser;
use \Proper\Definition;
use \Proper\ConfigurationException;


class Standard
implements Parser
{
	/**
		The tag that introduces a property access definition in a doc comment.
	**/
	const ACCESS_TAG = '@access';
	
	
	/**
		The tag that introduces a property filter definition in a doc comment.
	**/
	const FILTER_TAG = '@filter';
	
	
	public function __construct(Definition $property)
	{
		$this->property = $property;
	}
	
	
	public function parseAccess($definition)
	{
		$access = new \stdClass();
		$pattern = '/^[\s\*]*' . preg_quote(self::ACCESS_TAG, '/') . '\s+(.*)$/m';
		preg_match($pattern, $definition, $matches);
		
		if (isset($matches[1]))
		{
			$access->readable = preg_match('/(^|[^a-z])read(only|able)?($|[^a-z])/i', $matches[1]) === 1;
			$access->writable = preg_match('/(^|[^a-z])writ(e|eonly|able)($|[^a-z])/i', $matches[1]) === 1;
		}
		else
		{
			$access->readable = $access->writable = false;
		}
		
		return $access;
	}
	
	
	public function parseFilters($definition)
	{
		$filters = array();
		$pattern = '/^[\s\*]*' . preg_quote(self::FILTER_TAG, '/') . '\s+(\S+)\s+(.*)$/m';
		preg_match_all($pattern, $definition, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $match)
		{
			$class = $this->parseFilterClass($match[1]);
			$options = $this->parseFilterOptions($match[2]);
			$filters[$class] = $options;
		}
		
		return $filters;
	}
	
	
	public function parseFilterClass($class)
	{
		if ($class[0] !== '\\')
		{
			$class = '\\Proper\\Filter\\' . ucfirst($class);
		}
		
		if (class_exists($class, true))
		{
			$reflection = new \ReflectionClass($class);
			
			if ($reflection->implementsInterface('\\Proper\\Filter'))
			{
				return $class;
			}
			else
			{
				throw new ConfigurationException($this->property, "$class is not an instance of \\Proper\\Filter");
			}
		}
		else
		{
			throw new ConfigurationException($this->property, "$class is not defined");
		}
	}
	
	
	public function parseFilterOptions($json)
	{
		if ($options = json_decode($json))
		{
			return $options;
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
			throw new ConfigurationException($this->property, $errorMessage);
		}
	}
}