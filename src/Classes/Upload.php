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
	 * Stores all the file names.
	 *
	 * @var array
	 */
	protected $fileNames = [];

	/**
	 * Stores all the file types.
	 *
	 * @var array
	 */
	protected $fileTypes = [];

	/**
	 * Stores all the file temporary names.
	 *
	 * @var array
	 */
	protected $fileTempNames = [];

	/**
	 * Stores all the file extensions.
	 *
	 * @var array
	 */
	protected $fileExtensions = [];

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
	 * Stores the path of the upload folder.
	 * by default will be uploaded to root.
	 *
	 * @var array
	 */
	protected $directoryPath = '/';

	/**
	 * Stores the allowed files extensions.
	 *
	 * @var array
	 */
	protected $allowedExtensions = ['jpg', 'png'];

	/**
	 * Stores the maximum allowed size to upload.
	 *
	 * @var integer
	 */
	protected $maxSize = null;

	/**
	 * If the upload is multiple files.
	 *
	 * @var bool
	 */
	protected $isMultiple = false;

	/**
	 * Stores all custom error messages.
	 *
	 * @var array
	 */
	protected $customErrorMessages = [];

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
	 * Checks if its files or file.
	 *
	 * @param string | $input
	 * @return bool
	 */
	protected function isMultiple($input): bool
	{
		if (is_array($_FILES[$input]['name']) && count($_FILES[$input]['name']) > 1) {
			return true;
		}

		return false;
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
		$this->directoryPath = rtrim($path, '/');

		return $this;
	}

	/**
	 * Get the extentions of the files.
	 *
	 * @return array
	 */
	protected function getFileExtensions(): array
	{
		$extensions = [];

		foreach ($this->fileNames as $filename) {
			$str = explode('.', $filename);
			$str = end($str);
			$extension = strtolower($str);
			$extensions[] = $extension;
		}

		return $extensions;
	}

	/**
	 * Allows to set rules 
	 * for the upload process.
	 *
	 * @param array | $rules
	 * @return $this
	 */
	public function addRules(array $rules): UploadInterface
	{
        $this->validator->setRules($rules);
        
        return $this;
	}

	/**
	 * Allows the to set custom error messages.
	 *
	 * @param array | $errorMessages
	 * @return void
	 */
	public function customErrorMessages(array $errorMessages): void
	{
		foreach ($errorMessages as $ruleName => $customMessage)
		{
			switch ($ruleName)
			{
				case 'size':
					$this->customErrorMessages[$ruleName] = $customMessage;
					break;
				case 'extensions':
					$this->customErrorMessages[$ruleName] = $customMessage;
					break;
				default:
					throw new InvalidRuleException;
					break;
			}
		}
	}

	/**
	 * Starts the upload process.
	 *
	 * @return void
	 */
	public function start(): void
	{
		if (empty($this->fileInput)) {
			return;
		}

		if (! file_exists($this->directoryPath)) {
			throw new FolderNotExistException;
		}

		if (! empty($this->config) && $this->config['protocols']['default'] == 'ftp') {
			$this->upload(true);
		}

		// This block will be skipped if User-Agent is Curl.
		if ($this->request->shouldBeAsync() && $this->request->header('User-Agent') != 'Curl') {
			$this->uploadAsync();
		} else {
			$this->upload();
		}
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
	 * Process the files or file and upload them.
	 *
	 * @param bool | $ftp
	 * @return bool
	 */
	protected function upload($ftp = false): bool
	{
		foreach ($this->files as $key => &$file) {
			if ($this->fileIsNotValid($file)) {
				$file['success'] = false;
	    		continue;
	    	}

			if ($ftp === true) {
				$uploaded = $this->uploadUsingFtp($file);
			} else {
				$uploaded = $this->uploadUsingHttp($file);
			}

			if ($uploaded) {
				$file['success'] = true;
				$this->successfulUploads[] = $file;
			} else {
				$file['success'] = false;
				$this->failureUploads[] = $file;
			}
		}

		// Once all files were uploaded, close the connection.
		if ($ftp) {
			ftp_close($this->FTPConnection);
		}
	}

	/**
	 * Uploads a file using ftp protocol.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function uploadUsingFtp(&$file): bool
	{
		$config = $this->config['protocols']['ftp'];

		if (is_null($this->FTPConnection)) {
			$this->FTPConnection = ftp_connect($config['host'], $config['port']) or die('Could not connect to FTP server!');

			ftp_login($this->FTPConnection, $config['username'], $config['password']);
			ftp_pasv($this->FTPConnection, true);
		}

		return ftp_put($this->FTPConnection, $file['name'], $file['tmp_name'], FTP_BINARY);
	}

	/**
	 * Uploads a file using http protocol.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function uploadUsingHttp(&$file): bool
	{
		if ($this->shouldBeEncrypted($file)) {
			return move_uploaded_file($file['tmp_name'], $this->directoryPath.'/'.$file['encrypted_name']);
		}

		return move_uploaded_file($file['tmp_name'], $this->directoryPath.'/'.$file['name']);
	}

	/**
	 * Creates the directory if not exists.
	 * 
	 * @param bool | $create
	 * @return void
	 */
	public function create($create = false): void
	{
		if ($create == false) {
			return;
		}

		if (! file_exists($this->directoryPath)) {
			if (! @mkdir($this->directoryPath, 0777, true)) {
				throw new PermissionDeniedException;
			}
		}
	}

	/**
	 * Retrieves the allowed extensions.
	 *
	 * @return array
	 */
	public function getAllowedExtensions(): array
	{
		return ($this->allowedExtensions) ?: '';
	}

	/**
	 * Retrieves the maximum uploading size.
	 *
	 * @return int
	 */
	public function getMaxUploadingSize(): int
	{
		return $this->maxSize;
	}

	/**
	 * Checks if extensions allowed.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function extensionsAllowed(&$file): bool
	{
		if (empty($this->allowedExtensions) && empty($this->fileExtensions)) {
			return true;
		}

		if (in_array($file['extension'], $this->allowedExtensions)) {
			return true;
		}

		$file['error'] = 1;
		$file['success'] = false;
		$file['errorMessage'] = ($this->hasCustomMessage('extensions')) ? $this->customErrorMessages['extensions']
																		: $this->defaultErrorMessage('extensions');
		return false;
	}

	/**
	 * Checks if there are custom message by type.
	 *
	 * @param string | $type
	 * @return bool
	 */
	protected function hasCustomMessage($type): bool
	{
		return isset($this->customErrorMessages[$type]);
	}

	/**
	 * Checks if the file size allowed.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function maxSizeOk($file): bool
	{
		if (empty($this->fileSizes)) {
			return true;
		}

		if (empty($this->maxSize)) {
			return true;
		}

		if ($file['size'] < ($this->maxSize * 1000)) {
			return true;
		}

		$file['errorMessage'] = ($this->hasCustomMessage('size')) ? $this->customErrorMessages['size']
																  : $this->defaultErrorMessage('size', $file);

		return false;
	}

	/**
	 * Retrieves a default error message.
	 *
	 * @param string | $type
	 * @param array | $file
	 * @return string
	 */
	protected function defaultErrorMessage($type, $file = null): string
	{
		switch ($type) {
			case 'size':
				return "Sorry, but your file, " . $file['name'] . ", is too big. maximal size allowed " . $this->maxSize . " Kbyte";
			case 'extensions':
				return "Sorry, but only " . implode( ", " , $this->allowedExtensions ) . " files are allowed.";
		}

		return '';
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
