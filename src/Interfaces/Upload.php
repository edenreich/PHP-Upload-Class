<?php

namespace Reich\Interfaces;

use Reich\Interfaces\Validator;

interface Upload
{
	/**
	 * Set the directory path.
	 *
	 * @param string  $path
	 * @return $this
	 */
	public function setDirectory($path): Upload;

	/**
	 * Create the directory if not exists.
	 * 
	 * @param bool|null  $create
	 * @return void
	 */
	public function create(?bool $create = null): void;

	/**
	 * Retrieve the validator.
	 * 
	 * @return \Reich\Interfaces\Validator
	 */
	public function validator(): Validator;

	/**
	 * Starts the upload process.
	 *
	 * @return void
	 */
	public function start();

	/**
	 * Checks if an upload 
	 * form has been submitted.
	 *
	 * @return bool
	 */
	public static function submitted(): bool;
}
