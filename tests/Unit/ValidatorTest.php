<?php

namespace Tests\Unit;

use Reich\Types\Rule;
use Reich\Types\Extension;
use Reich\Types\MimeType;
use Reich\Classes\Input;
use Reich\Classes\Validator;

use Tests\TestCase;
use Tests\Unit\Helpers\FileGenerator;

class ValidatorTest extends TestCase
{
	protected function setUp()
	{
		$this->fileGenerator = new FileGenerator;
	}

	protected function tearDown()
	{
		unset($this->fileGenerator);
		unset($_FILES);
    }


	/** @test */
	public function it_checks_extensions_correctly()
	{
		$_FILES = $this->fileGenerator->single('file', [
			'name' => 'file.'. Extension::PDF
		]);

		$validator = new Validator(new Input('file'), [
			Rule::Extensions => [ Extension::PNG, Extension::JPG ]
		]);
		
		$this->assertFalse($validator->passes());
		$this->assertArrayHasKey(Rule::Extensions, $validator->errors());
	}

	/** @test */
	public function it_checks_the_size_rule_correctly()
	{
		$_FILES = $this->fileGenerator->multiple('file', [
			'size' => [ 3000, 6000 ]
		]);

		$validator = new Validator(new Input('file'), [
			Rule::Size => 4000
		]);

		$this->assertFalse($validator->passes());
		$this->assertArrayHasKey(Rule::Size, $validator->errors());
	}
}
