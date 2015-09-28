<?php

use BoomCMS\UI\Button;
use Illuminate\Support\Facades\Lang;

class Boom_UI_ButtonTest extends TestCase
{
    public function testTranslateReturnsOriginal()
    {
        $button = $this->getButton();

        Lang::shouldReceive('has')
            ->with('boom::buttons.test')
            ->andReturn(false);

        $this->assertEquals('test', $button->translate('test'));
    }

    public function testTranslateReturnsTranslation()
    {
        $button = $this->getButton();
        $translation = 'Test button text';

        Lang::shouldReceive('has')
            ->with('boom::buttons.test')
            ->andReturn(true);

        Lang::shouldReceive('get')
            ->with('boom::buttons.test')
            ->andReturn($translation);

        $this->assertEquals($translation, $button->translate('test'));
    }

    protected function getButton()
    {
        return $this->getMockBuilder(Button::class)
            ->disableOriginalConstructor()
            ->setMethods(['render'])
            ->getMock();
    }
}
