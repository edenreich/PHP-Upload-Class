<?php

namespace Reich\Exceptions;

class InvalidEncryptionKeyException extends \Exception 
{
	/**
	 * Initalize exception message.
	 *
	 * @return void
	 */
	public function __construct($message = 'Please go to Upload.php file and set manually a key inside the const KEY of 32 characters to encrypt your files. keep this key in safe place as well. you can call $this->generateMeAKey() to generate a random 32 characters key')
	{
		parent::__construct($message);
	}
}
