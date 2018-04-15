<?php

namespace Reich;

use Closure;
use stdClass;
use ReflectionFunction;

// Classes
use Reich\Classes\Request;

// Traits
use Reich\Traits\Encrypter;

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

class Upload
{
	use Encrypter;

	/**
	 * Stores the request object.
	 *
	 * @return \Reich\Classes\Request
	 */
	protected $request;

	/**
	 * Stores the uploaded source input.
	 *
	 * @var array
	 */
	protected $fileInput = [];

	/**
	 * Stores all files.
	 *
	 * @var array
	 */
	protected $files = [];

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
	 * Stores the configurations.
	 *
	 * @var array
	 */
	protected $config = [];

	/**
	 * Debug informations.
	 *
	 * @var array
	 */
	private $_debug = [];

	/**
	 * - Sets all of the attributes with file data.
	 * - Checks if it's single or multiple upload.
	 * - Sorts the files.
	 *
	 * @param string | $input
	 * @return void
	 */
	public function __construct($input = null)
	{
		if (empty($input)) {
			return;
		}

		if (! isset($_FILES[$input]) || empty($_FILES[$input]['name'][0])) {
			return;
		}

		$this->request = new Request;
		$this->fileInput = $_FILES[$input];
		$this->isMultiple = $this->isMultiple($input);
		$this->fileNames = $this->fileInput['name'];
		$this->fileTypes = $this->fileInput['type'];
		$this->fileTempNames = $this->fileInput['tmp_name'];
		$this->fileErrors = $this->fileInput['error'];
		$this->fileSizes = $this->fileInput['size'];
		$this->fileExtensions = $this->getFileExtensions();
		$this->files = $this->sortFiles($this->fileInput);
	}

	/**
	 * Setter for async upload.
	 *
	 * @param bool | $flag
	 * @return $this
	 */
	public function async($flag = true)
	{
		$this->request->async($flag);

		return $this;
	}

	/**
	 * Loads a config file instead.
	 *
	 * @param string | $path
	 * @return array
	 */
	public function loadConfig($path = '')
	{
		$path = rtrim($path, '/');

		$this->config = require $path ?: __DIR__ . '/config/upload.php';
		$this->request->setConfig($this->config);

		return $this->config;
	}

	/**
	 * Checks if its files or file.
	 *
	 * @param string | $input
	 * @return bool
	 */
	protected function isMultiple($input)
	{
		if (count($_FILES[$input]['name']) > 1) {
			return true;
		}

		return false;
	}

	/**
	 * Sets the directory path where you 
	 * want to upload the files(if not specfied,
	 * files will be uploaded to the current directory).
	 *
	 * @param string | $path
	 * @return $this
	 */
	public function setDirectory($path)
	{
		$this->directoryPath = rtrim($path, '/') . '/';

		return $this;
	}

	/**
	 * Get the extentions of the files.
	 *
	 * @return array
	 */
	protected function getFileExtensions()
	{
		$extensions = [];

		foreach ($this->fileNames as $filename)
		{
			$str = explode('.', $filename);
			$str = end($str);
			$extension = strtolower($str);
			$extensions[] = $extension;
		}

		return $extensions;
	}

	/**
	 * Organizes the files 
	 * in a an array of keys for each file.
	 *
	 * @param array | $files
	 * @return array
	 */
	public function sortFiles(array $files)
	{
		$sortedFiles = [];

		foreach ($files as $property => $values) {
			foreach ($values as $key => $value) {
				$sortedFiles[$key] = [
					'name' => $files['name'][$key],
					'encrypted_name' => '',
					'type' => $files['type'][$key],
					'extension' => $this->fileExtensions[$key],
					'tmp_name' => $files['tmp_name'][$key],
					'error' => $files['error'][$key],
					'size' => $files['size'][$key],
					'encryption' => false,
					'success' => false,
					'errorMessage' => ''
				];
			}
		}

		return $sortedFiles;
	}

