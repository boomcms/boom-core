<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Support\Helpers;

class Search extends Controller
{
    public function getPages()
    {
        $results = [];
        $pages = Helpers::getPages($this->request->input());

        return $pages;

        foreach ($pages as $p) {
            $results[] = [
                'id'           => $p->getId(),
                'title'        => $p->getTitle(),
                'url'          => (string) $p->url(),
                'visible'      => (int) $p->isVisible(),
                'has_children' => (int) $p->hasChildren(),
                '',
            ];
        }

        return $results;
    }
}
