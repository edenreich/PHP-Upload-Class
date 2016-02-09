<?php

/**
 *	Copyright Eden Reich ©. all rights reserved.
 * 
 */

class Upload
{
	private $_fileData = array(),
			$_fileName,
			$_fileTempName,
			$_fileExtention,
			$_fileSize,
			$_filePath,
			$_errors = array(),
			$_changedFileName,
			$_allowedExtentions = array(),
			$_maxSize;
	
	public	$isMultiple = false;
			
			

	/**
	 *	Set all the attributes with file data and check if it's single or multiple upload 
	 * 
	 */

	public function __construct()
	{
		if( isset($_FILES['file']) && $_FILES['file']['size'] > 0 )
		{
			if( count( $_FILES['file']['name'] ) > 1 )
			{
				$this->_fileName = $_FILES['file']['name'];
				$this->_fileTempName = $_FILES['file']['tmp_name'];
				$this->_fileSize = $_FILES['file']['size'];
				$this->isMultiple = true;
			}
			else
			{
				$this->_fileName = $_FILES['file']['name'][0];
				$this->_fileTempName = $_FILES['file']['tmp_name'][0];
				$this->_fileSize = $_FILES['file']['size'][0];
				$this->isMultiple = false;
			}

			$this->_fileData = $_FILES['file'];
			$this->_filePath = __DIR__ . DIRECTORY_SEPARATOR;
			$this->_fileExtention = $this->getFileExtention();
		}
	}

	/**
	 *	Get the file data array
	 *
	 *	@return Array 
	 *
	 */

	public function getFileData()
	{
		return $this->_fileData;
	}

	/**
	 *	Get the name/names of the file/files
	 *
	 *	@return String or Array
	 *
	 */

