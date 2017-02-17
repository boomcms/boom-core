<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Exception;
use Smalot\PdfParser\Parser;

class Pdf extends DefaultDriver
{
    /**
     * {@inheritdoc}
     *
     * @return null|Carbon
     */
    public function getCreatedAt()
    {
        $metadata = $this->getMetadata();

        return isset($metadata['CreationDate']) ?
            Carbon::parse($metadata['CreationDate']) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle(): string
    {
        $metadata = $this->getMetadata();

        return $metadata['Title'] ?? parent::getTitle();
    }

    /**
     * Extracts metadata from a PDF.
     *
     * @return array
     */
    public function readMetadata(): array
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($this->file->getPathname());

            return $pdf->getDetails();
        } catch (Exception $e) {
            return [];
        }
    }
}
