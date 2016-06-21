<?php

namespace BoomCMS\Chunk;

use BoomCMS\Foundation\Chunk\AcceptsHtmlString;
use Closure;
use DateTime;

class Timestamp extends BaseChunk
{
    use AcceptsHtmlString;

    /**
     * @var Closure
     */
    protected $closure;

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
    protected $formatIsEditable = true;

    /**
     * Define a closure to apply to the chunk content before rendering.
     *
     * @param Closure $closure
     *
     * @return $this
     */
    public function apply(Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    protected function addContentToHtml($content)
    {
        $html = $this->html ?: $this->defaultHtml;

        return str_replace('{time}', $content, $html);
    }

    public function attributes()
    {
        return [
            $this->attributePrefix.'timestamp'        => $this->getTimestamp(),
            $this->attributePrefix.'format'           => $this->getFormat(),
            $this->attributePrefix.'formatIsEditable' => (int) $this->formatIsEditable,
        ];
    }

    public function hasContent()
    {
        return $this->getFormat() && $this->getTimestamp() > 0;
    }

    /**
     * @return DateTime
     */
    public function getDateTime()
    {
        return (new DateTime())->setTimestamp($this->getTimestamp());
    }

    public function getFormat()
    {
        return isset($this->attrs['format']) ? $this->attrs['format'] : static::$defaultFormat;
    }

    public function getTimestamp()
    {
        return isset($this->attrs['timestamp']) ? $this->attrs['timestamp'] : 0;
    }

    public function setFormat($format)
    {
        $this->formatIsEditable = false;
        $this->attrs['format'] = $format;

        return $this;
    }

    protected function show()
    {
        $content = (is_callable($this->closure)) ?
            call_user_func($this->closure, $this)
            : date($this->getFormat(), $this->getTimestamp());

        return $this->addContentToHtml($content);
    }

    protected function showDefault()
    {
        return $this->addContentToHtml($this->getPlaceholderText());
    }
}
