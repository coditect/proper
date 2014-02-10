<?php namespace Proper\Filter;

use \Exception;
use \Proper\Filter;


class Range
implements Filter
{
	protected $min;
	protected $minExclusive;
	protected $max;
	protected $maxExclusive;
	
	
	/**
		Initializes the filter with an object containing one or more of the following keys:
		- **greaterThan**, **gt**, or **>**: The miniumum (exclusive) value.
		- **greaterThanOrEqualTo**, **gte**, or **>=**: The miniumum (inclusive) value.
		- **lessThan**, **lt**, or **<**: The maxiumum (exclusive) value.
		- **lessThanOrEqualTo**, **lte**, or **<=**: The maxiumum (inclusive) value.
		
		@param   object $rules  The miniumum and/or maxiumum values.
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
					$this->min = floatval($value);
					$this->minExclusive = true;
					break;
				
				case '>=':
				case 'gte':
				case 'greaterThanOrEqualTo':
				case 'min':
					$this->min = floatval($value);
					$this->minExclusive = false;
					break;
				
				case '<':
				case 'lt':
				case 'lessThan':
					$this->max = floatval($value);
					$this->maxExclusive = true;
					break;
				
				case '>=':
				case 'lte':
				case 'lessThanOrEqualTo':
				case 'max':
					$this->max = floatval($value);
					$this->maxExclusive = false;
					break;
			}
		}
	}
	
	
	public function apply($value)
	{
		$minSatisfied = $this->checkMin($value);
		$maxSatisfied = $this->checkMax($value);
		
		if ($this->min > $this->max && !is_null($this->min) && !is_null($this->max))
		{
			if (!$minSatisfied && !$maxSatisfied)
			{
				throw new Exception($this->getError($value));
			}
		}
		else
		{
			if (!$minSatisfied || !$maxSatisfied)
			{
				throw new Exception($this->getError($value));
			}
		}
		
		return $value;
	}
	
	
	protected function checkMin($value)
	{
		if (!is_null($this->min))
		{
			if ($this->minExclusive)
			{
				return $value > $this->min;
			}
			else
			{
				return $value >= $this->min;
			}
		}
		
		return true;
	}
	
	
	protected function checkMax($value)
	{
		if (!is_null($this->max))
		{
			if ($this->maxExclusive)
			{
				return $value < $this->max;
			}
			else
			{
				return $value <= $this->max;
			}
		}
		
		return true;
	}
	
	
	protected function getError($value)
	{
		$minExpectation = $this->getMinExpectation();
		$maxExpectation = $this->getMaxExpectation();
		
		$message = 'Expecting a value ';
		
		if ($minExpectation && $maxExpectation)
		{
			$joiner = $this->min > $this->max ? 'or' : 'and';
			$message .= "$minExpectation $joiner $maxExpectation";
		}
		else if ($minExpectation)
		{
			$message .= $minExpectation;
		}
		else
		{
			$message .= $maxExpectation;
		}
		
		return $message . ', ' . var_export($value, true) . ' given';
	}
	
	
	protected function getMinExpectation()
	{
		if (!is_null($this->min))
		{
			$minExpectation = 'greater than ';
			
			if (!$this->minExclusive)
			{
				$minExpectation .= 'or equal to ';
			}
			
			return $minExpectation . var_export($this->min, true);
		}
	}
	
	
	protected function getMaxExpectation()
	{
		if (!is_null($this->max))
		{
			$maxExpectation = 'less than ';
			
			if (!$this->maxExclusive)
			{
				$maxExpectation .= 'or equal to ';
			}
			
			return $maxExpectation . var_export($this->max, true);
		}
	}
}