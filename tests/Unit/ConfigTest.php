<?php

namespace Tests\Unit;

use Tests\TestCase;

use Reich\Classes\Config;

class ConfigTest extends TestCase
{ 
    /** @test */
	public function it_load_the_configuration_file_as_an_array()
	{
        $config = new Config;

        $configurations = $config->all();

        $this->isTrue(is_array($configurations));
    }

    /** @test */
    public function it_retrieve_a_key_from_the_configuration_file()
    {
        $config = new Config;

        $disks = $config->get('disks');

        $this->isTrue(is_array($configurations));
        $this->assertArrayHasKey('default', $disks);
    }

    /** @test */
    public function it_set_a_configuration_on_runtime()
    {
        $config = new Config;
        
        $config->set('foo', 'bar');

        $configurations = $config->all();
        $foo = $config->get('foo');

        $this->assertArrayHasKey('foo', $configurations);
        $this->assertEquals($foo, 'bar');
    }
}
