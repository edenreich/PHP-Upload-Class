<?php

namespace Tests\Unit;

use Reich\Classes\Input;
use Tests\Unit\Helpers\FileGenerator;

class InputTest extends \PHPUnit_Framework_TestCase
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
	public function it_indicates_if_its_single_or_multiple_files()
	{
		$_FILES = $this->fileGenerator->single('file');

		$input = new Input('file');

		$this->assertFalse($input->isMultiple());

		$_FILES = $this->fileGenerator->multiple('file');

		$input = new Input('file');

		$this->assertTrue($input->isMultiple());
	}
}
