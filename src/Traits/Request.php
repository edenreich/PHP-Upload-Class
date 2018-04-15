<?php

namespace Reich\Traits;

trait Request
{
	/**
	 * Retrieves a client's header.
	 *
	 * @param string | $name
	 * @return string
	 */
	protected function header($name)
	{
		switch($name) 
		{
			case 'User-Agent': $name = 'HTTP_USER_AGENT'; break;
			case 'Content-Type': $name = 'HTTP_CONTENT_TYPE'; break;
		}

		return isset($_SERVER[$name]) ? $_SERVER[$name] : null;
	}
}