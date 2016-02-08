<?php

namespace BoomCMS\Tests\Http\Controllers;

use BoomCMS\Database\Models\Page;
use BoomCMS\Http\Controllers\Page\PageController as Controller;
use BoomCMS\Support\Facades\PageVersion as PageVersionFacade;

class PageControllerTest extends BaseControllerTest
{
    /**
     * @var string
     */
    protected $className = Controller::class;

    public function testPostDiscard()
    {
        $page = new Page();

        $this->requireRole('edit', $page);

        PageVersionFacade::shouldReceive('deleteDrafts')
            ->once()
            ->with($page);

        $this->controller->postDiscard($page);
    }
}
