<?php

namespace Boom;

use Kohana;

class TextEditorToolbar
{
    protected $_buttonSet = 'text';
    protected $_config;
    protected $_htmlBefore = '<div data-buttonset="{buttonset}">';
    protected $_htmlAfter = '</div>';

    public function __construct($button_set = null)
    {
        $button_set && $this->_buttonSet = $button_set;
        $this->_config = Kohana::$config->load('text_editor_toolbar');
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public static function getAvailableButtonSets()
    {
        return array_keys(Kohana::$config->load('text_editor_toolbar')->get('button_sets'));
    }

    public function getButton($type)
    {
        return \Arr::get($this->_config->get('buttons'), $type);
    }

    public function getButtons()
    {
        return \Arr::get($this->_config->get('button_sets'), $this->_buttonSet);
    }

    public function getHtmlBefore()
    {
        return str_replace('{buttonset}', $this->_buttonSet, $this->_htmlBefore);
    }

    public function render()
    {
        return $this->getHtmlBefore() . $this->_showButtons() . $this->_htmlAfter;
    }

    protected function _showButtons()
    {
        $buttons = '';

        foreach ($this->getButtons() as $type) {
            list($text, $attrs) = $this->getButton($type);
            $buttons .= UI::button($type, $text, (array) $attrs);
        }

        return $buttons;
    }
}
