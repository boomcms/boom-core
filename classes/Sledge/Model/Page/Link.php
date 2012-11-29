<?php defined('SYSPATH') OR die('No direct script access.');

/**
 *
 * @package	Sledge
 * @category	Models
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Model_Page_Link extends ORM
{
	protected $_belongs_to = array('page' => array('foreign_key' => 'page_id'));
	protected $_table_columns = array(
		'id'			=>	'',
		'page_id'		=>	'',
		'location'		=>	'',
		'is_primary'	=>	'',
		'redirect'		=>	'',
	);
	protected $_cache_columns = array('location');

	/**
	 * Checks that the URI is unique before saving.
	 * This can't be done by a unique index on the table as the uri column is too long to be indexed.
	 */
	public function create(Validation $validation = NULL)
	{
		// Does the URI already exist?
		$exists = DB::select('id')
			->from('page_links')
			->where('location', '=', $this->location)
			->limit(1)
			->execute();

		if ($exists->count() > 0)
		{
			throw new Kohana_Exception("Link :link is already in use", array(':link' => $this->location));
		}

		return parent::create($validation);
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
			),
		);
	}

	public function filters()
	{
		return array(
			'location' => array(
				array('trim'),
				array('strip_tags'),								// Make sure there's no HTML in there.
				array('parse_url', array(':value', PHP_URL_PATH)),		// Remove the hostname
				array('trim', array(':value', '/')),					// Remove '/' from the beginning or end of the link
				array('preg_replace', array('|/+|', '/', ':value')),		// Remove duplicate forward slashes.
			),
		);
	}

	/**
	 * Function to be called when making a link the primary link for a page.
	 * Updates the page's primary link in the cache and ensures that this will be the only primary link for a page.
	 *
	 * This function will be called when making an existing link the primary link for a page
	 * Or when the page title is changed and a new link is created which will be made the primary link.
	 *
	 * @return	Model_Page_Link
	 */
	public function make_primary()
	{
		// Save the primary link in cache
		Cache::instance()->set('primary_link_for_page:' . $this->page_id, $this->location);

		// Ensure that this is the only primary link for the page.
		// We do this through the ORM rather than a DB update query to catch cached links
		$page_links = ORM::factory('Page_Link')
			->where('page_id', '=', $this->page_id)
			->where('id', '!=', $this->id)
			->where('is_primary', '=', TRUE)
			->find_all();

		foreach ($page_links as $page_link)
		{
			$page_link->is_primary = FALSE;
			$page_link->save();
		}

		return $this;
	}
}
