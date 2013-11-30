<?php namespace Proper;


interface Parser
{
	public function parseAccess($definition);
	public function parseFilters($definition);
}