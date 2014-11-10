<?php

namespace Boom;

/**
 * Helper class with static methods for creating UI elements.
 *
 */
abstract class UI
{

    public static function menuButton()
    {
        return new UI\Button('menu', __('Menu'), ['id' => 'b-menu-button', 'class' => 'menu-btn']);
    }
}
