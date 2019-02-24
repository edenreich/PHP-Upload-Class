<?php

namespace Reich\Traits;

// PHP Classes
use Closure;
use ReflectionFunction;

// Interfaces
use Reich\Interfaces\File;

// Exceptions
use Reich\Exceptions\LogicException;

trait EventListeners
{
	/**
	 * Store the success callback.
	 * 
	 * @var ReflectionFunction
	 */
	private $successCallback;

	/**
	 * Store the error callback.
	 * 
	 * @var ReflectionFunction
	 */
	private $errorCallback;

    /**
	 * Listener for success.
	 *
	 * @param Closure  $callback
	 * @return void
	 */
	public function onSuccess(Closure $callback): void
	{
		$reflector = new ReflectionFunction($callback);

		$this->successCallback = $reflector;
	}

	/**
	 * Listener for failure.
	 *
	 * @param Closure  $callback
	 * @return void
	 */
	public function onError(Closure $callback): void
	{
		$reflector = new ReflectionFunction($callback);

		$this->errorCallback = $reflector;
	}

	/**
	 * Invoke the error callback.
	 * 
	 * @return void
	 */
	protected function invokeErrorCallback(): void
	{
		if ($this->errorCallback === null) {
			return;
		}

		if (isset($this->errorCallback->getParameters()[0]) && $this->errorCallback->getParameters()[0]->getType()->getName() == File::class) {
			foreach ($this->failureUploads as $failureUpload) {
				$this->errorCallback->invoke($failureUpload);
			}
		}
	}

	/**
	 * Invoke the success callback.
	 * 
	 * @return void
	 */
	protected function invokeSuccessCallback()
	{
		if ($this->successCallback === null) {
			return;
		}

		if (isset($this->successCallback->getParameters()[0]) && $this->successCallback->getParameters()[0]->getType()->getName() == File::class) {
			foreach ($this->successfulUploads as $successfulUpload) {				
				$this->successCallback->invoke($successfulUpload);
			}
		}
	}
}
