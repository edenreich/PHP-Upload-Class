<?php

namespace Tests;

use Reich\Upload;
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
		unset($_FILES);
	}

	/** @test */
	public function order_files_properly()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$results = $upload->sortFiles($_FILES['file']);

		$this->assertCount(1, $results);
		$this->assertCount(10, $results[0]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

		$_FILES = $this->fileGenerator->multiple('files');

		$upload = new Upload('files');

		$results = $upload->sortFiles($_FILES['files']);

		$this->assertCount(2, $results);
		$this->assertCount(10, $results[0]);
		$this->assertCount(10, $results[1]);
		$this->assertArrayHasKey('encryption', $results[0]);
		$this->assertArrayHasKey('success', $results[0]);
		$this->assertArrayHasKey('errorMessage', $results[0]);

	}

	/** @test */
	public function can_add_rules()
	{

		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$upload->addRules([
		        'size' => 2000,
		        'extensions' => 'png|jpg|pdf'
		]);

		$extensions = $upload->getAllowedExtensions();
		$maxSize = $upload->getMaxUploadingSize();

		$this->assertEquals($extensions, ['png', 'jpg', 'pdf']);
		$this->assertEquals($maxSize, 2000);

		$upload->addRules([
		        'size' => 2500,
		        'extensions' => ['png', 'jpg', 'pdf']
		]);

		$extensions = $upload->getAllowedExtensions();
		$maxSize = $upload->getMaxUploadingSize();

		$this->assertEquals($extensions, ['png', 'jpg', 'pdf']);
		$this->assertEquals($maxSize, 2500);
	}

	/** @test */
	public function a_directory_is_being_created()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$dirPath = __DIR__. '/tmp';

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

		$upload = new Upload('file');

		$upload->addRules([
		        'size' => 2000,
		        'extensions' => 'png|jpg|pdf',
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

		$upload = new Upload('file');

		$upload->setDirectory('/invalid/directory/path');

		$upload->start();
	}

	/**
	 * @test
	 * @expectedException \Reich\Exceptions\PermissionDeniedException
	 */
	public function if_a_server_not_allowing_the_creation_of_a_folder_an_exception_is_throwen()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$upload->setDirectory('/invalid/directory/path')->create(true);

		$upload->start();
	}

	/** @test */
	public function can_encrypt_a_file_name_and_decrypt_it_later()
	{
		$_FILES = $this->fileGenerator->single('file');

		$upload = new Upload('file');

		$fileName = $_FILES['file']['name'][0];

		$encrypted = $upload->encrypt($fileName);
		$decrypted = $upload->decrypt($encrypted);

		$this->assertEquals($fileName, $decrypted);
	}

	/** @test */
	public function error_method_is_being_called_every_time_an_file_has_a_failure()
	{
		static $callsCount = 0;
		$_FILES = $this->fileGenerator->multiple('files');

		$upload = new Upload('files');

		$upload->start();

		$upload->error(function($file) use (&$callsCount) {
			$callsCount++;
		});

		$this->assertEquals(2, $callsCount);
	}
}
