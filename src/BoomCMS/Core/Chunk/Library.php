<?php

namespace BoomCMS\Core\Chunk;

use BoomCMS\Core\Page\Page;
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
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getTag()
    {
        return (isset($this->params['tag']) && $this->params['tag'] !== '') ?
            $this->params['tag']
            : null;
    }

    /**
     * @return bool
     */
    public function hasContent()
    {
        return !empty($this->params) && !empty(array_values($this->params));
    }

    protected function show()
    {
        return View::make($this->viewPrefix."library.$this->template", [
            'tag'    => $this->getTag(),
            'params' => $this->getParams(),
            'assets' => function () {
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
