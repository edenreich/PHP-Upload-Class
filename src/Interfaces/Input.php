<?php

namespace Reich\Interfaces;

interface Input
{
    /**
     * Indicates if it multiple files.
     * 
     * @return bool
     */
    public function isMultiple(): bool;

    /**
     * Check if the input is empty.
     * 
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * Retrieve the files.
     * 
     * @return array
     */
    public function getFiles(): array;
}
