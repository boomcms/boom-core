<?php

namespace Boom;

class TextEditorToolbar
{
    protected $_buttonSet = 'text';
    protected $_config;
    protected $_htmlBefore = '<div id="wysihtml5-toolbar" class="b-toolbar b-toolbar-vertical b-toolbar-text">';
    protected $_htmlAfter = '</div>';

    public function __construct($button_set = null)
    {
        $button_set && $this->_buttonSet = $button_set;
        $this->_config = \Kohana::$config->load('text_editor_toolbar');
    }

    public function __toString()
    {
        return (string) $this->render();
    }

    public function getButton($type)
    {
        return \Arr::get($this->_config->get('buttons'), $type);
    }

    public function getButtons()
    {
        return \Arr::get($this->_config->get('button_sets'), $this->_buttonSet);
    }

    public function render()
    {
        return $this->_htmlBefore.$this->_showButtons().$this->_htmlAfter;
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
