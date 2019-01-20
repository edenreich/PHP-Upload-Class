<?php

namespace Tests\Unit;

use Reich\Classes\Upload;
use Reich\Upload as UploadFactory;

use Tests\TestCase;
use Tests\Unit\Helpers\FileGenerator;

class UploadFactoryTest extends TestCase
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
    public function it_creates_an_instance_of_upload_class()
    {
        $upload = UploadFactory::picture('file');

        $this->assertInstanceOf(Upload::class, $upload);
    }
}
