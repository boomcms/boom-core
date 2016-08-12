<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Traits\Renderable;
use Collective\Html\HtmlFacade as Html;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

abstract class BaseChunk
{
    use Renderable;

    protected $attributePrefix = 'data-boom-';

    /**
     * @var mixed
     */
    protected $after;

    /**
     * @var array
     */
    protected $attrs;

    /**
     * @var mixed
     */
    protected $before;

    /**
     * @var string
     */
    protected $defaultTemplate;

    /**
     * @var bool
     */
    protected $editable = false;

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
     *
     * @var string
     */
    protected $slotname;

    /**
     * @var string
     */
    protected $template;

    protected $viewPrefix = 'boomcms.chunks::';

    public function __construct(Page $page, array $attrs, $slotname)
    {
        $this->page = $page;
        $this->attrs = $attrs;
        $this->slotname = $slotname;
    }

    /**
     * Displays the chunk when chunk data has been set.
     */
    abstract protected function show();

    /**
     * Set content which should be added after the chunk.
     *
     * The content is added either when the chunk has content in the site view.
     * Or when the editor is enabled and default content is being displayed.
     *
     * @param mixed $content
     *
     * @return $this
     */
    public function after($content)
    {
        $this->after = $content;

        return $this;
    }

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
     * Set content which should be added before the chunk.
     *
     * The content is added either when the chunk has content in the site view.
     * Or when the editor is enabled and default content is being displayed.
     *
     * @param mixed $content
     *
     * @return $this
     */
    public function before($content)
    {
        $this->before = $content;

        return $this;
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
     * Set whether the chunk is editable.
     *
     * @param bool $editable
     *
     * @return $this
     */
    public function editable($editable)
    {
        $this->editable = $editable;

        return $this;
    }

    /**
     * Returns the ID of the chunk.
     *
     * @return int
     */
    public function getId()
    {
        return isset($this->attrs['id']) ? $this->attrs['id'] : 0;
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
            $this->attributePrefix.'chunk-id'      => $this->getId(),
        ];
    }

    public function getPlaceholderText()
    {
        if ($this->placeholderText) {
            return $this->placeholderText;
        }

        $prefix = "boomcms::chunks.{$this->getType()}";

        return (Lang::has("$prefix.{$this->slotname}")) ?
            Lang::get("$prefix.{$this->slotname}")
            : Lang::get("$prefix.default");
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
        try {
            $html = $this->html();

            if ($this->editable === true) {
                $html = $this->addAttributesToHtml($html);
            }

            return empty($html) ? $html : $this->before.$html.$this->after;
        } catch (\Exception $e) {
            if (App::environment() === 'local') {
                throw $e;
            }
        }
    }

    /**
     * Displays default chunk HTML.
     *
     * @return View
     */
    protected function showDefault()
    {
        return ViewFacade::make($this->viewPrefix."default.{$this->getType()}.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
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
        if (!$this->hasContent() && !$this->isEditable()) {
            return '';
        }

        if ($this->template === null) {
            $this->template = $this->defaultTemplate;
        }

        $content = $this->hasContent() ? $this->show() : $this->showDefault();

        // If the return data is a View then assign any parameters to it.
        if ($content instanceof View && !empty($this->viewParams)) {
            $content->with($this->viewParams);
        }

        return (string) $content;
    }

    /**
     * Set parameters to be passed to the chunk view.
     *
     * @param array $params
     *
     * @return $this
     */
    public function params(array $params)
    {
        $this->viewParams = $params;

        return $this;
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
