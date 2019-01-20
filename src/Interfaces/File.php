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
}
