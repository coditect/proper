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
	
	
	public function __construct($params)
	{
		if (isset($params->{'>'}))
		{
			$this->min = floatval($params->{'>'});
			$this->minExclusive = true;
		}
		else if (isset($params->{'>='}))
		{
			$this->min = floatval($params->{'>='});
			$this->minExclusive = false;
		}
		
		if (isset($params->{'<'}))
		{
			$this->max = floatval($params->{'>'});
			$this->maxExclusive = true;
		}
		else if (isset($params->{'<='}))
		{
			$this->max = floatval($params->{'<='});
			$this->maxExclusive = false;
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