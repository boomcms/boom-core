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

    public function readMetadata(): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($this->getPathname());

        return $pdf->getDetails();
    }
}
