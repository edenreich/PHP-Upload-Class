<?php

namespace Tests\Helpers;

class FileGenerator 
{
	public function path()
	{
		$index = mt_rand(0, 2);
		
		$paths = ['tests/images/example-1.jpg', 'tests/images/example-2.jpg', 'tests/images/example-3.png'];
	
		return $paths[$index];
	}

	public function name() 
	{
		$index = mt_rand(0, 2);
		
		$names = ['example-1.jpg', 'example-2.jpg', 'example-3.jpg'];
	
		return $names[$index];
	}

	public function mimeType() 
	{
		$index = mt_rand(0, 3);
		
		$mimeType = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'];

		return $mimeType[$index];
	}

	public function size()
	{
		return mt_rand(10, 5000);
	}

	public function tempName() 
	{
		$index = mt_rand(0, 5);
		
		$tmp_names = [
			__DIR__ . '/tmp/php/php1h4j1o',
			__DIR__ . '/tmp/php/php121393',
			__DIR__ . '/tmp/php/php1dggw3',
			__DIR__ . '/tmp/php/phpokegewf',
			__DIR__ . '/tmp/php/php1qwdpg1',
			__DIR__ . '/tmp/php/phphsaddf6',
		];

		return $tmp_names[$index];
	}

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
}
