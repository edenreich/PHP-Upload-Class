<?php

namespace Tests;

use Source\Upload;
use Tests\Helpers\FileGenerator;

class UploadClassTest extends \PHPUnit_Framework_TestCase
{
	public function setUp() 
	{
		$this->fileGenerator = new FileGenerator;
	}

	public function tearDown()
	{
		unset($this->fileGenerator);
	}

	public function testOrderFiles()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$results = $upload->orderFiles($_FILES['file']);

		$this->assertCount(1, $results);
		$this->assertCount(10, $results[0]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

		$_FILES = $this->fileGenerator->multiple('files');

		$upload = new Upload('files');

		$results = $upload->orderFiles($_FILES['files']);

		$this->assertCount(2, $results);
		$this->assertCount(10, $results[0]);
		$this->assertCount(10, $results[1]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

	}

	public function testAddRules() 
	{
		
	}
}