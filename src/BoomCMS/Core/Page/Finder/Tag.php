<?php

namespace BoomCMS\Core\Page\Finder;

use BoomCMS\Core\Tag\Tag as TagObject;
use BoomCMS\Foundation\Finder\Filter;
use Illuminate\Database\Eloquent\Builder;

class Tag extends Filter
{
    /**
     * @var array
     */
    protected $tags = [];

    /**
     * @param array|TagObject $tags
     */
    public function __construct($tags)
    {
        if (is_array($tags)) {
            foreach ($tags as $i => $tag) {
                if (!$tag instanceof TagObject || !$tag->loaded()) {
                    unset($tags[$i]);
                }
            }

            $this->tags = $tags;
        } elseif ($tags instanceof TagObject && $tags->loaded()) {
            $this->tags = [$tags];
        }
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
