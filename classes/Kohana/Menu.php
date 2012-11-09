<?php defined('SYSPATH') OR die('No direct script access.');

class Kohana_Menu
{
	/**
	 *
	 * @var	array	Array of variables to be set in the view.
	 */
	protected $_data = array();

	/**
	 *
	 * @var	array	Array of menu items
	 */
	protected $_items = array();

	/**
	 *
	 * @var	string	Filename of the view to display the menu.
	 */
	protected $_view_filename;

	/**
	 *
	 * @var	string	Default menu group for [Menu::factory()]
	 */
	public static $default = 'default';

	/**
	 * Convert the menu object to a string by calling [Menu::render()]
	 */
	public function __toString()
	{
		return (string) $this->render();
	}

	/**
	 * Generate a menu object
	 *
	 * @param	string	$group	The menu group to be generated. The default can be set via [Menu::$default]
	 * @param	array	$data	Array of variables to be set in the menu's view.
	 * @uses		Menu::$default
	 */
	public function __construct($group = NULL, array $data = NULL)
	{
		// If $group isn't specified then use the default from [Menu::$default].
		if ($group === NULL)
		{
			$group = Menu::$default;
		}

		// Load the menu config.
		$config = Kohana::$config->load("menu.$group");

		// Set the view filename
		$this->_view_filename = Arr::get($config, 'view_filename');

		// Set the menu items.
		$this->_items = (array) Arr::get($config, 'items');

		// Set the m
		// Set the view paramaters
		$this->_data = $data;
	}

	/**
	 * Filter the menu items so that any items which have a role set are only displayed if the current user is logged in to the specified role.
	 */
	protected function _filter_items()
	{
		// Array of items we're going to include for this menu.
		$items = array();

		// Get the Auth object to determine which menu items the current user should see.
		$auth = Auth::instance();

		foreach ($this->_items as $item)
		{
			// Include the item in the menu if a required role isn't given or the current user is logged in to the role.
			if ( ! isset($item['role']) OR $auth->logged_in($item['role']))
			{
				$items[] = $item;
			}
		}

		$this->_items = $items;
	}

	/**
	 * Generate a menu object
	 *
	 * @param	string	$group	The menu group to be generated. The default can be set via [Menu::$default]
	 * @param	array	$data	Array of variables to be set in the menu's view.
	 * @return	Menu
	 */
	public static function factory($group = NULL, array $data = NULL)
	{
		return new Menu($group, $data);
	}

	/**
	 * Display the menu.
	 *
	 * @param	string	$view_filename		Set the name of the view to use to display the menu.
	 * @return	string
	 * @uses		[Auth::logged_in()]
	 */
	public function render($view_filename = NULL)
	{
		// If a view filename has been given then set it.
		if ($view_filename !== NULL)
		{
			$this->_view_filename = $view_filename;
		}

		$this->_filter_items();

		// Check that we've got some items to add to the menu.
		if ( ! empty($this->_items))
		{
			// If there's a template for this section then use that, otherwise use a generic template.
			$view = View::factory($this->_view_filename, $this->_data);
			$view->menu_items = $this->_items;

			return $view->render();
		}
	}

	/**
	 * Set paramaters for the view.
	 *
	 * @param	mixed	$key	A variable name or array of name => values.
	 * @param	mixed	$value	Value when setting a single variable.
	 * @return	Menu	Returns the current menu object.
	 */
	public function set($key, $value = NULL)
	{
		if (is_array($key))
		{
			foreach ($key as $name => $value)
			{
				$this->_data[$name] = $value;
			}
		}
		else
		{
			$this->_data[$key] = $value;
		}

		return $this;
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
		if (is_string($key))
		{
			$key = usort($this->_items, function($a, $b) use ($key) {
				return $a[$key] - $b[$key];
			});
		}

		if (is_callable($key))
		{
			call_user_func($key, $this->_items);
		}

		return $this;
	}

	/**
	 * Get / set the filename of the view which will be used to display the menu items.
	 *
	 * @param string $view_filename
	 * @return mixed Returns a string when used as a getter or an instance of Menu when used as a setter.
	 * @uses [Menu::$_view_filename]
	 */
	public function view($view_filename = NULL)
	{
		if ($view_filename === NULL)
		{
			// Act as a getter.
			return $this->_view_filename;
		}
		else
		{
			// Act as a setter.
			$this->_view_filename = $view_filename;

			return $this;
		}
	}
}