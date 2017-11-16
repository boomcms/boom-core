<?php

namespace BoomCMS\Tests\FileInfo;

use BoomCMS\FileInfo\Facade as FileInfo;
use BoomCMS\Tests\AbstractTestCase;
use Illuminate\Support\Facades\Storage;

abstract class BaseDriverTest extends AbstractTestCase
{
    protected function getInfo($filename)
    {
        $filesystem = Storage::disk('local');

        return FileInfo::create($filesystem, $filename);
    }
}
