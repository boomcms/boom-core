<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Tests\AbstractTestCase;
use Symfony\Component\HttpFoundation\File\File;

abstract class BaseDriverTest extends AbstractTestCase
{
    protected function getInfo($filename)
    {
        $path = realpath(__DIR__."/../files/$filename");
        $file = new File($path);
        $info = FileInfo::create($file);

        return $info;
    }
}
