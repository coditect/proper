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
		@filter Type {"allow": "string"}
	**/
	protected $delta;
	
	/**
		Epsilon casts any input to an integer.
		
		@readable and @writable
		@filter Type {"force": "integer"}
	**/
	protected $epsilon;
	
	/**
		Zeta accepts any numeric input and casts it to a float.
		
		@readable and @writable
		@filter Type {"allow": "numeric", "force": "float"}
	**/
	protected $zeta;
	
	/**
		Eta accepts both integers and booleans and casts to boolean.
		
		@readable and @writable
		@filter Type {"allow": ["boolean", "integer"], "force": "boolean"}
	**/
	protected $eta;
	
	/**
		Theta only accepts strings that start with "th".
		
		@readable and @writable
		@filter Type {"allow": "string"}
		@filter Regex "/^th/i"
	**/
	protected $theta;
	
	/**
		Iota only accepts DateTime objects.
		
		@readable and @writable
		@filter Instance {"class": "DateTime"}
	**/
	protected $iota;
	
	
	/**
		Kappa only accepts numbers in the range (0, 1].
		
		@readable and @writable
		@filter Type {"allow": "numeric", "force": "float"}
		@filter Range {">": 0, "<=": 1}
	**/
	protected $kappa;
	
	
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