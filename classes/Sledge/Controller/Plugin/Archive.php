<?php defined('SYSPATH') OR die('No direct script access.');

/**
*
* @package	Sledge
* @category	Controllers
* @author	Rob Taylor
* @copyright	Hoop Associates
*/
class Sledge_Controller_Plugin_Archive extends Sledge_Controller
{
	/**
	 *
	 * @var Model_Page		The parent page to count the children of.
	 */
	protected $parent;

	public function before()
	{
		parent::before();

		// If a parent page has been specified then use it, otherwise use the parent of the inital request.
		$this->parent = ($this->request->post('parent'))? ORM::factory('Page', $this->request->post('parent')) : Request::initial()->param('page');

		// If the parent isn't found then throw a 404.
		if ( !  $this->parent->loaded())
		{
			throw new HTTP_Exception_404;
		}
	}

	/**
	 * Generates a count of the number of children of the current page by year and month.
	 *
	 * The response body is a json_encoded array. Example array:
	 *
	 *	array(
	 *		'2012'	=>	array(
	 *			'1'	=> array(
	 *				'name'	=>	'January',
	 *				'count'	=>	1,
	 *			),
	 *			2	=>	array(
	 *				'name'	=>	'Febuary'
	 *				'count'	=>	3,
	 *			)
	 *		)
	 *	)
	 *
	 */
	public function action_date()
	{
		// Build a query to get a count of pages grouped by year and month.
		$query = DB::select()
			->select(array(DB::expr('year(t)'), 'year'))
			->select(array(DB::expr('month(t)'), 'month'))
			->select(array(DB::expr('monthname(t)'), 'name'))
			->select(array(DB::expr('count(*)'), 'count'))
			->from(array(
				DB::select(array(DB::expr('from_unixtime(visible_from)'), 't'))
					->from('pages')
					->join('page_versions', 'inner')
					->on('pages.' . Page::join_column($this->parent, $this->person), '=', 'page_versions.id')
					->join('page_mptt', 'inner')
					->on('page_mptt.id', '=', 'pages.id')
					->where('page_mptt.parent_id', '=', $this->parent->id),
				'q'
			))
			->group_by('year', 'month')
			->order_by('year');

		// Run the query and get the results
		$results = $query
			->execute()
			->as_array();

		// Turn the dates in a multi-dimensional array.
		$dates = array();
		foreach ($results as $result)
		{
			$dates[$result['year']][$result['month']] = array(
				'name' => $result['name'],
				'count' => $result['count']
			);
		}

		// Pop the dates in the template.
		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode($dates));
	}

	/**
	* Display an archive box with the number of pages by date or tag.
	*
	*/
	public function action_index()
	{
		// Get the archive options from the post data.
		// This will tell us which sections of the archive to display.
		$options = $this->request->post();

		// Create a hash of the data for caching.
		$hash = md5($this->parent->id . "-" . $this->auth->logged_in() . "-" . serialize($options));

		// Try and get it from the cache.
		 if (Kohana::$environment === Kohana::DEVELOPMENT OR ! Fragment::load("child_page_list:$hash", Date::HOUR))
		 {
			// Create an array of sections.
			// Each section (e.g. date, tags) will be a seperate request to a the date or tags controller.
			$sections = array();

			// Should we display a count of child pages by date?
			if (in_array('date', $options))
			{
				$sections[] = View::factory('sledge/plugin/archive/date', array(
					'dates' => json_decode(
						Request::factory('plugin/archive/date')
							->post(array('parent' => $this->parent->id))
							->execute()
							->body()
					),
				));
			}

			// Include child pages by tag.
			foreach ( (array) Arr::get($options, 'tags') as $name => $route)
			{
				$v = View::factory("sledge/plugin/archive/tag")
					->set('tags',  json_decode(
						Request::factory('plugin/archive/tag')
							->post(array('parent' => $this->parent->id, 'name' => $name, 'route' => $route))
							->execute()
							->body()
					))
					->set('name', $name);

				// This allows us to create different links for author, category, or tag tags.
				// i.e. /blog/author, /blog/category, etc.
				switch($route)
				{
					case 'Authors':
						$section = 'author';
						break;

					case Page::CATEGORIES:
						$section = 'category';
						break;

					default:
						$section = 'tag';
				}

				$v->section = $section;
				$sections[] = $v;
			}

			echo View::factory("sledge/plugin/archive/index")
				->set('parent', $this->parent)
				->set('sections', $sections)
				->set('title',  ($this->request->post('title'))? $this->request->post('title') : 'Archive');

			// Update the cache.
			Fragment::save();
		 }
	}

	/**
	* Displays a breakdown of the current pages child pages by tag.
	* Accepts a tag name and route in the POST data.
	* The name is only used to for information in the template.
	*/
	public function action_tag()
	{
		// Build the query.
		$query = DB::select('tags.name', array(DB::expr('count("tags.id")'), 'count'))
			->from('tags')
			->join('tags_applied', 'inner')
			->on('tags_applied.tag_id', '=', 'tags.id')
			->where('tags_applied.object_type', '=',Model_Tag_Applied::OBJECT_TYPE_PAGE)
			->group_by('tags.id')
			->join('page_mptt', 'inner')
			->on('tags_applied.object_id', '=', 'page_mptt.id')
			->where('page_mptt.parent_id', '=', $this->parent->id)
			->join('pages', 'inner')
			->on('tags_applied.object_id', '=', 'pages.id')
			->join('page_versions', 'inner')
			->on('pages.' . Page::join_column($this->parent, $this->auth->get_user()), '=', 'page_versions.id')
			->where('page_versions.deleted', '=', FALSE)
			->order_by('name', 'asc');

		if ($this->request->post('route'))
		{
			// Get the tag we're looking up from the given route.
			$query->where('tags.path', 'like', $this->request->post('route') . "/%");
		}
		else
		{
			// Not filtering by a parent - show all page tags.
			$query->where('tags.type', '=', 2);
		}

		if ( ! $this->auth->logged_in())
		{
			// Only count pages which are visible and published.
			$time = time();
			$query
				->where('visible', '=', TRUE)
				->where('visible_from', '<=', $time)
				->and_where_open()
					->where('visible_to', '>=', $time)
					->or_where('visible_to', '=', 0)
				->and_where_close();
		}

		$this->response
			->headers('Content-Type', 'application/json')
			->body(json_encode(
				$query
					->execute()
					->as_array()
			));
	}
}
