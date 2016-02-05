<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Traits\Renderable;
use Illuminate\Html\HtmlFacade as Html;
use Illuminate\Support\Facades\Lang;
use Illuminate\View\View;

abstract class BaseChunk
{
    use Renderable;

    protected $attributePrefix = 'data-boom-';

    /**
     * @var array
     */
    protected $attrs;

    /**
     * @var string
     */
    protected $defaultTemplate;

    /**
     * @var bool
     */
    protected $editable;

    /**
     * @var Page
     */
    protected $page;

    /**
     * An array of parameters which will be passed to the chunk view.
     *
     * @var array
     */
    protected $viewParams = [];

    /**
     * @var string
     */
    protected $placeholderText;

    /**
     * The slotname used to find the chunk.
     * This has to be stored seperately to $this->_chunk so that for default chunks where $this->_chunk isn't loaded we know the slotname where the chunk belongs.
     *
     * @var string
     */
    protected $slotname;

    /**
     * @var string
     */
    protected $template;

    protected $viewPrefix = 'boomcms.chunks::';

    public function __construct(Page $page, array $attrs, $slotname, $editable = false)
    {
        $this->page = $page;
        $this->attrs = $attrs;
        $this->slotname = $slotname;
        $this->editable = $editable;
    }

    /**
     * Displays the chunk when chunk data has been set.
     */
    abstract protected function show();

    /**
     * Displays default chunk HTML.
     *
     * @return View
     */
    abstract protected function showDefault();

    /**
     * Attributes to be added to the chunk HTML.
     *
     * Can be overriden to pass additional info to javascript editor.
     *
     * @return array()
     */
    public function attributes()
    {
        return [];
    }

    /**
     * This adds the necessary classes to chunk HTML for them to be picked up by the JS editor.
     * i.e. it makes chunks editable.
     *
     * @param string $html HTML to add classes to.
     *
     * @return string
     */
    public function addAttributesToHtml($html)
    {
        $html = trim((string) $html);

        $attributes = array_merge($this->getRequiredAttributes(), $this->attributes());
        $attributesString = Html::attributes($attributes);

        return preg_replace('|<(.*?)>|', "<$1$attributesString>", $html, 1);
    }

    /**
     * Returns an array of HTML attributes which are required to be make the chunk editable.
     *
     * To add other attributes see the attributes method.
     *
     * @return array
     */
    public function getRequiredAttributes()
    {
        return [
            $this->attributePrefix.'chunk'         => $this->getType(),
            $this->attributePrefix.'slot-name'     => $this->slotname,
            $this->attributePrefix.'slot-template' => $this->template,
            $this->attributePrefix.'page'          => $this->page->getId(),
            $this->attributePrefix.'chunk-id'      => isset($this->attrs['id']) ? $this->attrs['id'] : 0,
        ];
    }

    public function getPlaceholderText()
    {
        if ($this->placeholderText) {
            return $this->placeholderText;
        }

        return (Lang::has("boomcms::chunks.{$this->getType()}.{$this->slotname}")) ?
            Lang::get("boomcms::chunks.{$this->getType()}.{$this->slotname}")
            : Lang::get("boomcms::chunks.{$this->getType()}.default");
    }

    /**
     * Returns the slotname for the chunk.
     *
     * @return string
     */
    public function getSlotname()
    {
        return $this->slotname;
    }

    public function getType()
    {
        return strtolower(class_basename($this));
    }

    /**
     * @return bool
     */
    public function isEditable()
    {
        return $this->editable;
    }

    /**
     * Makes a chunk readonly.
     *
     * @return BaseChunk
     */
    public function readonly()
    {
        $this->editable = false;

        return $this;
    }

    /**
     * Attempts to get the chunk data from the cache, otherwise calls _execute to generate the cache.
     */
    public function render()
    {
        $html = $this->html();

        if ($this->editable === true) {
            $html = $this->addAttributesToHtml($html);
        }

        return $html;
    }

    /**
     * Returns whether the chunk has any content.
     *
     * @return bool
     */
    abstract public function hasContent();

    /**
     * Generate the HTML to display the chunk.
     *
     * @return string
     */
    public function html()
    {
        if ($this->template === null) {
            $this->template = $this->defaultTemplate;
        }

        if ($this->hasContent()) {
            $return = $this->show();
        } elseif ($this->isEditable()) {
            $return = $this->showDefault();
        } else {
            return '';
        }

        // If the return data is a View then assign any parameters to it.
        if ($return instanceof View && !empty($this->viewParams)) {
            $return->with($this->viewParams);
        }

        return (string) $return;
    }

    /**
     * Getter / setter method for template parameters.
     */
    public function params($params = null)
    {
        if ($params === null) {
            return $this->viewParams;
        } else {
            $this->viewParams = $params;

            return $this;
        }
    }

    public function setPlaceholderText($text)
    {
        $this->placeholderText = $text;

        return $this;
    }

    /**
     * Set the template to display the chunk.
     *
     * @param string $template The name of a view file.
     *
     * @return Chunk
     */
    public function template($template = null)
    {
        // Set the template filename.
        $this->template = $template;

        return $this;
    }
}
