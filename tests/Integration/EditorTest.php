<?php

namespace BoomCMS\Tests\Integration;

use BoomCMS\Support\Facades\Editor as EditorFacade;
use BoomCMS\Tests\AbstractTestCase;

class EditorTest extends AbstractTestCase
{
    public function testLoginPageShouldRedirectAuthenticatedUser()
    {
        $this->withoutMiddleware();

        EditorFacade::disable();

        $response = $this->call('POST', route('editor.state'), [
            'state' => 'edit',
        ]);

        $this->assertResponseStatus(200, $response);

        $this->assertTrue(EditorFacade::isEnabled());
    }
}
