<?php

namespace BoomCMS\Core\UI;

use HTML;

class Button extends AbstractUIElement
{
    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $text;

    /**
     *
     * @var array
     */
    private $attrs;

    /**
     *
     * @param string $type  The type of button, used to determine the button icon
     * @param string $text  Button text
     * @param array  $attrs HTML attributes for the button
     */
    public function __construct($type, $text, $attrs = [])
    {
        $this->type = $type;
        $this->text = $text;
        $this->attrs = $attrs;
    }

    public function render()
    {
        // Add the important b-button class to the button attributes which has all the CSS rules targeted at it.
        isset($this->attrs['class']) || $this->attrs['class'] = '';
        $this->attrs['class'] = trim($this->attrs['class'] . ' b-button');

        // Make the button text the title of the button.
        $this->attrs['title'] = $this->text;

        $attrs_string = '';
        foreach ($this->attrs as $key => $value) {
            $attrs_string .= "$key = \"$value\"";
        }

        return "<button $attrs_string><span class='b-button-icon fa-2x fa fa-{$this->type}'></span><span class='b-button-text'>{$this->text}</span></button>";

    }
}
