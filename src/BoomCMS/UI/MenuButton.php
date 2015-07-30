<?php

namespace BoomCMS\UI;

use Illuminate\Support\Facades\Lang;

class MenuButton extends AbstractUIElement
{
    public function render()
    {
        return new Button('bars', Lang::get('Menu'), ['id' => 'b-menu-button', 'class' => 'menu-btn']);
    }
}
