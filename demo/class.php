<?php

require __DIR__ . '/../lib/autoload.php';


class Demo
{
	use Proper\Properties;
	
	/**
		Alpha is neither readable nor writable.
	**/
	protected $alpha = 'ash';
	
	/**
		Beta is readable but not writable.
		
		@readable
	**/
	protected $beta = 'birch';
	
	/**
		Gamma is both readable and writable.
		
		@readable
		@writable
	**/
	protected $gamma = 'gum';
	
	/**
		Delta only accepts string inputs.
		
		@readable and @writable
		@constraint Type: string
	**/
	protected $delta;
	
	/**
		Epsilon casts any input to an integer.
		
		@readable and @writable
		@filter Integer
	**/
	protected $epsilon;
	
	/**
		Zeta accepts any numeric input and casts it to a float.
		
		@readable and @writable
		@constraint Type: numeric
		@filter Float
	**/
	protected $zeta;
	
	/**
		Eta accepts both integers and booleans and casts to boolean.
		
		@readable and @writable
		@constraint Type: boolean, integer
		@filter Boolean
	**/
	protected $eta;
	
	/**
		Theta only accepts strings that start with "th".
		
		@readable and @writable
		@filter String
		@constraint Regex:  /^th/i
	**/
	protected $theta;
	
	/**
		Iota only accepts DateTime objects.
		
		@readable and @writable
		@constraint Instance: DateTime or null
	**/
	protected $iota;
	
	
	/**
		Kappa only accepts numbers in the range (0, 1].
		
		@readable and @writable
		@constraint Type: real
		@constraint Range: 0 < n <= 1
	**/
	protected $kappa;
	
	/**
		Lambda only accepts strings with a length between 5 and 7 characters
		
		@readable and @writable
		@constraint Type: string
		@constraint Length: 5 <= n <= 7
	**/
	protected $lambda;
	
	
	
	protected $mu;
	protected $nu;
	protected $xi;
	protected $omicron;
	protected $pi;
	protected $rho;
	protected $sigma;
	protected $tau;
	protected $upsilon;
	protected $phi;
	protected $chi;
	protected $psi;
	protected $omega;
}