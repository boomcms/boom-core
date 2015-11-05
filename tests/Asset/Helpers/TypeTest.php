<?php

namespace BoomCMS\Tests\Asset\Helpers;

use BoomCMS\Core\Asset\Helpers\Type;
use BoomCMS\Tests\AbstractTestCase;

class TypeTest extends AbstractTestCase
{
    public function testControllerFromClassname()
    {
        $namespace = 'BoomCMS\Http\Controllers\Asset\\';

        $expected = [
            'Image'   => $namespace.'Image',
            'MSExcel' => $namespace.'BaseController',
            'MSWord'  => $namespace.'BaseController',
            'PDF'     => $namespace.'PDF',
            'Text'    => $namespace.'BaseController',
            'Tiff'    => $namespace.'Tiff',
            'Video'   => $namespace.'Video',
        ];

        foreach ($expected as $type => $controller) {
            $this->assertEquals($controller, Type::controllerFromClassname($type));
        }
    }
}
