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

        $this->assertEquals('boomcms::page.diff.'.$className.'.summary', $this->class->getSummaryKey());
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

    public function testGetNewDescriptionKey()
    {
        $className = strtolower(str_replace('Change', '', class_basename($this->class)));

        $this->assertEquals('boomcms::page.diff.'.$className.'.new', $this->class->getNewDescriptionKey());
    }

    public function testGetNewDescription()
    {
        $description = 'test';

        Lang::shouldReceive('get')
            ->once()
            ->with($this->class->getNewDescriptionKey(), [])
            ->andReturn($description);

        $this->assertEquals($description, $this->class->getNewDescription());
    }

    public function testGetNewDescriptionParams()
    {
        $this->assertEquals([], $this->class->getNewDescriptionParams());
    }

    public function testGetOldDescriptionKey()
    {
        $className = strtolower(str_replace('Change', '', class_basename($this->class)));

        $this->assertEquals('boomcms::page.diff.'.$className.'.old', $this->class->getOldDescriptionKey());
    }

    public function testGetOldDescription()
    {
        $description = 'test';

        Lang::shouldReceive('get')
            ->once()
            ->with($this->class->getOldDescriptionKey(), [])
            ->andReturn($description);

        $this->assertEquals($description, $this->class->getOldDescription());
    }

    public function testGetOldDescriptionParams()
    {
        $this->assertEquals([], $this->class->getOldDescriptionParams());
    }
}
