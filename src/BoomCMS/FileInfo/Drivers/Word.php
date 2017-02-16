<?php

namespace BoomCMS\FileInfo\Drivers;

use PhpOffice\PhpWord\IOFactory;

class Word extends DefaultDriver
{
    protected function readMetadata(): array
    {
        $phpWord = IOFactory::load($this->file->getPathname());

        dd($phpWord->getProperties());
    }
}
