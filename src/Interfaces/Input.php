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
}
