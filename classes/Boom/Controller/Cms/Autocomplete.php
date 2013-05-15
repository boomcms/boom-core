<?php defined('SYSPATH') OR die('No direct script access.');

/**
 * Autocomplete controller
 * Backend for suggesting tags, assets, or pages via an autocomplete frontend.
 *
 * @package	BoomCMS
 * @category	Controllers
 * @author	Rob Taylor
 * @copyright	Hoop Associates
 */
class Boom_Controller_Cms_Autocomplete extends Boom_Controller
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
	 * Autocomplete on asset title.
	 */
	public function action_assets()
	{
		// Build the query.
		$query = DB::select('title')
			->from('assets')
			->where('title', 'like', "%$this->text%")
			->order_by('title', 'asc')
			->limit($this->count);

		// Get the results
		$results = $query
			->execute()
			->as_array('title');

		// Get an array of asset titles from the results.
		$this->results = array_keys($results);
	}

	/**
	 * Auto complete on page title
	 */
	public function action_pages()
	{
		// Build a query to find pages matching title.
		$query = DB::select('title')
			->from('pages')
			->join('page_versions', 'inner')
			->on('pages.id', '=', 'page_versions.page_id');

		if ($this->editor->state_is(Editor::EDIT))
		{
			// Get the most recent version for each page.
			$query
				->join(array(
					DB::select(array(DB::expr('max(id)'), 'id'))
						->from('page_versions')
						->where('stashed', '=', FALSE)
						->group_by('page_id'),
					'current_version'
				))
				->on('page_versions.id', '=', 'current_version.id');
		}
		else
		{
			// Get the most recent published version for each page.
			$query
				->join(array(
					DB::select(array(DB::expr('max(id)'), 'id'))
						->from('page_versions')
						->where('embargoed_until', '<=', $this->editor->live_time())
						->where('stashed', '=', FALSE)
						->where('published', '=', TRUE)
						->group_by('page_id'),
					'current_version'
				))
				->on('page_versions.id', '=', 'current_version.id')
				->where('page_versions.page_deleted', '=', FALSE)
				->where('visible_from', '<=', $this->editor->live_time())
				->and_where_open()
					->where('visible_to', '>=', $this->editor->live_time())
					->or_where('visible_to', '=', 0)
				->and_where_close();
		}

		$query
			->where('title', 'like', "%$this->text%")
			->limit($this->count)
			->order_by('title', 'asc');

		// Get the results
		$results = $query
			->execute()
			->as_array('title');

		// Get an array of page titles.
		$this->results = array_keys($results);
	}

	/**
	 * Suggest tag names based on an infix.
	 *
	 */
	public function action_tags()
	{
		// Determine whether we're filtering page or assets tags so we know which table to join on.
		$object_name = ($this->request->query('type') == Model_Tag::ASSET)? 'asset' : 'page';
		$join_table = Inflector::plural($object_name).'_tags';
		$object_id_column = $object_name.'_id';

		// Build a query to find tags matching on path.
		$query = DB::select('tags.name', 'tags.id')
			->from('tags')
			->join($join_table, 'inner')
			->on('tags.id', '=', $join_table.".tag_id")
			->where('name', 'like', "%$this->text%")
			->where('type', '=', $this->request->query('type'))
			->order_by('name', 'asc')
			->distinct(TRUE)
			->limit($this->count);

		// If an array of tags has been sent as well then only include tags which are in use with the given tags.
		$tags = $this->request->query('tags');

		if ( ! empty($tags))
		{
			$tag_count = count($tags);

			// Only match tags which are in use with the given tags.
			$query
				->join(array($join_table, 't1'), 'inner')
				->on('tags.id', '=', 't1.tag_id')
				->join(array($join_table, 't2'), 'inner')
				->on("t1.$object_id_column", '=', "t2.$object_id_column")
				->where('t2.tag_id', 'IN', $tags)
				->where('t1.tag_id', 'not in', $tags)
				->group_by("t1.$object_id_column")
				->having(DB::expr('count(distinct t2.tag_id)'), '>=', $tag_count);
		}

		// Get the query results.
		$results = $query
			->execute()
			->as_array('name', 'id');

		// Turn the results into a flat array of tag paths and pop it in $this->results for outputting.
		$this->results = $results;
	}

	public function after()
	{
		$this->response
			->headers('content-type', 'application/json')
			->body(json_encode($this->results));
	}
}