<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 */
class Boom_Model_Page_URL extends ORM
{
	protected $_belongs_to = array('page' => array('foreign_key' => 'page_id'));
	protected $_table_columns = array(
		'id'			=>	'',
		'page_id'		=>	'',
		'location'		=>	'',
		'is_primary'	=>	'',
	);
	protected $_table_name = 'page_urls';

	/**
	 * Convert a Model_Page_URL object to a string
	 * Returns the location property for the current model
	 *
	 * @uses URL::site()
	 * @return string
	 */
	public function __toString()
	{
		return URL::site($this->location, Request::$current);
	}

	/**
	 * Calls [Boom_Model_Page_URL::make_primary()] when a page URL is created which has the is_primary property set to true.
	 * This removes the need to call is_primary() after creating a URL.
	 *
	 * @param \Validation $validation
	 * @return \Boom_Model_Page_URL
	 */
	public function create(\Validation $validation = NULL)
	{
		parent::create($validation);

		// Ensure that this is the only primary URL for this page.
		$this->is_primary AND $this->make_primary();

		return $this;
	}

	/**
	* ORM Validation rules
	* @link http://kohanaframework.org/3.2/guide/orm/examples/validation
	*/
	public function rules()
	{
		return array(
			'page_id' => array(
				array('not_empty'),
				array('numeric'),
			),
			'location' => array(
				array('max_length', array(':value', 2048)),
				array(array('Page_URL', 'is_available')),
			),
		);
	}

	public function filters()
	{
		return array(
			'location' => array(
				array(array('Page_URL', 'sanitise'))
			),
		);
	}

	/**
	 * Function to be called when making a link the primary link for a page.
	 * Ensures that this will be the only primary link for a page.
	 *
	 * This function will be called when making an existing link the primary link for a page
	 * Or when the page title is changed and a new link is created which will be made the primary link.
	 *
	 * @return	Model_Page_URL
	 */
	public function make_primary()
	{
		// Ensure that this is the only primary link for the page.
		DB::update($this->_table_name)
			->set(array('is_primary' => FALSE))
			->where('page_id', '=', $this->page_id)
			->where('id', '!=', $this->id)
			->where('is_primary', '=', TRUE)
			->execute($this->_db);

		// Set the is_primary property for this URL to true.
		$this
			->set('is_primary', TRUE)
			->update();

		// Update the primary uri for the page in the pages table.
		DB::update('pages')
			->set(array('primary_uri' => $this->location))
			->where('id', '=', $this->page_id)
			->execute($this->_db);

		return $this;
	}
}
