<?php

namespace Reich\Classes;

use Reich\Interfaces\Config as ConfigInterface;

class Config implements ConfigInterface
{
    /**
     * Store the configuration file.
     * 
     * @var array
     */
    private $file;

    /**
     * Read the configuration file.
     */
    public function __construct()
    {
        $this->loadFrom(__DIR__.'/../config/upload.php');
    }

    /**
     * Retrieve a value from the configurations.
     * 
     * @param string  $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->file[$key] ?? null;
    }

    /**
     * Set a value to the configurations.
     * 
     * @param string  $key
     * @param mixed  $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->file[$key] = $value;
    }

    /**
     * Get all configurations.
     * 
     * @return array
     */
    public function all(): array
    {
        return $this->file;
    }

    /**
     * Load configurations by given file path.
     * 
     * @param string  $path
     * @return void 
     */
    public function loadFrom(string $path): void
    {
        $this->file = require ($path);
    }
}