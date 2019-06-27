<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Http\Controllers\Controller;
use Illuminate\View\View;

use Illuminate\Http\Request;

class Relatedlangpages extends Controller
{
    /**
     * Initialise the class with authorization of edit page
     * 
     * @param Page $page
     */
    protected function auth(Page $page)
    {
        $this->authorize('edit', $page);
    }

    /**
     * Shows the view for setting language related pages
     * 
     * @param Page $page
     * @return View
     */
    public function index(Page $page)
    {
        $this->auth($page);
        return view('boomcms::editor.page.settings.relatedlangpages');
    }

    /**
     * Get all related language of a specified page 
     * 
     * @param Page $page
     * @return JSON 
     */
    public function getRelatedPages(Page $page)
    {
        $pages = PageFacade::getRelatedLangPages($page->getId());
        return json_encode($pages);
    }

    /**
     * Store the related page of a specified page 
     * 
     * @param Page $page
     * @param Request $request
     */
    public function store(Page $page, Request $request)
    {
        $this->auth($page);
       
        $page_id = $request->get('page_id');
        $language = $request->get('lang');
        $related_page_id = $request->get('related_page_id');

        if($page->hasRelatedLanguagePage($language, $related_page_id)) {
            return json_encode(
                [
                    'status' => false,
                    'msg' => trans('boomcms::settings.relatedlangpages.already-related')
                ]
            );
        }

        if(!$page->addRelatedLanguagePage($page_id, $language, $related_page_id)) {
            return json_encode(
                [
                    'status' => false,
                    'msg' => trans('boomcms::settings.relatedlangpages.failed-to-add')
                ]
            );
        }

        return json_encode(['status' => true]);
    }

    /**
     * Remove the related page of a specified page 
     * 
     * @param Page $page
     * @param Request $request
     */
    public function destroy(Page $page, Request $request)
    {
        $this->auth($page);
       
        $language = $request->get('lang');
        $related_page_id = $request->get('related_page_id');

        $remove = $page->remvoeRelatedLanguagePage($language, $related_page_id);

        if(!$remove) {
            return json_encode(
                [
                    'status' => false,
                    'msg' => trans('boomcms::settings.relatedlangpages.failed-to-remove'),
                ]
            );
        }

        return json_encode(['status' => true, 'lang' => $language]);
    }
}
