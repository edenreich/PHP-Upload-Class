<?php

namespace Tests\Unit\Helpers;

class FileGenerator 
{
	/**
	 * Generates a random file.
	 *
	 * @param string  $inputName
	 * @param array  $overrides
	 * @return array
	 */
	public function single(string $inputName, array $overrides = []): array
	{
		return [
    		$inputName => array_merge([
        		'name' => [ $this->name() ],
        		'type' => [ $this->mimeType() ],
        		'size' => [ $this->size() ],
        		'tmp_name' => [ $this->tempName() ],
        		'error' => [ 0 ]
			], $overrides)
    	];
	}

	/**
	 * Generates random files.
	 * 
	 * @param string  $inputName
	 * @param array  $overrides
	 * @return array
	 */
	public function multiple(string $inputName, array $overrides = []): array 
	{
		return [
    		$inputName => array_merge([
        		'name' => [ $this->name(), $this->name() ],
        		'type' => [ $this->mimeType(), $this->mimeType() ],
        		'size' => [ $this->size(), $this->size() ],
        		'tmp_name' => [ $this->tempName(), $this->tempName() ],
        		'error' => [ 0, 0 ]
			], $overrides)
    	];
	}

	/**
	 * Generates an invalid files.
	 *
	 * @param string  $inputName
	 * @return array
	 */
	public function invalidFiles(string $inputName): array
	{
		return [
    		$inputName => [
        		'name' => [$this->name(), $this->name()],
        		'type' => [$this->mimeType(), $this->mimeType()],
        		'size' => [$this->size(), $this->size()],
        		'tmp_name' => [$this->tempName(), $this->tempName()],
        		'error' => [1, 1]
    		]
    	];
	}

	/**
	 * Generates a random file path.
	 *
	 * @return string
	 */
	protected function path(): string
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
	protected function name(): string
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
	protected function mimeType(): string
	{
		$index = mt_rand(0, 3);
		
		$mimeType = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];

		return $mimeType[$index];
	}

	/**
	 * Generates a random file size.
	 *
	 * @return int
	 */
	protected function size(): string
	{
		return mt_rand(10, 1000);
	}

	/**
	 * Generates a random tempName.
	 *
	 * @return string
	 */
	protected function tempName(): string
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
