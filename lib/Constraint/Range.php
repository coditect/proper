<?php namespace Proper\Constraint;

use \Exception;
use \Proper\Constraint;


/**
	The Range constraint validates that a given value satisfies an inequality such as `n >= 1` or `0 <= c < 256`.
**/
class Range
implements Constraint
{
	/**
		The regular expression used to parse inequalities.
	**/
	const INEQUALITY_PATTERN = '/^(?:([-\.0-9]+)\s*([<>]=?))?\s*\w+\s*(?:([<>]=?)\s*([-\.0-9]+))?$/';
	
	
	/**
		The upper bound of the range.
		
		@var int|float
	**/
	protected $upper;
	
	
	/**
		The lower bound of the range.
		
		@var integer|float
	**/
	protected $lower;
	
	
	/**
		Whether or not the range includes or excludes its upper bound.
		
		@var boolean
	**/
	protected $includesUpper;
	
	
	/**
		Whether or not the range includes or excludes its lower bound.
		
		@var boolean
	**/
	protected $includesLower;
	
	
	/**
		Initializes the constraint with an expression that indicates the upper and/or lower bounds of the range.
		
		@param  string $inequality  The upper and/or lower bounds of the range.
		@throws Exception           When the given expression contradicts itself by using incompatible comparison operators.
		@throws Exception           When the lower bound in the given expression is greater than the upper bound.
		@throws Exception           When the given expression is syntactically invalid.
	**/
	public function __construct($inequality)
	{
		preg_match(self::INEQUALITY_PATTERN, trim($inequality), $matches);
		
		$leftValue  = isset($matches[1]) ? self::parseValue($matches[1]) : null;
		$rightValue = isset($matches[4]) ? self::parseValue($matches[4]) : null;
		
		$leftOperator  = empty($matches[2]) ? '0' : $matches[2];
		$rightOperator = empty($matches[3]) ? '0' : $matches[3];
		
		if (is_int($leftValue) || is_int($rightValue))
		{
			if ($leftOperator && $rightOperator)
			{
				if ($leftOperator[0] !== $rightOperator[0])
				{
					throw new Exception("The $leftOperator and $rightOperator operators cannot be used in the same inequality");
				}
				
				if (($leftOperator[0] === '<' && $leftValue > $rightValue) || ($leftOperator[0] === '>' && $leftValue < $rightValue))
				{
					throw new Exception('The lower bound of a range cannot be greater than the upper bound');
				}
			}
		
			if ($leftOperator[0] === '<' || $rightOperator[0] === '<')
			{
				$this->upper = $rightValue;
				$this->lower = $leftValue;
				$this->includesUpper = strlen($rightOperator) === 2;
				$this->includesLower = strlen($leftOperator) === 2;
			}
			else
			{
				$this->upper = $leftValue;
				$this->lower = $rightValue;
				$this->includesUpper = strlen($leftOperator) === 2;
				$this->includesLower = strlen($rightOperator) === 2;
			}
		}
		else
		{
			throw new Exception(var_export($inequality, true) . ' is not a valid inequality');
		}
	}
	
	
	/**
		Parses a string composed of the characters [-\.0-9] only into an integer or float.
		
		@param  string $value  The value to parse.
		@return integer|float  The parsed value.
		@throws Exception      When the string does not represent a number.
	**/
	public static function parseValue($value)
	{
		if (is_numeric($value))
		{
			if (strpos($value, '.') === false)
			{
				return intval($value);
			}
			else
			{
				return floatval($value);
			}
		}
		else if (!empty($value))
		{
			throw new Exception(var_export($value, true) . ' is not a number');
		}
	}
	
	
	/**
		Checks that the given value falls within the specified range.
		
		@param   integer|float  The value to be validated.
		@return  string         An error message, when the given value does not fall within the specified range.
	**/
	public function apply($value)
	{
		$messageHead = 'Expecting a value ';
		$messageTail = ', ' . var_export($value, true) . ' given';
		
		if (!$this->checkUpper($value))
		{
			$orEqualTo = $this->includesUpper ? ' or equal to ' : ' ';
			$messageBody = 'less than' . $orEqualTo . var_export($this->upper, true);
			return $messageHead . $messageBody . $messageTail;
		}
		
		if (!$this->checkLower($value))
		{
			$orEqualTo = $this->includesLower ? ' or equal to ' : ' ';
			$messageBody = 'greater than' . $orEqualTo . var_export($this->lower, true);
			return $messageHead . $messageBody . $messageTail;
		}
	}
	
	
	/**
		Checks that the given value is less than the range's maximum.
		
		@param   mixed    The value to be validated.
		@return  boolean  `True` if the value is less than the upper bound, `false` otherwise.
	**/
	protected function checkUpper($value)
	{
		if (!is_null($this->upper))
		{
			if ($this->includesUpper)
			{
				return $value <= $this->upper;
			}
			else
			{
				return $value < $this->upper;
			}
		}
		
		return true;
	}
	
	
	/**
		Checks that the given value is greater than the range's minimum.
		
		@param   mixed    The value to be validated.
		@return  boolean  `True` if the value is greater than the lower bound, `false` otherwise.
	**/
	protected function checkLower($value)
	{
		if (!is_null($this->lower))
		{
			if ($this->includesLower)
			{
				return $value >= $this->lower;
			}
			else
			{
				return $value > $this->lower;
			}
		}
		
		return true;
	}
}