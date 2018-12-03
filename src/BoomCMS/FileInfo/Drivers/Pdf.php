<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Exception;
use Imagick;
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

        try {
            if (isset($metadata['CreationDate'])) {
                $timestamp = is_array($metadata['CreationDate']) ? $metadata['CreationDate'][0] ?? null : $metadata['CreationDate'];
            }

            return isset($timestamp) ? Carbon::parse($timestamp) : null;
        } catch (Exception $e) {
            return;
        }
    }

    /**
     * Generates a thumbnail for the PDF from the first page of the document.
     *
     * @return Imagick
     */
    public function getThumbnail(): Imagick
    {
        try {

            $image = new Imagick($this->file->getPathname().'[0]1');
            $image->setImageFormat('png');

        } catch (Exception $e) {

            $default_pdf_thumbnail = __DIR__.'/../../../../public/img/extensions/pdf.png';
            $image = new Imagick($default_pdf_thumbnail);
            
        }

        return $image;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle(): string
    {
        $metadata = $this->getMetadata();

        if (isset($metadata['Title'])) {
            return is_array($metadata['Title']) ? $metadata['Title'][0] ?? '' : $metadata['Title'];
        }

        return parent::getTitle();
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
