<?php

namespace BoomCMS\Core\Menu;

use BoomCMS\Core\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

class Menu
{
    /**
	 *
	 * @var	array
	 */
    protected $menuItems = [];

    protected $viewFilename = 'boom::menu.boom';

    /**
	 * Convert the menu object to a string by calling [Menu::render()]
	 */
    public function __toString()
    {
        return (string) $this->render();
    }

    /**
	 * Filter the menu items so that any items which have a role set are only displayed if the current user is logged in to the specified role.
	 */
    protected function filterItems()
    {
        // Array of items we're going to include for this menu.
        $itemsToInclude = [];

        foreach ($this->menuItems as $item) {
            if ( ! isset($item['role']) || Auth::loggedIn($item['role'])) {
                $itemsToInclude[] = $item;
            }
        }

        $this->menuItems = $itemsToInclude;
    }

    public function render()
    {
        $this->menuItems = Config::get('boomcms.menu');
        $this->filterItems();
        $this->sort('priority');

        if ( ! empty($this->menuItems)) {
            return View::make($this->viewFilename, [
                'items' => $this->menuItems
            ])->render();
        }
    }

    /**
	 * Sort the items in the menu by the specified key.
	 * Can also be a passed a callback function for custom sorting.
	 *
	 * @param	mixed	$key
	 * @return	Menu	Current menu object
	 */
    public function sort($key)
    {
        if (is_string($key)) {
            $key = usort($this->menuItems, function ($a, $b) use ($key) {
                return $a[$key] - $b[$key];
            });
        }

        if (is_callable($key)) {
            call_user_func($key, $this->menuItems);
        }

        return $this;
    }
}
