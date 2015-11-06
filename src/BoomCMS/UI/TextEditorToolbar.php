<?php

namespace BoomCMS\UI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class TextEditorToolbar extends AbstractUIElement
{
    protected $_buttonSet = 'text';
    protected $_config;
    protected $_htmlBefore = '<div data-buttonset="{buttonset}">';
    protected $_htmlAfter = '</div>';

    public function __construct($button_set = null)
    {
        $button_set && $this->_buttonSet = $button_set;
        $this->_config = Config::get('boomcms.text_editor_toolbar');
    }

    public static function getAvailableButtonSets()
    {
        $config = Config::get('boomcms.text_editor_toolbar');

        return array_keys($config['button_sets']);
    }

    public function getButton($type)
    {
        return $this->_config['buttons'][$type];
    }

    public function getButtons()
    {
        return $this->_config['button_sets'][$this->_buttonSet];
    }

    public function getHtmlBefore()
    {
        return str_replace('{buttonset}', $this->_buttonSet, $this->_htmlBefore);
    }

    public function render()
    {
        return $this->getHtmlBefore().$this->_showButtons().$this->_htmlAfter;
    }

    protected function _showButtons()
    {
        $buttons = '';

        foreach ($this->getButtons() as $group) {
            $buttons .= '<div class="b-button-group">';

            foreach ($group as $type) {
                list($text, $attrs) = $this->getButton($type);
                $buttons .= new Button($type, $text, (array) $attrs);
            }

            $buttons .= '</div>';
        }

        if ($this->_buttonSet === 'block') {
            $buttons .= View::make('boomcms::editor.table_buttons')->render();
        }

        return $buttons;
    }
}