	/**
	 * Allows to set rules 
	 * for the upload process.
	 *
	 * @param array | $rules
	 * @return $this
	 */
	public function addRules(array $rules)
	{
		foreach ($rules as $rule => $value) {
			switch ($rule) {
				case 'size':
					$this->maxSize = @intval($value);
					break;
				case 'extensions':
					if (is_array($value)) {
						$this->allowedExtensions = $value;
						break;
					}

					if ($extensions = explode('|', $value)) {
						$this->allowedExtensions = $extensions;
						break;
					}

					$this->allowedExtensions[] = $value;
					break;
				default:
					throw new InvalidRuleException;
					break;
			}
		}

		return $this;
	}

	/**
	 * Allows the to set custom error messages.
	 *
	 * @param array | $errorMessages
	 * @return void
	 */
	public function customErrorMessages(array $errorMessages)
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
	public function start()
	{
		if (empty($this->fileInput)) {
			return;
		}

		if (! file_exists($this->directoryPath)) {
			throw new FolderNotExistException;
		}

		foreach ($this->files as $key => &$file) {
			if ($this->fileIsNotValid($file)) {
				$file['success'] = false;
	    		continue;
	    	}

			if (! empty($this->config) && $this->config['protocols']['default'] == 'ftp') {
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

		//$this->request->postAsyncHandlers();
	}

	/**
	 * Uploads a file using ftp protocol.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function uploadUsingFtp(&$file) 
	{
		// @todo upload the file using ftp protocol
	}

	/**
	 * Uploads a file using http protocol.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function uploadUsingHttp(&$file) 
	{
		if ($this->request->shouldBeAsync() && ! $this->request->header('User-Agent') == 'Curl') {
			$this->addPostAsyncHandler($file);
			return true;
		}

		if ($this->shouldBeEncrypted($file)) {
			return move_uploaded_file($file['tmp_name'], $this->directoryPath . $file['encrypted_name']);
		} else {
			return move_uploaded_file($file['tmp_name'], $this->directoryPath . $file['name']);
		}
	}

	/**
	 * Listener for success.
	 *
	 * @param Closure | $callback
	 * @param bool | $asObject
	 * @return void
	 */
	public function success(Closure $callback, $asObject = true)
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
	public function error(Closure $callback, $asObject = true)
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

	/**
	 * Creates the directory if not exists.
	 * 
	 * @param bool | $create
	 * @return void
	 */
	public function create($create = false)
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
	public function getAllowedExtensions()
	{
		return ($this->allowedExtensions) ?: '';
	}

	/**
	 * Retrieves the maximum uploading size.
	 *
	 * @return integer
	 */
	public function getMaxUploadingSize()
	{
		return $this->maxSize;
	}

	/**
	 * Checks if extensions allowed.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function extensionsAllowed(&$file)
	{
		if (empty($this->allowedExtensions) && empty($this->fileExtensions)) {
			return;
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
	protected function hasCustomMessage($type)
	{
		return isset($this->customErrorMessages[$type]);
	}

	/**
	 * Checks if the file size allowed.
	 *
	 * @param array | $file
	 * @return bool
	 */
	protected function maxSizeOk($file)
	{
		if (empty($this->fileSizes)) {
			return;
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
	protected function defaultErrorMessage($type, $file = null)
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
	protected function fileIsNotValid(&$file)
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
	public function unsuccessfulFilesHas()
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
	public function successfulFilesHas()
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
	public function errorFiles()
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
	public function successFiles()
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
	public function displayErrors()
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
	public function displaySuccess()
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
	public static function submitted()
	{
		if (empty($_FILES)) {
			return false;
		}

		return true;
	}

	/**
	 * Gets the errors array to give 
	 * feedback to the developer.
	 *
	 * @return array
	 */
	public function debug()
	{
		return $this->_debug;
	}
}
