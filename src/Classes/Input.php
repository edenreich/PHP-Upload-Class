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
    private $name;

    /**
     * Stores the input.
     * 
     * @var array
     */
    private $input;

    /**
     * Initialize the file name.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->input = $_FILES[$name] ?? null;
    }

    /**
     * Indicate if it multiple files.
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

    /**
     * Check if the input is empty.
     * 
     * @return bool
     */
    public function isEmpty(): bool
    {
        if ($this->input === null) {
			return true;
		}
        
        return false;
    }
}