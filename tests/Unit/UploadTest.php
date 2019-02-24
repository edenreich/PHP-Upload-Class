<?php

namespace Tests\Unit;

use Reich\Upload;

use Tests\TestCase;
use Tests\Unit\Helpers\FileGenerator;

// Types
use Reich\Types\Rule;

// Interfaces
use Reich\Interfaces\File;

class UploadTest extends TestCase
{
	public function setUp()
	{
		$this->fileGenerator = new FileGenerator;
	}

	public function tearDown()
	{
		unset($this->fileGenerator);
		unset($_FILES);
	}

	/** @test */
	public function a_directory_is_being_created()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = Upload::file('file');

		$upload->setDirectory('images')->create(true);

		$dirPath = __DIR__ . '/tmp';

		$upload->setDirectory($dirPath)->create(true);

		$this->assertTrue(file_exists($dirPath));

		rmdir($dirPath);
	}

	/**
	 * @test
	 * @expectedException \Reich\Exceptions\InvalidRuleException
	 */
	public function an_exception_is_throwen_when_a_rule_that_does_not_exist_is_applied()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = Upload::file('file');

		$upload->validator()->setRules([
		        Rule::Size => 2000,
		        Rule::Extensions => 'png|jpg|pdf',
		        'notexist' => 'somevalue',
		]);
	}

	/**
	 * @test
	 * @expectedException \Reich\Exceptions\FolderNotExistException
	 */
	public function if_a_folder_is_not_present_an_exception_is_throwen()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = Upload::file('file');

		$upload->setDirectory('invalid/directory/path');

		$upload->start();
	}

	/** @test */
	public function can_encrypt_a_file_name_and_decrypt_it_later()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = Upload::file('file');

		$fileName = $_FILES['file']['name'][0];

		$encrypted = $upload->encrypt($fileName);
		$decrypted = $upload->decrypt($encrypted);

		$this->assertEquals($fileName, $decrypted);
	}

	/** @test */
	public function error_method_is_being_called_every_time_a_file_has_a_failure()
	{
		static $callsCount = 0;
		
		$_FILES = $this->fileGenerator->invalidFiles('files');

		$upload = Upload::file('files');

		$dirPath = __DIR__ . '/tmp';

		$upload->setDirectory($dirPath)->create(true);

		$upload->onError(function(File $file) use (&$callsCount) {
			$callsCount++;
		});

		$upload->start();

		$this->assertEquals(2, $callsCount);

		rmdir($dirPath);
	}
}
