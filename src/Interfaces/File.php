<?php

namespace Reich\Interfaces;

interface File
{
    /**
     * Retrieve the file name.
     * 
     * @return string
     */
    public function getName(): string;

    /**
     * Retrieve the encrypted name of the file.
     * 
     * @return string
     */
    public function getEncryptedName(): string;

    /**
     * Indicates if the files is encrypted.
     * 
     * @return bool
     */
    public function isEncrypted(): bool;

    /**
     * Retrieve the file type.
     * 
     * @return string
     */
    public function getType(): string;

    /**
     * Retrieve the file size.
     * 
     * @return string
     */
    public function getSize(): int;

    /**
     * Retrieve the file temp name.
     * 
     * @return string
     */
    public function getTmpName(): string;

    /**
     * Retrieve the file error code.
     * 
     * @return string
     */
    public function getError(): int;

    /**
     * Retrieve the file error message.
     * 
     * @return string
     */
    public function getErrorMessage(): string;

    /** 
     * Indicates if the file 
     * was uploaded successfully.
     * 
     * @return bool
     */
    public function success(): bool;
}
