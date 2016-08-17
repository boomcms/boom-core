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

    public function testGetSummaryKey()
    {
        $className = strtolower(str_replace('Change', '', class_basename($this->class)));

        $this->assertEquals('boomcms::page.diff.'.$className, $this->class->getSummaryKey());
    }

    public function testGetSummary()
    {
        $description = 'test';

        Lang::shouldReceive('get')
            ->once()
            ->with($this->class->getSummaryKey(), [])
            ->andReturn($description);

        $this->assertEquals($description, $this->class->getSummary());
    }

    public function testGetSummaryParams()
    {
        $this->assertEquals([], $this->class->getSummaryParams());
    }
}