	public function getFileName()
	{
		return $this->_fileName;
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
	 *	Get the extention/extentions of the file/files
	 *
	 *	@return String or Array
	 *
	 */

	protected function getFileExtention()
	{
		if( $this->isMultiple === true )
		{
			foreach ( $this->_fileName as $filename )
			{
				$str = end( explode( '.', $filename ) );
				$extn = strtolower( $str );
				$this->_fileExtention[] = $extn;
			}
			return $this->_fileExtention;
		}
		else
		{
			$str = end( explode( '.', $this->_fileName ) );
			$this->_fileExtention = strtolower( $str );
			return $this->_fileExtention;
		}
	}

	/**
	 *	Get the size/sizes of the file/files
	 *
	 *	@return Integer or Array
	 *
	 */

	public function getFileSize()
	{
		return $this->_fileSize;
	}

	/**
	 *	Get the path/paths of the file/files
	 *
	 *	@return String or Array
	 *
	 */

	public function getFilePath()
	{
		if( file_exists( $this->_filePath ) )
		{
			return $this->_filePath;
		}
		else
		{
			echo "Sorry, but this path does not exists. You should create it first or use createFoldersIfNotExists() before that command.<br>";
		}
	}

	/**
	 *	Set the path directory where you want to upload the files(if not specfied file/files will be uploaded to the current directory)
	 *
	 *	@param String
	 *
	 */

	public function setFilePath( $path )
	{
		if( substr( $path , -1 ) == '/' )
		{
			$this->_filePath = $path;
		}
		else
		{
			$this->_filePath = $path . '/';
		}
	}

	/**
	 *	Save the file/files with the original name on the server
	 */

	public function save()
	{
		if( !empty( $this->_fileData ) )
		{
			if( file_exists( $this->_filePath ) )
			{
				if ( $this->isMultiple )
				{
					foreach ( $_FILES['file']['error'] as $key => $error ) 
					{
					    if ( $error == UPLOAD_ERR_OK ) 
					    {
					    	if( $this->validatePasses() )
					    	{
						        if ( !empty($this->_changedFileName) )
								{
						       		move_uploaded_file( $this->_fileTempName[ $key ], $this->_filePath . $this->_changedFileName[ $key ] );
						    	}
						    	else
						    	{
						    		move_uploaded_file( $this->_fileTempName[ $key ], $this->_filePath . $this->_fileName[ $key ] );
						    	}
					    	}
					    }
					    else
					    {
					    	$this->_errors[] = "Invalid File: " . $this->_fileName[ $key ] . ".<br>"; 
					    }
					}
				}
				else
				{
					if( $_FILES['file']['error'][0] == UPLOAD_ERR_OK )
					{
						if( $this->validatePasses() )
						{
							if ( !empty($this->_changedFileName) )
							{
								move_uploaded_file( $this->_fileTempName, $this->_filePath . $this->_changedFileName );
							}
							else
							{
								move_uploaded_file( $this->_fileTempName, $this->_filePath . $this->_fileName );
							}
						}
					}
					else
					{
						$this->_errors[] = "Invalid File.<br>";
					}
				}
			}
			else
			{
				echo "Sorry, but this path does not exists. You should create it first or use createFoldersIfNotExists() before that command.<br>";
			}
		}
	}

	/**
	 *	Save the file/files with the random name on the server(optional for security uses)
	 *
	 */

	public function generateFileName()
	{
		if( !empty( $this->_fileData ) )
		{
			if ( $this->isMultiple )
			{
				foreach($this->_fileName as $key => $fileName)
				{
					$randomName = uniqid();
					$extention = $this->_fileExtention[ $key ];
					$this->_changedFileName[ $key ] = $randomName . "." . $extention;
				}
			}
			else
			{
				$randomName = uniqid();
				$extention = $this->_fileExtention;
				$this->_changedFileName = $randomName . "." . $extention;
			}
		}
	}

	/**
	 *	Get the Errors array
	 *
	 *	@return Array  
	 *
	 */

	public function uploadErrors()
	{
		return $this->_errors;
	}

	/**
	 *	Get the generated name/names of the file/files
	 *
	 *	@return String or Array
	 *
	 */

	public function getGeneratedFileName()
	{
		return $this->_changedFileName;
	}

	/**
	 *	Creates the directories of the paths if they are not exists
	 *
	 */

	public function createFoldersIfNotExists()
	{
		if( !file_exists( $this->_filePath ) )
		{
			mkdir( $this->_filePath );
		}
	}

	/**
	 *	Set the extentions you want to allow for upload.
	 *
	 *	@param Array
	 *
	 */

	public function setAllowedExtentions( $extentions = array() )
	{
		$this->_allowedExtentions = $extentions;
	}

	/**
	 * 	Check if extentions allowed
	 *
	 *	@return Boolean
	 *
	 */

	protected function extentionsAllowed()
	{
		if( !empty( $this->_allowedExtentions ) && !empty( $this->_fileExtention ) )
		{
			if( $this->isMultiple === true )
			{
				foreach( $this->_fileExtention as $extention )
				{
					if( in_array( $extention, $this->_allowedExtentions ) )
					{
						return true;
					}
					else
					{
						$this->_errors[] = "Sorry, but only " . implode( ", " , $this->_allowedExtentions ) . " files are allowed.";
						return false;
					}
				}
			}
			else
			{
				if( in_array( $this->_fileExtention, $this->_allowedExtentions ) )
				{
					return true;
				}
				else
				{
					$this->_errors[] = "Sorry, but only " . implode( ", " , $this->_allowedExtentions ) . " files are allowed.";
					return false;
				}
			}
		}

		return true;
	}


	public function setMaxSize( $maxSize )
	{
		$this->_maxSize = $maxSize;
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
	 * 	Check if file validation passes
	 *
	 *	@return Boolean
	 *
	 */

	protected function validatePasses()
	{
		if( $this->extentionsAllowed() && $this->maxSizeOk() )
		{
			return true;
		}
		else
		{
			return false;
		}
	}		
}