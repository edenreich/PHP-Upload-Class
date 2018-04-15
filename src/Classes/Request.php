<?php

namespace Reich\Classes;

use Reich\Traits\AsyncRequest;

class Request
{
	use AsyncRequest;

	/**
	 * Stores the configurations.
	 *
	 * @return array
	 */
	protected $config;

	/**
	 * Setter for the configurations.
	 *
	 * @return void
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}

	/**
	 * Retrieves a client's header.
	 *
	 * @param string | $name
	 * @return string
	 */
	public function header($name)
	{
		switch($name) 
		{
			case 'User-Agent': $name = 'HTTP_USER_AGENT'; break;
			case 'Content-Type': $name = 'HTTP_CONTENT_TYPE'; break;
		}

		return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
	}
}