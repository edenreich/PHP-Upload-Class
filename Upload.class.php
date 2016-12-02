<?php


/*
|--------------------------------------------------------------------------
| Upload Class 
|--------------------------------------------------------------------------
| This class handle uploading of multiple files
| 
|
*/
class Upload
{
	const KEY = 'fc01e8d00a90c1d392ec45459deb6f12'; // Please set your key for encryption here

	protected $_fileInput = array();

	protected $_files = array();
	
	protected $_fileNames = array();

	protected $_fileTypes = array();
	
	protected $_fileTempNames = array();
			
	protected $_fileExtensions = array();
			
	protected $_fileErrors = array();
	
	protected $_fileSizes = array();

	protected $_directoryPath = '/';

	protected $_debugErrors = array();

	protected $_encryptedFileNames = array();

	protected $_allowedExtensions = array('jpg', 'png');

	protected $_maxSize = null;
	
	protected $isMultiple = false;

	protected $_fileTypesToEncrypt = array();

	/**
	 * Setting all the attributes with file data and check if it's single or multiple upload 
	 */
	public function __construct($input = null)
	{
		if(empty($input) && !isset($_FILES[$input]))
			return;
		
		$this->_fileInput = $_FILES[$input];
		$this->isMultiple = $this->isMultiple($this->_fileInput);
		
		$this->_fileNames = $this->_fileInput['name'];
		$this->_fileTypes = $this->_fileInput['type'];
		$this->_fileTempNames = $this->_fileInput['tmp_name'];
		$this->_fileErrors = $this->_fileInput['error'];
		$this->_fileSizes = $this->_fileInput['size'];
		$this->_fileExtensions = $this->getFileExtensions();

		$this->_files = $this->orderFiles($this->_fileInput);
	}

	/**
	 * This method organized the files in a an array of keys for each file.
	 *
	 * @param Array | $files
	 * 
	 * @return Array | $sortedFiles
	 */
	public function orderFiles(Array $files)
	{
		$sortedFiles = array(); 
	
		foreach($files as $property => $values)
		{
			foreach($values as $key => $value) 
			{
				$sortedFiles[$key] = array(
											'name' => $files['name'][$key],
											'encrypted_name' => '',
											'type' => $files['type'][$key],
											'extension' => $this->_fileExtensions[$key],
											'tmp_name' => $files['tmp_name'][$key],
											'error' => $files['error'][$key],
											'size' => $files['size'][$key],
											'encryption' => false,
											'success' => false,
											'message' => '',
										);
				
			}
					
		}

		return $sortedFiles;
	}


	/**
	 * This method check if the file is set. normally when the user submits the form.
	 */
	public static function formIsSubmitted()
	{
		if(empty($_FILES))
			return false;

		if($_FILES['file']['size'] <= 0)
			return false;
		
		return true;
	}

	/**
	 * This method checks if its files or file.
	 *
	 * @param Array | $input
	 * 
	 * @return Boolean
	 */
	protected function isMultiple(Array $input)
	{
		if(count($_FILES['file']['name']) > 1)
			return true;
		
		return false;
	}

	/**
	 *	Get the file data array
	 *
	 * @return Array | $this->_fileInput
	 */
	public function getFileData()
	{
		return $this->_fileInput;
	}

	/**
	 * Get the name/names of the file/files
	 *
	 * @return String or Array
	 */
	public function getFileName($index)
	{
		return $this->_fileNames[$index];
	}

	/**
	 *	Get the temp-name/temp-names of the file/files
	 *
	 *	@return String or Array
	 *
	 */
	public function getFileTempName()
	{
		return $this->_fileTempName;
	}

	/**
	 * Get the extention/extentions of the file/files
	 *
	 * @return String or Array
	 */
	protected function getFileExtensions()
	{
		$extensions = array();

		foreach($this->_fileNames as $filename)
		{
			$str = end(explode('.', $filename));
			$extension = strtolower($str);
			$extensions[] = $extension;
		}
		return $extensions;

	}

	/**
	 * Get the size/sizes of the file/files
	 *
	 * @return Integer or Array
	 */
	public function getFileSize()
	{
		return $this->_fileSize;
	}

