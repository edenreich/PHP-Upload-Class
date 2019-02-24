<?php

namespace Reich\Classes;

use stdClass;

// Classes
use Reich\Classes\Request;

// Interfaces
use Reich\Interfaces\Config;
use Reich\Interfaces\Input;
use Reich\Interfaces\Validator;
use Reich\Interfaces\Upload as UploadInterface;

// Traits
use Reich\Traits\Encrypter;
use Reich\Traits\EventListeners;

// Exceptions
use InvalidArgumentException;
use Reich\Exceptions\InvalidRuleException;
use Reich\Exceptions\FolderNotExistException;
use Reich\Exceptions\PermissionDeniedException;

/**
 * Upload class that handles multiple file uploads.
 *
 * @author Eden Reich <eden.reich@gmail.com>
 * @license MIT
 * @version 2.0
 */

class Upload implements UploadInterface
{
	use Encrypter, EventListeners;

	/**
	 * Stores the successful uploads.
	 *
	 * @var array
	 */
	protected $successfulUploads = [];

	/**
	 * Stores the faliure uploads.
	 *
	 * @var array
	 */
	protected $failureUploads = [];

	/**
	 * Stores the FTP connection.
	 *
	 * @var resource
	 */
	protected $FTPConnection = null;

	/**
	 * Store the config.
	 * 
	 * @var \Reich\Interfaces\Config
	 */
	private $config;

	/**
	 * Store the input.
	 * 
	 * @var \Reich\Interfaces\Input
	 */
	private $input;

	/**
	 * Store the validator.
	 * 
	 * @var \Reich\Interfaces\Validator
	 */
	private $validator;

	/**
	 * Store the request.
	 *
	 * @var \Reich\Classes\Request
	 */
	protected $request;

	/**
	 * Stores the files.
	 * 
	 * @var array
	 */
	protected $files;

	/**
	 * Initialize:
	 * 	- Config
	 *  - Input
	 *  - Validator
	 *  - Request
	 *
	 * @param \Reich\Classes\Config  $config
	 * @param \Reich\Classes\Input  $input
	 * @param \Reich\Classes\Validator  $validator
	 * @param \Reich\Classes\Request  $request
	 */
	public function __construct(Config $config, Input $input, Validator $validator, Request $request)
	{
		if ($input->isEmpty()) {
			return;
		}

		$this->config = $config;
		$this->input = $input;
		$this->validator = $validator;
		$this->request = $request;
		$this->files = $input->getFiles();
	}

	/**
	 * Set the directory path.
	 *
	 * @param string  $path
	 * @return $this
	 */
	public function setDirectory($path): UploadInterface
	{
		$path = rtrim($path, '/');

		$disks = $this->config->get('disks');

		$disks['local']['path'] = $path;

		$this->config->set('disks', $disks);

		return $this;
	}

	/**
	 * Create the directory if not exists.
	 * 
	 * @param bool|null  $create
	 * @return void
	 */
	public function create(?bool $create = null): void
	{
		$disks = $this->config->get('disks');
		$create = $create ?? $disks['local']['create'];

		if ($create == false) {
			return;
		}

		$path = $disks['local']['path'];

		if (! file_exists($path)) {
			if (! @mkdir($path, 0777, true)) {
				throw new PermissionDeniedException;
			}
		}
	}

	/**
	 * Retrieve the validator.
	 * 
	 * @return \Reich\Interfaces\Validator
	 */
	public function validator(): Validator
	{
		return $this->validator;
	}

	/**
	 * Starts the upload process.
	 *
	 * @return void
	 */
	public function start(): void
	{
		if ($this->input->isEmpty()) {
			return;
		}

		if (! file_exists($this->config->get('disks')['local']['path'])) {
			throw new FolderNotExistException;
		}

		$files = $this->input->getFiles();

		if ($this->validator->fails()) {
			$errors = $this->validator->errors();
			// @todo display the error messages
			return;
		}

		if ($this->config->get('protocols')['default'] == 'http') {
			$this->uploadUsingHTTP($files);
		}

		if ($this->config->get('protocols')['default'] == 'ftp') {
			$this->uploadUsingFTP($files);
		}

		$this->invokeErrorCallback();
		$this->invokeSuccessCallback();
	}

	/**
	 * Uploads a file using http protocol.
	 *
	 * @param array<File>  $file
	 * @return void
	 */
	protected function uploadUsingHTTP(array $files): void
	{
		foreach ($files as $file) {
			if ($file->isNotValid()) {
				$file->failed();
				$this->failureUploads[] = $file;
	    		continue;
			}

			$uploaded = move_uploaded_file($file->getTmpName(), $this->config->get('disks')['local']['path'].'/'.$file->getName());

			if ($uploaded) {
				$file->succeed();
				$this->successfulUploads[] = $file;
			} else {
				$file->failed();
				$this->failureUploads[] = $file;
			}
		}

		// if ($this->request->shouldBeAsync() && $this->request->header('User-Agent') != 'Curl') {
		// 	$this->uploadAsync();
		// } else {
		// 	$this->upload();
		// }
	}

	/**
	 * Uploads a file using ftp protocol.
	 *
	 * @param array<File>  $file
	 * @return bool
	 */
	protected function uploadUsingFTP(array $files): void
	{
		$config = $this->config->get('protocols')['ftp'];

		if (is_null($this->FTPConnection)) {
			$this->FTPConnection = ftp_connect($config['host'], $config['port']) or die('Could not connect to FTP server!');

			ftp_login($this->FTPConnection, $config['username'], $config['password']);
			ftp_pasv($this->FTPConnection, true);
		}

		ftp_put($this->FTPConnection, $file['name'], $file['tmp_name'], FTP_BINARY);

		ftp_close($this->FTPConnection);
	}

	/**
	 * Checks if an upload 
	 * form has been submitted.
	 *
	 * @return bool
	 */
	public static function submitted(): bool
	{
		if (empty($_FILES)) {
			return false;
		}

		return true;
	}
}
