<?php

namespace Source\Exceptions;

class InvalidRuleException extends \Exception 
{
	/**
	 * Initalize exception message.
	 *
	 * @return void
	 */
	public function __construct($message = 'Sorry but this rule you specfied does not exist')
	{
		parent::__construct($message);
	}
}