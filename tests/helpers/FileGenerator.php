<?php

namespace Tests\Helpers;

class FileGenerator 
{
	/**
	 * Generates a random file.
	 *
	 * @return array
	 */
	public function single($inputName) 
	{
		return [
    		$inputName => [
        		'name' => [$this->name()],
        		'type' => [$this->mimeType()],
        		'size' => [$this->size()],
        		'tmp_name' => [$this->tempName()],
        		'error' => [0]
    		]
    	];
	}

	/**
	 * Generates random files.
	 *
	 * @return array
	 */
	public function multiple($inputName) 
	{
		return [
    		$inputName => [
        		'name' => [$this->name(), $this->name()],
        		'type' => [$this->mimeType(), $this->mimeType()],
        		'size' => [$this->size(), $this->size()],
        		'tmp_name' => [$this->tempName(), $this->tempName()],
        		'error' => [0,0]
    		]
    	];
	}

	/**
	 * Generates an invalid file.
	 *
	 * @return array
	 */
	public function invalidFile($inputName)
	{
		return [
    		$inputName => [
        		'name' => [],
        		'type' => [$this->mimeType(), $this->mimeType()],
        		'size' => [$this->size(), $this->size()],
        		'tmp_name' => [$this->tempName(), $this->tempName()],
        		'error' => [0,0]
    		]
    	];
	}

	/**
	 * Generates a random file path.
	 *
	 * @return string
	 */
	protected function path()
	{
		$index = mt_rand(0, 2);
		
		$paths = ['tests/images/example-1.jpg', 'tests/images/example-2.jpg', 'tests/images/example-3.png'];
	
		return $paths[$index];
	}

	/**
	 * Generates a random filenmae.
	 *
	 * @return string
	 */
	protected function name() 
	{
		$index = mt_rand(0, 2);
		
		$names = ['example-1.jpg', 'example-2.jpg', 'example-3.jpg'];
	
		return $names[$index];
	}

	/**
	 * Generates a random mimeType.
	 *
	 * @return string
	 */
	protected function mimeType() 
	{
		$index = mt_rand(0, 3);
		
		$mimeType = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];

		return $mimeType[$index];
	}

	/**
	 * Generates a random file size.
	 *
	 * @return integer
	 */
	protected function size()
	{
		return mt_rand(10, 1000);
	}

	/**
	 * Generates a random tempName.
	 *
	 * @return string
	 */
	protected function tempName() 
	{
		$index = mt_rand(0, 5);

		$tmpDir = sys_get_temp_dir();
		tempnam(sys_get_temp_dir(), 'Tux');
		$tmpNames = [
			$tmpDir . '/php/php1h4j1o',
			$tmpDir . '/php/php121393',
			$tmpDir . '/php/php1dggw3',
			$tmpDir . '/php/phpokegewf',
			$tmpDir . '/php/php1qwdpg1',
			$tmpDir . '/php/phphsaddf6',
		];

		return $tmpNames[$index];
	}
}
