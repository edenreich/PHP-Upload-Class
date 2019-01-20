<?php

namespace Reich\Classes;

use Reich\Interfaces\Input as InputInterface;

class Input implements InputInterface
{
    /**
     * Stores the input name.
     * 
     * @var string
     */
    private $input;

    /**
     * Initialize the file name.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Indicates if it multiple files.
     * 
     * @return bool
     */
    public function isMultiple(): bool
    {
        if (count($_FILES[$this->name]['name']) > 1) {
			return true;
		}

		return false;
    }
}