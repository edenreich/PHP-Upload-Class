<?php

namespace Reich\Interfaces;

interface Upload
{
	/**
	 * Setter for async upload.
	 *
	 * @param bool | $flag
	 * @return $this
	 */
	public function async($flag = true): Upload;

	/**
	 * Loads a config file instead.
	 *
	 * @param string | $path
	 * @return void
	 */
	public function loadConfig($path = ''): void;

	/**
	 * Sets the directory path where you 
	 * want to upload the files(if not specfied,
	 * files will be uploaded to the current directory).
	 *
	 * @param string | $path
	 * @return $this
	 */
	public function setDirectory($path): Upload;

	/**
	 * Organizes the files 
	 * in a an array of keys for each file.
	 *
	 * @param array | $files
	 * @return array
	 */
	public function sortFiles(array $files): array;

	/**
	 * Allows to set rules 
	 * for the upload process.
	 *
	 * @param array | $rules
	 * @return $this
	 */
	public function addRules(array $rules): Upload;

	/**
	 * Allows the to set custom error messages.
	 *
	 * @param array | $errorMessages
	 * @return void
	 */
	public function customErrorMessages(array $errorMessages): void;

	/**
	 * Starts the upload process.
	 *
	 * @return void
	 */
	public function start();

	/**
	 * Listener for success.
	 *
	 * @param Closure | $callback
	 * @param bool | $asObject
	 * @return void
	 */
	public function success(Closure $callback, $asObject = true): void;

	/**
	 * Listener for failure.
	 *
	 * @param Closure $callback
	 * @param bool | $asObject
	 * @return void
	 */
	public function error(Closure $callback, $asObject = true): void;

	/**
	 * Creates the directory if not exists.
	 * 
	 * @param bool | $create
	 * @return void
	 */
	public function create($create = false): void;

	/**
	 * Retrieves the allowed extensions.
	 *
	 * @return array
	 */
	public function getAllowedExtensions(): array;

	/**
	 * Retrieves the maximum uploading size.
	 *
	 * @return int
	 */
	public function getMaxUploadingSize(): int;

	/**
	 * Checks if the upload was unsuccessful.
	 *
	 * @return bool
	 */
	public function unsuccessfulFilesHas(): bool;

	/**
	 * Checks if the upload was successful.
	 *
	 * @return bool
	 */
	public function successfulFilesHas(): bool;

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function errorFiles(): array;

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function successFiles(): array;

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displayErrors(): void;

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displaySuccess(): void;

	/**
	 * Checks if an upload 
	 * form has been submitted.
	 *
	 * @return bool
	 */
	public static function submitted(): bool;
}
