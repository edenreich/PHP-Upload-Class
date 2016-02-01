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
			$_originFileName;
	
	public	$isMultiple = false;
			

	/**
	 *	Set all the attributes with file data and check if it's single or multiple upload 
	 * 
	 */

	public function __construct()
	{
		if( isset($_FILES['file']) && $_FILES['file']['size'] > 0 )
		{
			$this->_fileData = $_FILES['file'];
			$this->_filePath = __DIR__ . DIRECTORY_SEPARATOR;

			

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

	public function getFileExtention()
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
		return $this->_filePath;
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
			if ( $this->isMultiple )
			{
				foreach ($_FILES['file']['error'] as $key => $error) 
				{
				    if ( $error == UPLOAD_ERR_OK ) 
				    {
				        move_uploaded_file( $this->_fileTempName[ $key ], $this->_filePath . $this->_fileName[ $key ] );
				    }
				    else
				    {
				    	$this->_errors[] = 'Invalid File: ' . $this->_originFileName[ $key ] . ".<br>"; 
				    }
				}
			}
			else
			{
				if( $_FILES['file']['error'][0] == UPLOAD_ERR_OK )
				{
					move_uploaded_file( $this->_fileTempName, $this->_filePath . $this->_fileName );
				}
				else
				{
					$this->_errors[] = "Invalid File.<br>";
				}
			}
		}
	}

	/**
	 *	Save the file/files with the random name on the server(optional for security uses)
	 *
	 */

	public function generateFileNames()
	{
		if( !empty( $this->_fileData ) )
		{
			if ( $this->isMultiple )
			{
				foreach($this->_fileName as $key => $fileName)
				{
					$randomName = uniqid();
					$extention = $this->_fileExtention[ $key ];
					$this->_originFileName[ $key ] = $this->_fileName[ $key ];
					$this->_fileName[ $key ] = $randomName . "." . $extention;
				}
			}
			else
			{
				$randomName = uniqid();
				$extention = $this->_fileExtention;
				$this->_originFileName = $this->_fileName;
				$this->_fileName = $randomName . "." . $extention;
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
}