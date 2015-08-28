<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;

class Timestamp extends BaseChunk
{
    use AcceptsHtmlString;

    public static $defaultFormat = 'j F Y';
    public static $formats = [
        'j F Y',
        'j F Y H:i',
        'j F Y h:i A',
        'l j F Y',
        'l j F Y H:i',
        'l j F Y h:i A',
        'H:i',
        'h:i A',
    ];

    protected $defaultHtml = "<span class='b-chunk-timestamp'>{time}</span>";
    protected $type = 'timestamp';

    protected function addContentToHtml($content)
    {
        $html = $this->html ?: $this->defaultHtml;

        return str_replace('{time}', $content, $html);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'timestamp' => $this->getTimestamp(),
            $this->attributePrefix.'format'    => $this->getFormat(),
        ];
    }

    public function hasContent()
    {
        return $this->getTimestamp() > 0;
    }

    public function getFormat()
    {
        return isset($this->attrs['format']) ? $this->attrs['format'] : static::$defaultFormat;
    }

    public function getTimestamp()
    {
        return isset($this->attrs['timestamp']) ? $this->attrs['timestamp'] : 0;
    }

    protected function show()
    {
        return $this->addContentToHtml(date($this->getFormat(), $this->getTimestamp()));
    }

    protected function showDefault()
    {
        return $this->addContentToHtml($this->getPlaceholderText());
    }
}
