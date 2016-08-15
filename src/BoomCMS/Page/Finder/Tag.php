<?php

namespace BoomCMS\Page\Finder;

use BoomCMS\Contracts\Models\Tag as TagInterface;
use BoomCMS\Foundation\Finder\Filter;
use BoomCMS\Support\Facades\Tag as TagFacade;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Filter
{
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @param array|TagInterface $tags
     */
    public function __construct($tags)
    {
        if (!is_array($tags)) {
            $tags = [$tags];
        }

        foreach ($tags as $i => $tag) {
            if (is_int($tag) || ctype_digit($tag)) {
                $tags[$i] = $tag = TagFacade::find($tag);
            }

            if (!$tag instanceof TagInterface || !$tag->getId()) {
                unset($tags[$i]);
            }
        }

        $this->tags = $tags;
    }

    public function build(Builder $query)
    {
        foreach ($this->tags as $i => $tag) {
            $alias = "tag-$i";

            $query
                ->join("pages_tags as $alias", 'pages.id', '=', "$alias.page_id")
                ->where("$alias.tag_id", '=', $tag->getId());
        }

        return $query;
    }

    public function shouldBeApplied()
    {
        return !empty($this->tags);
    }
}
