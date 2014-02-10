<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


/**
	The Range filter validates that a given value falls within a specified range.
	
	A range (or *interval*, in mathematical parlance) may specify an upper bound, a lower bound, both, or neither†.  Each end of the range can be open (excluding the fixed value of the bound) or closed (including it).  A Range filter can model the union of two disjoint half-bounded intervals by specifying a lower bound that is greater than the upper bound.
	
	† The utility of specifying a range that corresponds to the universal set within the context of data validation is dubious, but the capability is there should the need arise.
**/
class Range
implements Filter
{
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
		Initializes the filter with an object containing one or more of the following keys:
		- **greaterThan**, **gt**, or **>**: The miniumum (exclusive) value.
		- **greaterThanOrEqualTo**, **gte**, or **>=**: The miniumum (inclusive) value.
		- **lessThan**, **lt**, or **<**: The maxiumum (exclusive) value.
		- **lessThanOrEqualTo**, **lte**, or **<=**: The maxiumum (inclusive) value.
		
		@param  object $rules  The upper and/or lower bounds of the range.
	**/
	public function __construct($rules)
	{
		foreach ($rules as $key => $value)
		{
			switch ($key)
			{
				case '>':
				case 'gt':
				case 'greaterThan':
					$this->lower = floatval($value);
					$this->includesLower = false;
					break;
				
				case '>=':
				case 'gte':
				case 'greaterThanOrEqualTo':
				case 'min':
					$this->lower = floatval($value);
					$this->includesLower = true;
					break;
				
				case '<':
				case 'lt':
				case 'lessThan':
					$this->upper = floatval($value);
					$this->includesUpper = false;
					break;
				
				case '<=':
				case 'lte':
				case 'lessThanOrEqualTo':
				case 'max':
					$this->upper = floatval($value);
					$this->includesUpper = true;
					break;
			}
		}
	}
	
	
	/**
		Checks that the given value falls within the specified range.
		
		@param   integer|float  The value to be validated.
		@return  integer|float  The given value, if it falls within the specified range.
		@throws  Exception      When the given value does not fall within the specified range.
	**/
	public function apply($value)
	{
		$lessThanUpper = $this->checkUpper($value);
		$greaterThanLower = $this->checkLower($value);
		
		if ($this->upper < $this->lower && !is_null($this->upper) && !is_null($this->lower))
		{
			if (!$lessThanUpper && !$greaterThanLower)
			{
				throw new Exception($this->getError($value));
			}
		}
		else
		{
			if (!$lessThanUpper || !$greaterThanLower)
			{
				throw new Exception($this->getError($value));
			}
		}
		
		return $value;
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
	
	
	/**
		Generates an error message for a value that falls outside of the range.
		
		@param   mixed   The value that failed validation.
		@return  string  The error message.
	**/
	protected function getError($value)
	{
		$upperText = $this->getUpperText();
		$lowerText = $this->getLowerText();
		
		$message = 'Expecting a value ';
		
		if ($upperText && $lowerText)
		{
			$conjunction = $this->lower > $this->upper ? 'or' : 'and';
			$message .= "$lowerText $conjunction $upperText";
		}
		else if ($lowerText)
		{
			$message .= $lowerText;
		}
		else
		{
			$message .= $upperText;
		}
		
		return $message . ', ' . var_export($value, true) . ' given';
	}
	
	
	/**
		Produces a textual description of the range's upper bound.
		
		@return string
	**/
	protected function getUpperText()
	{
		if (!is_null($this->upper))
		{
			$upperText = 'less than ';
			
			if ($this->includesUpper)
			{
				$upperText .= 'or equal to ';
			}
			
			return $upperText . var_export($this->upper, true);
		}
	}
	
	
	/**
		Produces a textual description of the range's lower bound.
		
		@return string
	**/
	protected function getLowerText()
	{
		if (!is_null($this->lower))
		{
			$lowerText = 'greater than ';
			
			if ($this->includesLower)
			{
				$lowerText .= 'or equal to ';
			}
			
			return $lowerText . var_export($this->lower, true);
		}
	}
}