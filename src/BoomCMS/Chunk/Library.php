<?php

namespace BoomCMS\Chunk;

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Helpers;
use Illuminate\Support\Facades\View;

class Library extends BaseChunk
{
    /**
     * @var array
     */
    protected $params;

    protected $defaultTemplate = 'gallery';

    public function __construct(Page $page, array $attrs, $slotname, $editable)
    {
        parent::__construct($page, $attrs, $slotname, $editable);

        $this->params = isset($attrs['params']) ? $this->cleanData((array) $attrs['params']) : [];
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public function cleanData(array $array)
    {
        return array_filter($array, function ($v) {
            return !empty($v);
        });
    }

    public function getAssets()
    {
        return Helpers::getAssets($this->params);
    }

    /**
     * @return void|int
     */
    public function getLimit()
    {
        return $this->getParam('limit');
    }

    /**
     * Returns a search parameter by key.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getParam($key)
    {
        return isset($this->params[$key]) ? $this->params[$key] : null;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->getParam('tag') ? $this->getParam('tag') : [];
    }

    public function getOrder()
    {
        return $this->getParam('order');
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->params) &&
            !empty(array_values($this->params)) &&
            $this->hasFilters();
    }

    /**
     * Returns whether the parameters array contains any filters.
     *
     * @return bool
     */
    public function hasFilters()
    {
        $params = array_except($this->params, ['order', 'limit']);

        return !empty($params) && !empty(array_values($params));
    }

    /**
     * @param array $params
     */
    public function mergeParams(array $params)
    {
        $this->params = $this->params + $params;

        return $this;
    }

    protected function show()
    {
        return View::make($this->viewPrefix."library.$this->template", [
            'tags'    => $this->getTags(),
            'params'  => $this->getParams(),
            'assets'  => function () {
                return $this->getAssets();
            },
        ]);
    }

    protected function showDefault()
    {
        return View::make($this->viewPrefix."default.library.$this->template", [
            'placeholder' => $this->getPlaceholderText(),
        ]);
    }
}
