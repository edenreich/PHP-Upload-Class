<?php

namespace Reich\Classes;

use Reich\Interfaces\File as FileInterface;

class File implements FileInterface
{
    /**
     * Stores the file name.
     * 
     * @var string
     */
    private $name;

    /**
     * Stores the file encrypted name.
     * 
     * @var string
     */
    private $encryptedName;

    /**
     * Indicates if the file is encrypted.
     * 
     * @var bool
     */
    private $isEncrypted;

    /**
     * Stores the file type.
     * 
     * @var string
     */
    private $type;

    /**
     * Stores the file size.
     * 
     * @var int
     */
    private $size;

    /**
     * Stores the file temp name.
     * 
     * @var string
     */
    private $tmpName;

    /**
     * Stores the file error code.
     * 
     * @var int
     */
    private $error;

    /**
     * Stores the error message.
     * 
     * @var string
     */
    private $errorMessage;

    /**
     * Indicates if the file 
     * was uploaded successfully.
     * 
     * @var bool
     */
    private $success;

    /**
     * Initialize the file properties.
     */
    public function __construct(array $properties)
    {
        $this->name = $properties['name'];
        $this->encryptedName = '';
        $this->isEncrypted = false;
        $this->type = $properties['type'];
        $this->size = $properties['size'];
        $this->tmpName = $properties['tmp_name'];
        $this->error = $properties['error'];
        $this->errorMessage = '';
        $this->success = false;
    }

    /**
     * Retrieve the file name.
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Retrieve the encrypted name of the file.
     * 
     * @return string
     */
    public function getEncryptedName(): string
    {
        return $this->encryptedName;
    }

    /**
     * Indicates if the files is encrypted.
     * 
     * @return bool
     */
    public function isEncrypted(): bool
    {
        return $this->isEncrypted;
    }

    /**
     * Retrieve the file type.
     * 
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Retrieve the file size.
     * 
     * @return string
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Retrieve the file temp name.
     * 
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    /**
     * Retrieve the file error code.
     * 
     * @return string
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * Retrieve the file error message.
     * 
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /** 
     * Indicates if the file 
     * was uploaded successfully.
     * 
     * @return bool
     */
    public function success(): bool
    {
        return $this->success;
    }
}
	