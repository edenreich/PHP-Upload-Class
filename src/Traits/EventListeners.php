<?php

namespace Reich\Traits;

use Closure;
use ReflectionFunction;

trait EventListeners
{
    /**
	 * Listener for success.
	 *
	 * @param Closure | $callback
	 * @param bool | $asObject
	 * @return void
	 */
	public function success(Closure $callback, $asObject = true): void
	{
		$reflector = new ReflectionFunction($callback);

		if (isset($reflector->getParameters()[0]) && $reflector->getParameters()[0]->name == 'file') {
			
			foreach ($this->successfulUploads as $successfulUpload) {
				$successfulUpload = ($asObject) ? json_decode(json_encode($successfulUpload)) 
								: $successfulUpload;
				
				$reflector->invoke($successfulUpload);
			}

		} else {
			
			throw new InvalidArgumentException;
		}
	}

	/**
	 * Listener for failure.
	 *
	 * @param Closure $callback
	 * @param bool | $asObject
	 * @return void
	 */
	public function error(Closure $callback, $asObject = true): void
	{
		$reflector = new ReflectionFunction($callback);

		if (isset($reflector->getParameters()[0]) && $reflector->getParameters()[0]->name == 'file') {
			
			foreach ($this->failureUploads as $failureUpload) {
				$failureUpload = ($asObject) ? json_decode(json_encode($failureUpload)) 
							     : $failureUpload;
				
				$reflector->invoke($failureUpload);
			}

		} else {
			
			throw new InvalidArgumentException;
		
		}
	}
}
