<?php

use BoomCMS\Support\Helpers;
use BoomCMS\Support\Facades\Settings;
use Illuminate\Support\Facades\App;

class Support_HelpersTest extends TestCase
{
    public function testAnalyticsReturnsAnalyticsSettingInProduction()
    {
        App::shouldReceive('environment')->andReturn('production');
        Settings::shouldReceive('get')->with('analytics')->andReturn('test');

        $this->assertEquals('test', Helpers::analytics());
    }

    public function testAnalyticsReturnsNullIfNotInProduction()
    {
        App::shouldReceive('environment')->andReturn('local');
        Settings::shouldReceive('get')->with('analytics')->andReturn('test');

        $this->assertEquals('', Helpers::analytics());
    }
}