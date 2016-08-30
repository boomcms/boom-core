<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Autocomplete extends Controller
{
    /**
     * The number of results to be returned. Default is 10.
     *
     * @var int
     */
    public $count;

    /**
     * Array of matches to be output.
     *
     * @var array
     */
    public $results = [];

    /**
     * The text to search for.
     *
     * @var string
     */
    public $text;

    public function __construct(Request $request)
    {
        $this->count = ($request->input('count') > 0) ? $request->input('count') : 10;
        $this->text = $request->input('text');
        $this->request = $request;
    }

    public function getAssets()
    {
        $query = DB::table('assets')
            ->select('title')
            ->where('title', 'like', "%$this->text%")
            ->orderBy(DB::raw('length(title)'), 'asc')
            ->distinct(true)
            ->limit($this->count);

        return $query->lists('title');
    }

    public function getPageTitles()
    {
        $results = [];
        $pages = Page::autocompleteTitle($this->request->input('text'), $this->count)->get();

        foreach ($pages as $p) {
            $primaryUri = substr($p->primary_uri, 0, 1) === '/' ? $p->primary_uri : '/'.$p->primary_uri;

            $results[] = [
                'label' => $p->title.' ('.$primaryUri.')',
                'value' => $primaryUri,
            ];
        }

        return $results;
    }

    public function getPageTags()
    {
        $group = $this->request->query('group') ?: null;

        // Build a query to find tags matching on path.
        $query = DB::table('tags')
            ->select('tags.name', 'tags.id')
            ->join('pages_tags', 'tags.id', '=', 'pages_tags.tag_id')
            ->where('name', 'like', "%{$this->text}%")
            ->where('group', '=', $group)
            ->orderBy(DB::raw('length(tags.name)'), 'asc')
            ->distinct(true)
            ->limit($this->count);

        if ($this->request->query('ignore')) {
            $query->whereNotIn('tags.id', $this->request->query('ignore'));
        }

        // Get the query results.
        $results = $query->get();

        foreach ($results as &$result) {
            $result = [
                'label' => $result->name,
                'value' => $result->id,
            ];
        }

        return $results;
    }
}
