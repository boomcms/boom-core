<?php

namespace BoomCMS\Http\Controllers;

use BoomCMS\Database\Models\Page;
use Illuminate\Http\Request;

class Autocomplete extends Controller
{
    public function getPageTitles(Request $request)
    {
        $count = ($request->input('count') > 0) ? $request->input('count') : 10;
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
}
