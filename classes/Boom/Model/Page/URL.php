<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	BoomCMS
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Model_Page_URL extends ORM
{
	protected $_belongs_to = array('page' => array('foreign_key' => 'page_id'));
	protected $_table_columns = array(
		'id'			=>	'',
		'page_id'		=>	'',
		'location'		=>	'',
		'is_primary'	=>	'',
		'redirect'		=>	'',
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
	 * @uses Boom_Model_Page_URL::is_primary()
	 *
	 * @param \Validation $validation
	 * @return \Boom_Model_Page_URL
	 */
	public function create(\Validation $validation = NULL)
	{
		parent::create($validation);

		// If the is_primary property is true.
		if ($this->is_primary)
		{
			// Call Boom_Model_Page_URL::make_primary()
			// to ensure that this is the only primary URL for this page.
			$this->make_primary();
		}

		// Return the current object.
		return $this;
	}

	/**
	 * Checks that the URL is unique before saving.
	 * This can't be done by a unique index on the table as the location column is too long to be indexed.
	 *
	 * @return boolean
	 */
	public function location_available($location)
	{
		// Prepare a query to determine when the location is already in use.
		$query = DB::select('id')
			->from('page_urls')
			->where('location', '=', $location)
			->limit(1);

		// If the current object has already been saved then make sure we ignore it from the query.
		if ($this->_saved OR $this->_loaded)
		{
			$query->where('id', '!=', $this->id);
		}

		// Run the query.
		$exists = $query->execute($this->_db);

		// Were there any results?
		return ($exists->count() == 0);
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
				array(array($this, 'location_available')),
			),
		);
	}

	public function filters()
	{
		return array(
			'location' => array(
				array('trim'),
				array('strtolower'),
				array('strip_tags'),								// Make sure there's no HTML in there.
				array('parse_url', array(':value', PHP_URL_PATH)),		// Remove the hostname
				array('trim', array(':value', '/')),					// Remove '/' from the beginning or end of the link
				array('preg_replace', array('|/+|', '/', ':value')),		// Remove duplicate forward slashes.
				array('preg_replace', array('![^'.preg_quote('-').'\/\pL\pN\s]+!u', '', ':value')),
				array('preg_replace', array('!['.preg_quote('-').'\s]+!u', '-', ':value')),
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
