<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Smalot\PdfParser\Parser;

class Pdf extends DefaultDriver
{
    public function getCreatedAt(): Carbon
    {
        $metadata = $this->getMetadata();

        return isset($metadata['CreationDate']) ?
            Carbon::parse($metadata['CreationDate']) : null;
    }

    public function getTitle(): string
    {
        $metadata = $this->getMetadata();

        return $metadata['Title'] ?? parent::getTitle();
    }

    public function readMetadata(): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($this->file->getPathname());

        return $pdf->getDetails();
    }
}
