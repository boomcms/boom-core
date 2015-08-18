<?php

namespace BoomCMS\Core\Page\Finder;

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
        $this->text = addslashes(trim(strip_tags($text)));
    }

    public function build(Builder $query)
    {
        return $query
            ->where('internal_indexing', '=', true)
            ->leftJoin('chunk_texts as standfirst_table', function ($join) {
                $join
                    ->on('version.id', '=', 'standfirst_table.page_vid')
                    ->on('standfirst_table.slotname', '=', DB::raw('"standfirst"'));
            })
            ->leftJoin('chunk_texts', function ($join) {
                $join
                    ->on('version.id', '=', 'chunk_texts.page_vid')
                    ->on('chunk_texts.slotname', '!=', DB::raw('"standfirst"'));
            })
            ->whereRaw(DB::raw(
                "((((MATCH(title) against ('{$this->text}')) * 1000) > 0) OR ".
                "(((MATCH(standfirst_table.text) against ('{$this->text}')) * 50) > 0) OR ".
                "((MATCH(chunk_texts.text) against ('{$this->text}')) > 0))"
            ));
    }

    public function execute(Builder $query)
    {
        return $query
            ->addSelect(DB::raw("((MATCH(title) against ('{$this->text}')) * 1000) as rel1"))
            ->addSelect(DB::raw("((MATCH(standfirst_table.text) against ('{$this->text}')) * 50) as rel2"))
            ->addSelect(DB::raw("(MATCH(chunk_texts.text) against ('{$this->text}')) as rel3"))
            ->having(DB::raw('rel1 + rel2 + rel3'), '>', 0)
            ->orderBy(DB::raw('rel1 + rel2 + rel3'), 'desc');
    }

    public function shouldBeApplied()
    {
        return !empty($this->text);
    }
}
