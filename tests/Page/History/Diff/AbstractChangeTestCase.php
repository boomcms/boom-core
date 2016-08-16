<?php

namespace BoomCMS\Tests\Page\History\Diff;

use BoomCMS\Database\Models\PageVersion;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Lang;
use Mockery as m;

abstract class AbstractChangeTestCase extends AbstractTestCase
{
    protected $class;
    protected $className;
    protected $hasNewDescription = false;
    protected $hasOldDescription = false;

    public function setUp()
    {
        parent::setUp();

        $this->class = new $this->className(m::mock(PageVersion::class), m::mock(PageVersion::class));
    }

    public function testLangKeysExist()
    {
        $this->assertTrue(Lang::has($this->class->getSummaryKey()));

        if ($this->hasNewDescription) {
            $this->assertTrue(Lang::has($this->class->getNewDescriptionKey()));
        }

        if ($this->hasOldDescription) {
            $this->assertTrue(Lang::has($this->class->getOldDescriptionKey()));
        }
    }
}
