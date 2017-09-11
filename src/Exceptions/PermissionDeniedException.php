<?php

namespace Reich\Exceptions;

class PermissionDeniedException extends \Exception 
{
	/**
	 * Initalize exception message.
	 *
	 * @return void
	 */
	public function __construct($message = 'Server configuration denies the creation of the folder')
	{
		parent::__construct($message);
	}
}