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
class AnnotationLoader
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
			$definition->actions = self::parseActions($docComment);
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
		Parses a property's doc comment for `@constraint` and `@filter` definitions.
		
		@param   string $docComment  The property's doc comment.
		@return  stdClass[]          A set of objects that indicate the filter's class and parameters.
	**/
	protected static function parseActions($docComment)
	{
		$actions = array();
		$pattern = '/^[\s\*]*@(constraint|filter)\s+(\S+):(.*)$/m';
		preg_match_all($pattern, $docComment, $matches, PREG_SET_ORDER);
		
		foreach ($matches as $match)
		{
			$action = new \stdClass();
			$action->type = ucfirst(strtolower($match[1]));
			$action->class = $match[2];
			$action->rules = trim($match[3]);
			
			$typeClass = '\\Proper\\' . $action->type;
			$typeNamespace = $typeClass . '\\';
			
			if ($action->class[0] !== '\\')
			{
				$action->class = $typeNamespace . $action->class;
			}
			
			if (class_exists($action->class))
			{
				$reflection = new ReflectionClass($action->class);
			
				if ($reflection->implementsInterface($typeClass))
				{
					$actions[] = $action;
				}
				else
				{
					throw new Exception("$class is not an instance of $typeClass");
				}
			}
			else
			{
				throw new Exception("{$action->type} {$action->class} is not defined");
			}
		}
		
		return $actions;
	}
}