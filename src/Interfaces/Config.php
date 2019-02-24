<?php

namespace Reich\Interfaces;

interface Config
{
    /**
     * Retrieve a value from the configurations.
     * 
     * @param string  $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * Set a value to the configurations.
     * 
     * @param string  $key
     * @param mixed  $value
     * @return void
     */
    public function set(string $key, $value): void;

    /**
     * Get all configurations.
     * 
     * @return array
     */
    public function all(): array;
    
    /**
     * Load configurations by given file path.
     * 
     * @param string  $path
     * @return void 
     */
    public function loadFrom(string $path): void;
}
