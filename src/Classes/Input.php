<?php

namespace Reich\Classes;

use Countable;
use Reich\Interfaces\Input as InputInterface;

class Input implements InputInterface, Countable
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
     * Stores the files.
     * 
     * @var array
     */
    private $files;

    /**
     * Stores the count of the files.
     * 
     * @var int
     */
    private $count;

    /**
     * Initialize the file name.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->files = [];

        $this->input = $_FILES[$name] ?? [];

        if (! empty($this->input['name']) && is_string($this->input['name'])) {
            $this->input['name'] = [$this->input['name']];
        }

        $this->count = empty($this->input['name']) ? 0 : count($this->input['name']);

        for ($i = 0; $i < $this->count; ++$i) {
            $this->files[] = new File([
                'name' => $this->input['name'][$i] ?? $this->input['name'],
        		'type' => $this->input['type'][$i],
        		'size' => $this->input['size'][$i],
        		'tmp_name' => $this->input['tmp_name'][$i],
        		'error' => $this->input['error'][$i]
            ]);
        }
    }

    /**
     * Indicate if it multiple files.
     * 
     * @return bool
     */
    public function isMultiple(): bool
    {
        return count($this->files) > 1;
    }

    /**
     * Check if the input is empty.
     * 
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->input === null ? true : false;
    }

    /**
     * Retrieve the files.
     * 
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Retrieve the count of the inputs.
     * 
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }
}