	/**
	 *	Get the path/paths of the file/files
	 *
	 *	@return String or Array
	 */
	public function getUploadDirectory()
	{
		if(!file_exists($this->_directoryPath))
		{
			$this->_debugErrors[] = 'Sorry, but this directory does not exist yet. You can allow directory creating
									 By setting create() to true. Example: $this->setDirectory(\'images\')->create(true);';
			return;
		}

		return $this->_directoryPath;
	}

	/**
	 * Set the path directory where you want to upload the files(if not specfied file/files 
	 * will be uploaded to the current directory)
	 *
	 * @param String
	 *
	 * @return Object | $this
	 */
	public function setDirectory($path)
	{
		if(substr($path , -1) == '/')
			$this->_directoryPath = $path;
		else
			$this->_directoryPath = $path . '/';

		return $this;
	}

	/**
	 * Set the extensions you want to allow for upload.
	 *
	 * @param Array
	 *
	 * @return Object | $this
	 */
	public function setAllowedExtensions(Array $extensions = array())
	{
		$this->_allowedExtensions = $extensions;

		return $this;
	}

	/**
	 * Set the Size of the files allowed to upload
	 *
	 * @param Integer | $maxSize
	 * 
	 * @return Object | $this;
	 */
	public function setMaxSize($maxSize)
	{
		$this->_maxSize = $maxSize;

		return $this;
	}

	/**
	 * start the upload process
	 */
	public function start()
	{
		if(empty($this->_fileInput))
			return;


		if(!file_exists($this->_directoryPath))
		{
			$this->_debugErrors[] = 'Sorry, but this path does not exists. you can also set create() to true.
									 Example: $this->setDirectory(\'images\')->create(true);';
			return;
		}
			
		foreach($this->_files as $key => &$file) 
		{
		    if($file['error'] !== UPLOAD_ERR_OK) 
		    {
		    	$this->_debugErrors[] = 'The file ' . $file['name'] . ' couldn\'t be uploaded. Please ensure 
		    							your php.ini file allow this size of files to be uploaded';
		    	$this->_files[$key]['message'] = 'Invalid File: ' . $file['name'];
		    	continue;
		    }
		    
	    	if($this->validationFails($file))
	    		continue;


	    	$fileToUpload = ($this->shouldBeEncrypted($file)) ? $this->_directoryPath . $file['encrypted_name'] : 
	    												 		$this->_directoryPath . $file['name'];

	    	if(!move_uploaded_file($file['tmp_name'], $fileToUpload))
				$file['success'] = false;
			else
				$file['success'] = true;
		}
	}

	protected function shouldBeEncrypted($file)
	{
		return $file['encryption'];
	}

	/**
	 * This method decrypt the file name based on the key you specfied.
	 *
	 * @param $encryptedName
	 * 
	 * @return String | Decrypted File Name 
	 */
	public function decryptFileName($encryptedName)
	{
		$encryptedName = str_replace('#', '/' , $base64EncodedString);
		return rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, static::KEY, base64_decode($encryptedCode), MCRYPT_MODE_ECB));
	}


