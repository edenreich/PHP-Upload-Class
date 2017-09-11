<?php

namespace Reich\Exceptions;

class FolderNotExistException extends \Exception 
{
	/**
	 * Initalize exception message.
	 *
	 * @return void
	 */
	public function __construct($message = 'Sorry, but this path does not exists. you can also set create() to true.
									 Example: $this->setDirectory(\'images\')->create(true);')
	{
		parent::__construct($message);
	}
}