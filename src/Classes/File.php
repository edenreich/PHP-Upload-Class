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
     * Initialize the file properties.
     */
    public function __construct(array $properties)
    {
        $this->name = $properties['name'];
        $this->type = $properties['type'];
        $this->size = $properties['size'];
        $this->tmpName = $properties['tmp_name'];
        $this->error = $properties['error'];
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
}