	/**
	 * Save the file/files with the random name on the server(optional for security uses).
	 *
	 * @param Boolean | $generate
	 *
	 * @return Object | $this
	 */
	public function encryptFileNames($encrypt = false)
	{
		if($encrypt == false)
			return $this;

		if(empty(static::KEY))
		{
			$this->_debugErrors[] = 'Please go to Upload.class.php file and set manually a key inside the const KEY
								     of 32 characters to encrypt your files. keep this key in safe place as well. 
								     you can call $this->generateMeAKey() to generate a random 32 characters key';
			return;
		}	
		
		if(!empty($this->_fileInput))
		{
			foreach($this->_fileNames as $key => $fileName)
			{
				$base64EncodedString = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, static::KEY, $fileName, MCRYPT_MODE_ECB));
				$encryptedName = str_replace('/', '#' , $base64EncodedString);
				
				$extension = $this->_fileExtensions[$key];
				$this->_files[$key]['encrypted_name'] = $encryptedName . "." . $extension;
			}
		}

		return $this;
	}

	/**
	 * Allow the user to specify which file types to encrypt
	 *
	 * @param $types
	 *
	 * @return Object | $this
	 */
	public function only(Array $types)
	{
		$this->_fileTypesToEncrypt = $types;

		foreach($this->_files as $key => &$file)
		{
			if(in_array($this->_fileExtensions[$key], $this->_fileTypesToEncrypt))
				$file['encryption'] = true;
		}

		return $this;
	}

	/**
	 * This method checks if there are any errors.
	 *
	 * @return Array  
	 */
	public function hasErrors()
	{
		return !empty($this->_errors);
	}

	/**
	 * This method get the errors array to give some feedback to the user.
	 *
	 * @return Array  
	 */
	public function errors()
	{
		$failedUploads = array();

		foreach($this->_files as $key => $file)
		{

			if($file['success'] == true)
				continue;
			
			$failedFile = new stdClass();
			
			$failedFile->name = $file['name'];
			$failedFile->encryptedName = $file['encrypted_name'];
			$failedFile->type = $file['type'];
			$failedFile->extension = $file['extension'];
			$failedFile->size = $file['size'];
			$failedFile->error = $file['error'];
			$failedFile->message = $file['message'];

			$failedUploads[] = $failedFile;
		}
						
		return $failedUploads;
	}

	/**
	 * This method get the errors array to give some feedback to the developer.
	 *
	 * @return Array  
	 */
	public function errorsForDeveloper()
	{
		return $this->_debugErrors;
	}

	/**
	 * This get the name of an encrypted file name by its index.
	 *
	 * @param Integer | $index
	 *
	 * @return String | $this->_files['encrypted_name'][$index]
	 */
	public function getEncryptedFileName($index)
	{
		return $this->_files[$index]['encrypted_name'];
	}

	/**
	 * This get the names of the encrypted file names.
	 *
	 * @return Array | $this->_files['encrypted_name'] 
	 */
	public function getEncryptedFileNames()
	{
		$encryptedFileNames = array();
		
		foreach($this->_files as $file)
		{
			if($file['encryption'] == true)
				$encryptedFileNames[] = $file['encrypted_name'];
		}
		
		return $encryptedFileNames;
	}

	/**
	 * This method create the directory if needed
	 * 
	 * @param Boolean | $create
	 *
	 * @return Object | $this
	 */
	public function create($create = false)
	{
		if($create == false)
			return $this;

		if(!file_exists($this->_directoryPath))
			mkdir($this->_directoryPath);	
	
		return $this;
	}

	/**
	 * Check if extensions allowed
	 *
	 * @return Boolean
	 */
	protected function extensionsAllowed($file)
	{
		if(empty($this->_allowedExtensions) && empty($this->_fileExtensions))
			return;
		
		if(in_array($file['extension'], $this->_allowedExtensions))
			return true;
	
		$file['message'] = "Sorry, but only " . implode( ", " , $this->_allowedExtensions ) . " files are allowed.";
		return false;
	}

	/**
	 * 	Check if the file size allowed
	 *
	 *	@return Boolean
	 *
	 */
	protected function maxSizeOk(&$file)
	{
		if(empty($this->_maxSize) && empty($this->_fileSizes))
			return;
			
		if($file['size'] < ($this->_maxSize * 1000))
			return true;
		
		$file['message'] = "Sorry, but your file, " . $file['name'] . ", is too big. maximal size allowed " . $this->_maxSize . " Kbyte";
		
		return false;	
	}

	/**
	 * Check if file validation passes
	 *
	 * @return Boolean
	 */
	protected function validationPasses($file)
	{
		if($this->extensionsAllowed($file) && $this->maxSizeOk($file))
			return true;
	
		return false;
	}

	/**
	 * Check if file validation fails
	 *
	 * @return Boolean
	 */
	protected function validationFails(&$file)
	{
		if($this->extensionsAllowed($file) && $this->maxSizeOk($file))
			return false;
	
		return true;
	}

	/**
	 * A simple gererator of a random key to use for encrypting 
	 */
	public static function generateMeAKey()
	{
		echo md5(uniqid());
	}
}