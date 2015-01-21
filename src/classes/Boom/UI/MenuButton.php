<?php

namespace Boom\UI;

class MenuButton extends AbstractUIElement
{
    public function render()
    {
        return new Button('menu', __('Menu'), ['id' => 'b-menu-button', 'class' => 'menu-btn']);
    }
}
