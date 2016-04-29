<?php

namespace BoomCMS\UI;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class TextEditorToolbar extends AbstractUIElement
{
    protected $buttonSet = 'text';
    protected $config;
    protected $htmlBefore = '<div data-buttonset="{buttonset}">';
    protected $htmlAfter = '</div>';

    public function __construct($buttonSet = null)
    {
        $buttonSet && $this->buttonSet = $buttonSet;
        $this->config = Config::get('boomcms.text_editor_toolbar');
    }

    public static function getAvailableButtonSets()
    {
        $config = Config::get('boomcms.text_editor_toolbar');

        return array_keys($config['button_sets']);
    }

    public function getButton($type)
    {
        return $this->config['buttons'][$type];
    }

    public function getButtons()
    {
        return $this->config['button_sets'][$this->buttonSet];
    }

    public function getHtmlBefore()
    {
        return str_replace('{buttonset}', $this->buttonSet, $this->htmlBefore);
    }

    public function render()
    {
        return $this->getHtmlBefore().$this->showButtons().$this->htmlAfter;
    }

    protected function showButtons()
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

        if ($this->buttonSet === 'block') {
            $buttons .= View::make('boomcms::editor.table_buttons')->render();
        }

        return $buttons;
    }
}
