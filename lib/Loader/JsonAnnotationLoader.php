<?php namespace Proper\Loader;

use \Exception;
use \ReflectionClass;
use \Proper\Loader;


/**
	JSONAnnotationLoader uses the {@link http://www.php.net/manual/en/book.reflection.php Reflection API} to extract a textual definition of the property's accessibility and filters from its doc comment.
	
	
	Accesiblity
	===========
	
	Properties are designated as publicly readable and writable by including the `@readable` and `@writable` tags, respectively.  A property whose doc comment contains neither of these tags is not publicly accessible.
	
	
	Filters
	=======
	
	Filter definitions consist of the `@filter` tag followed by the name of a class that implements the {@link Proper\Filter} interface and a JSON-formatted set of parameters.  Filter definitions must occupy their own line and may not wrap.  When referencing one of Proper's built-in filters, the namespace can be omitted; otherwise, the class name must be fully qualified.
	
	
	Examples
	========
	
	Indicates that a property is publicly readable:
	<code>@readable
	</code>
	
	Indicates that a property is both publicly readable and publicly writable:
	<code>This property is @readable and @writable
	</code>
	
	Designates a {@link Proper\Filter\Range} filter that accepts numbers between 0 and 255:
	<code>@filter Range {">=": 0, "<=": 255}
	</code>
	
	Designates a custom filter:
	<code>@filter \My\Custom\Filter {"foo": "bar"}
	</code>
	
**/
class JsonAnnotationLoader
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
	
	
	/**
		@inheritdoc
	**/
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
	
	
	/**
		Searches for the `@readable` tag in a property's doc comment.
		
		@param   string $docComment  The property's doc comment.
		@return  boolean             `True` if the property is readable, `false` if not.
	**/
	protected static function parseReadability($docComment)
	{
		$pattern = '/' . preg_quote(self::READABLE_TAG, '/') . '/m';
		return preg_match($pattern, $docComment) === 1;
	}
	
	
	/**
		Searches for the `@writable` tag in a property's doc comment.
		
		@param string $docComment The property's doc comment.
		@returns `True` if the property is writable, `false` if not.
	**/
	protected static function parseWritability($docComment)
	{
		$pattern = '/' . preg_quote(self::WRITABLE_TAG, '/') . '\\W/m';
		return preg_match($pattern, $docComment) === 1;
	}
	
	
	/**
		Parses a property's doc comment for `@filter` definitions.
		
		@param   string $docComment  The property's doc comment.
		@return  stdClass[]          A set of objects that indicate the filter's class and parameters.
	**/
	protected static function parseFilters($docComment)
	{
		$filters = array();
		$pattern = '/^[\s\*]*' . preg_quote(self::FILTER_TAG, '/') . '\s+(\S+)\s+(.*)$/m';
		preg_match_all($pattern, $docComment, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $match)
		{
			$filter = new \stdClass();
			$filter->class = self::parseFilterClass($match[1]);
			$filter->options = self::parseFilterOptions($match[2], $filter->class);
			$filters[] = $filter;
		}
		
		return $filters;
	}
	
	
	/**
		Converts the filter class from the doc comment into a fully-qualified class name.
		
		@param   string $class  The name of the class as it appears in the doc comment.
		@return  string         The fully-qualified class name.
		@throws  Exception      When the specified class is not defined.
		@throws  Exception      When the specified class does not implement the {@link Proper\Filter} interface.
	**/
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
	
	
	/**
		Converts JSON-formatted filter options from the doc comment into a PHP object.
		
		@param   string $json  The JSON as it appears in the doc comment.
		@return  mixed         The parsed options.
		@throws  Exception     When the JSON could not be parsed.
	**/
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