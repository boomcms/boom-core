<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Page\Relations as Controller;
use Mockery as m;

class RelationsTest extends BaseControllerTest
{
    protected $className = Controller::class;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var Page
     */
    protected $related;

    public function setUp()
    {
        parent::setUp();

        $this->page = m::mock(Page::class)->makePartial();
        $this->related = new Page();
    }

    public function testDestroy()
    {
        $this->page
            ->shouldReceive('removeRelation')
            ->once()
            ->with($this->related);

        $this->controller->destroy($this->page, $this->related);
    }

    public function testStore()
    {
        $this->page
            ->shouldReceive('addRelation')
            ->once()
            ->with($this->related);

        $this->controller->store($this->page, $this->related);
    }
}
