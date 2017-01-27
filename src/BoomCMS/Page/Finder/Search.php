<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Search extends Filter
{
    /**
     * @var array
     */
    protected $text;

    public function __construct($text)
    {
        $this->text = mysqli_real_escape_string(trim(strip_tags(str_replace(["\n", "\r"], '', $text))));
    }

    public function build(Builder $query)
    {
        $pageIds = DB::table('search_texts')
            ->select('page_id')
            ->join('pages', 'pages.id', '=', 'search_texts.page_id')
            ->where('internal_indexing', '=', '?')
            ->whereRaw('match(title, standfirst, text, meta) against (?)')
            ->where('embargoed_until', '<=', '?')
            ->setBindings([true, $this->text, time()])
            ->groupBy('page_id')
            ->pluck('page_id')
            ->all();

        return $query->whereIn('pages.id', $pageIds);
    }

    public function execute(Builder $query)
    {
        return $query
            ->addSelect(DB::raw("(((match(search_texts.title) against ('{$this->text}')) * 100) + ((match(search_texts.meta) against ('{$this->text}')) * 20) + ((match(search_texts.standfirst) against ('{$this->text}')) * 10) + match(search_texts.text) against ('{$this->text}')) as rel"))
            ->join('search_texts', 'pages.id', '=', 'search_texts.page_id')
            ->where('search_texts.embargoed_until', '<=', time())
            ->orderBy('rel', 'desc');
    }

    public function shouldBeApplied()
    {
        return !empty($this->text);
    }
}
