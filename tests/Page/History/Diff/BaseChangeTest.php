<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Page\History\Diff\BaseChange;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

class BaseChangeTest extends AbstractTestCase
{
    /**
     * @var BaseChange
     */
    protected $class;

    public function setUp()
    {
        parent::setUp();

        $this->class = m::mock(BaseChange::class)->makePartial();
    }

    public function testGetDescriptionKey()
    {
        $className = strtolower(str_replace('Change', '', class_basename($this->class)));

        $this->assertEquals('boomcms::page.history.diff.'.$className, $this->class->getDescriptionKey());
    }

    public function testGetDescriptionParams()
    {
        $this->assertEquals([], $this->class->getDescriptionParams());
    }

    public function testGetDescription()
    {
        $description = 'test';

        Lang::shouldReceive('get')
            ->once()
            ->with($this->class->getDescriptionKey(), [])
            ->andReturn($description);

        $this->assertEquals($description, $this->class->getDescription());
    }
}
