<?php

namespace BoomCMS\Core\Controllers\CMS;

use \Boom\Tag\Tag as Tag;

class Autocomplete extends Boom\Controller
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
    public $results = [];

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
        $this->count = ($this->request->query('count') > 0) ? $this->request->query('count') : 10;

        // The text to search for.
        $this->text = $this->request->query('text');
    }

    /**
	 * Autocomplete on asset title.
	 */
    public function assets()
    {
        // Build the query.
        $query = DB::select('title')
            ->from('assets')
            ->where('title', 'like', "%$this->text%")
            ->order_by(DB::expr('length(title)'), 'asc')
            ->limit($this->count);

        // Get the results
        $results = $query
            ->execute()
            ->as_array('title');

        // Get an array of asset titles from the results.
        $this->results = array_keys($results);
    }

    public function asset_tags()
    {
        $query = DB::select('tag')
            ->from('assets_tags')
            ->where('tag', 'like', "%$this->text%")
            ->order_by(DB::expr('length(tag)'), 'asc')
            ->distinct(true)
            ->limit($this->count);

        if ($this->request->query('ignore')) {
            $query->where('tag', 'not in', $this->request->query('ignore'));
        }

        $results = $query
            ->execute()
            ->as_array();

        $this->results = Arr::pluck($results, 'tag');
    }

    /**
	 * Suggest tag names based on an infix.
	 *
	 */
    public function page_tags()
    {
        $group = $this->request->query('group')?: null;

        // Build a query to find tags matching on path.
        $query = DB::select('tags.name', 'tags.id')
            ->from('tags')
            ->join('pages_tags', 'inner')
            ->on('tags.id', '=', "pages_tags.tag_id")
            ->where('name', 'like', "%$this->text%")
            ->where('group', '=', $group)
            ->order_by(DB::expr('length(tags.name)'), 'asc')
            ->distinct(true)
            ->limit($this->count);

        if ($this->request->query('ignore')) {
            $query->where('tags.id', 'not in', $this->request->query('ignore'));
        }

        // Get the query results.
        $results = $query
            ->execute()
            ->as_array();

        foreach ($results as &$result) {
            $result = [
                'label' => $result['name'],
                'value' => $result['id'],
            ];
        }

        // Turn the results into a flat array of tag paths and pop it in $this->results for outputting.
        $this->results = $results;
    }

    public function after()
    {
        $this->response
            ->headers('content-type', static::JSON_RESPONSE_MIME)
            ->body(json_encode($this->results));
    }
}
