<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Autocomplete controller
 * Backend for suggesting tags, assets, or pages via an autocomplete frontend.
 *
 * @package	Sledge
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Sledge_Controller_Cms_Autocomplete extends Sledge_Controller
{
	/**
	 * The number of results to be returned. Default is 10.
	 *
	 * @var	integer
	 */
	public $count;

	/**
	 * Array of matches to be output.
	 *
	 * @var	array
	 */
	public $results = array();

	/**
	 * The text to search for.
	 *
	 * @var	string
	 */
	public $text;

	public function before()
	{
		parent::before();

		// Determine the number of results to display
		// Use 10 as the default value if nothing is sent.
		$this->count = ($this->request->query('count') > 0)? $this->request->query('count') : 10;

		// The text to search for.
		$this->text = $this->request->query('text');
	}

	/**
	 * Suggest tag names based on an infix.
	 *
	 */
	public function action_tags()
	{
		// Build a query to find matching tags.
		$query = DB::select('tags.path')
			->from('tags')
			->where('path', 'like', "%$this->text%")
			->where('type', '=', $this->request->query('type'))
			->order_by('path', 'asc')
			->limit($this->count);

		// Get the query results.
		$results = $query
			->execute()
			->as_array('path');

		// Turn the results into a flat array of tag paths and pop it in $this->results for outputting.
		$this->results = array_keys($results);
	}

	public function after()
	{
		$this->response
			->headers('content-type', 'application/json')
			->body(json_encode($this->results));
	}
}