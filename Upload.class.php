<?php


/*
|--------------------------------------------------------------------------
| Upload Class 
|--------------------------------------------------------------------------
| This class handle uploading of files
| 
|
*/
class Upload
{
	protected $_fileInput = array();
	
	protected $_fileNames = array();
	
	protected $_fileTempNames = array();
			
	protected $_fileExtensions = array();
			
	protected $_fileSizes = array();

	protected $_directoryPath = '';

	protected $_errors = array();

	protected $_encryptedFileNames = array();

	protected $_allowedExtensions = array();

	protected $_maxSize = null;
	
	protected $isMultiple = false;
			
	const KEY = 'fc01e8d00a90c1d392ec45459deb6f12'; // Please set your key for encryption here

	/**
	 * Setting all the attributes with file data and check if it's single or multiple upload 
	 */
	public function __construct()
	{
		$this->_fileInput = $_FILES['file'];

		$this->isMultiple = $this->isMultiple($this->_fileInput);
		
		$this->_fileNames = $this->_fileInput['name'];
		$this->_fileTempNames = $this->_fileInput['tmp_name'];
		$this->_fileSizes = $this->_fileInput['size'];

		$this->setDirectory('/images');
		$this->_fileExtensions = $this->getFileExtensions();
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
			echo "Sorry, but this directory does not exist yet. You should create it first or use createFoldersIfNotExists() before that command.<br>";
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
	public function setAllowedExtentions(Array $extensions = array())
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
			echo 'Sorry, but this path does not exists. you can also set $this->createDirectory(true)' . "<br>";
			return;
		}
			
		foreach($this->_fileInput['error'] as $key => $error) 
		{
		    if($error == UPLOAD_ERR_OK) 
		    {
		    	if($this->validatePasses())
		    	{
			        if (!empty($this->_encryptedFileNames))
					{
			       		move_uploaded_file($this->_fileTempNames[$key], $this->_directoryPath . $this->_encryptedFileNames[$key]);
			    	}
			    	else
			    	{
			    		move_uploaded_file($this->_fileTempNames[$key], $this->_directoryPath . $this->_fileNames[$key]);
			    	}
		    	}
		    }
		    else
		    {
		    	$this->_errors[] = "Invalid File: " . $this->_fileName[ $key ] . ".<br>"; 
		    }
		}	
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
			echo 'Please go to Upload.class.php file and set manually a key inside the const KEY of 32 characters to encrypt your files. keep this key in safe place as well. you can call $this->generateMeAKey() to generate a random 32 characters key';
			return;
		}	
		
		if(!empty($this->_fileInput))
		{
			foreach($this->_fileNames as $key => $fileName)
			{
				$encryptedName = rtrim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, static::KEY, $fileName, MCRYPT_MODE_ECB)));
				$extention = $this->_fileExtensions[ $key ];
				$this->_encryptedFileNames[ $key ] = $encryptedName . "." . $extention;
			}
		}

		return $this;
	}

	/**
	 * Get the Errors array
	 *
	 * @return Array  
	 */
	public function uploadErrors()
	{
		return $this->_errors;
	}

	/**
	 * Get the generated name/names of the file/files
	 *
	 * @return String or Array
	 */
	public function getGeneratedFileName()
	{
		return $this->_encryptedFileNames;
	}

	/**
	 * This method create the directory if needed
	 * 
	 * @param Boolean | $create
	 *
	 * @return Object | $this
	 */
	public function createDirectory($create = false)
	{
		if($create == false)
			return $this;

		if(!file_exists($this->_directoryPath))
			mkdir($this->_directoryPath);	
	
		return $this;
	}

	/**
	 * Check if extentions allowed
	 *
	 * @return Boolean
	 */
	protected function extentionsAllowed()
	{
		if(empty($this->_allowedExtensions) && empty($this->_fileExtensions))
			return;

		foreach( $this->_fileExtensions as $extention )
		{
			if(in_array($extention, $this->_allowedExtensions))
			{
				return true;
			}
			else
			{
				$this->_errors[] = "Sorry, but only " . implode( ", " , $this->_allowedExtensions ) . " files are allowed.";
				return false;
			}
		}
	
		return true;
	}

	/**
	 * 	Check if the file size allowed
	 *
	 *	@return Boolean
	 *
	 */
	protected function maxSizeOk()
	{
		if( !empty($this->_maxSize) && !empty($this->_fileSize) )
		{
			if( $this->isMultiple === true )
			{
				foreach( $this->_fileSize as $key => $fileSize)
				{
					if( $fileSize < $this->_maxSize )
					{
						return true;
					}
					else
					{
						$this->_errors[] = "Sorry, but your file, " . $this->_fileName[ $key ] . ", is too big. maximal size allowed " . $this->_maxSize . " Kbyte";
						return false;
					}

				}
			}
			else
			{
				if( $this->_fileSize < $this->_maxSize )
				{
					return true;
				}
				else
				{
					$this->_errors[] = "Sorry, but your file is too big. maximal size allowed " . $this->_maxSize . " Kbyte";
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Check if file validation passes
	 *
	 * @return Boolean
	 */
	protected function validatePasses()
	{
		if($this->extentionsAllowed() && $this->maxSizeOk())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * A simple gererator of a random key to use for encrypting 
	 */
	public function generateMeAKey()
	{
		echo md5(uniqid());
	}
}