<?php

namespace BoomCMS\Core\Chunk;

class Timestamp extends BaseChunk
{
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

    protected $html = "<span class='b-chunk-timestamp'>{time}</span>";
    protected $type = 'timestamp';

    public function attributes()
    {
        return [
            $this->attributePrefix . 'timestamp' => $this->getTimestamp(),
            $this->attributePrefix . 'format' => $this->getFormat(),
        ];
    }

    protected function show()
    {
        return str_replace('{time}', date($this->getFormat(), $this->getTimestamp()), $this->html);
    }

    protected function showDefault()
    {
        return str_replace('{time}', $this->getPlaceholderText(), $this->html);
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
}
