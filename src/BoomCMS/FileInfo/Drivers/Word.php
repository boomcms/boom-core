<?php

namespace BoomCMS\FileInfo\Drivers;

use Carbon\Carbon;
use Exception;
use PhpOffice\PhpWord\IOFactory;

class Word extends DefaultDriver
{
    /**
     * {@inheritdoc}
     *
     * @return null|Carbon
     */
    public function getCreatedAt()
    {
        $metadata = $this->getMetadata();

        return isset($metadata['created']) ? Carbon::createFromTimestamp($metadata['created']) : null;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTitle(): string
    {
        $metadata = $this->getMetadata();

        return $metadata['title'] ?? '';
    }

    /**
     * Retrieves metadata from the file and turns it into an array.
     *
     * @return array
     */
    protected function readMetadata(): array
    {
        try {
            $phpWord = IOFactory::load($this->file->getPathname());
            $docinfo = $phpWord->getDocInfo();

            $attrs = [
                'creator'        => $docinfo->getCreator(),
                'created'        => $docinfo->getCreated(),
                'lastModifiedBy' => $docinfo->getLastModifiedBy(),
                'modified'       => $docinfo->getModified(),
                'title'          => $docinfo->getTitle(),
                'description'    => $docinfo->getDescription(),
                'subject'        => $docinfo->getSubject(),
                'keywords'       => $docinfo->getKeywords(),
                'category'       => $docinfo->getCategory(),
                'company'        => $docinfo->getCompany(),
                'manager'        => $docinfo->getManager(),
            ];

            foreach ($attrs as $key => $value) {
                if (empty($value)) {
                    unset($attrs[$key]);
                }
            }

            return $attrs;
        } catch (Exception $e) {
            return [];
        }
    }
}
