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
	 * Stores the uploaded source input.
	 *
	 * @var array
	 */
	protected $fileInput = [];

	/**
	 * Stores all the file errors.
	 *
	 * @var array
	 */
	protected $fileErrors = [];

	/**
	 * Stores all the file sizes.
	 *
	 * @var array
	 */
	protected $fileSizes = [];

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
	 * Debug informations.
	 *
	 * @var array
	 */
	private $_debug = [];

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
	 * Setter for async upload.
	 *
	 * @param bool  $flag
	 * @return $this
	 */
	public function async($flag = true): UploadInterface
	{
		$this->config->set('async', $flag);

		return $this;
	}

	/**
	 * Sets the directory path where you 
	 * want to upload the files(if not specfied,
	 * files will be uploaded to the current directory).
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
	 * Uploads the file asyncrounsly.
	 *
	 * @return bool
	 */
	protected function uploadAsync(): bool
	{
		foreach ($this->files as $key => &$file) {
			if ($this->fileIsNotValid($file)) {
				$file['success'] = false;
	    		continue;
	    	}

	    	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];

	    	// Registers each file for a separate request.
			$this->request->register($url, $file);
		}
	
		// Executes all request asyncrously.
		$responses = $this->request->executeAll();
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

			$uploaded = move_uploaded_file($file['tmp_name'], $this->directoryPath.'/'.$file['name']);

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
	 * Checks if file validation fails.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function fileIsNotValid(&$file): bool
	{
		if ($file['error'] !== UPLOAD_ERR_OK) {
	    	$this->_debug[] = 'The file ' . $file['name'] . ' couldn\'t be uploaded. Please ensure
	    							your php.ini file allow this size of files to be uploaded';
	    	$file['errorMessage'] = 'Invalid File: ' . $file['name'];
	    	return false;
	    }

		if ($this->extensionsAllowed($file) && $this->maxSizeOk($file)) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if the upload was unsuccessful.
	 *
	 * @return bool
	 */
	public function unsuccessfulFilesHas(): bool
	{
		foreach ($this->files as $file) {
			if ($file['success'] == false && ! empty($file['errorMessage'])) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Checks if the upload was successful.
	 *
	 * @return bool
	 */
	public function successfulFilesHas(): bool
	{
		foreach ($this->files as $file) {
			if ($file['success'] == true) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function errorFiles(): array
	{
		$failedUploads = [];

		foreach ($this->files as $key => $file) {
			if ($file['success'] == true) {
				continue;
			}

			$failedFile = new stdClass;

			$failedFile->name = $file['name'];

			if ($this->shouldBeEncrypted($file)) {
				$failedFile->encryptedName = $file['encrypted_name'];
			}

			$failedFile->type = $file['type'];
			$failedFile->extension = $file['extension'];
			$failedFile->size = $file['size'];
			$failedFile->error = $file['error'];

			if (! empty($file['errorMessage'])) {
				$failedFile->errorMessage = $file['errorMessage'];
			}

			$failedUploads[] = $failedFile;
		}

		return $failedUploads;
	}

	/**
	 * Retrieves the errors array 
	 * to give some feedback to the user.
	 *
	 * @return array
	 */
	public function successFiles(): array
	{
		$successfulUploads = [];

		foreach ($this->files as $key => $file) {
			if ($file['success'] == false) {
				continue;
			}

			$successfulFile = new stdClass;

			$successfulFile->name = $file['name'];

			if ($this->shouldBeEncrypted($file)) {
				$successfulFile->encryptedName = $file['encrypted_name'];
			}

			$successfulFile->type = $file['type'];
			$successfulFile->extension = $file['extension'];
			$successfulFile->size = $file['size'];

			$successfulUploads[] = $successfulFile;
		}

		return $successfulUploads;
	}

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displayErrors(): void
	{
		foreach ($this->errorFiles() as $file) {
	      echo '<div class="alert alert-danger">couldn\'t upload ' . $file->name .'. '. $file->errorMessage . '</div><br/>';
	    }
	}

	/**
	 * Displays the errors 
	 * nicely formated with bootstraps.
	 *
	 * @return void
	 */
	public function displaySuccess(): void
	{
		foreach ($this->successFiles() as $file) {
	      echo '<div class="alert alert-success">' . $file->name .' uploaded successfuly</div><br/>';
	    }
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
