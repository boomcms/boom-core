<?php

namespace BoomCMS\Http\Controllers\Page;

use BoomCMS\Database\Models\Page;
use BoomCMS\Events;
use BoomCMS\Jobs\DeletePage;
use BoomCMS\Jobs\ReorderChildPages;
use BoomCMS\Support\Facades\Page as PageFacade;
use BoomCMS\Support\Facades\Template as TemplateFacade;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class Settings extends PageController
{
    /**
     * @var string
     */
    protected $viewPrefix = 'boomcms::editor.page.settings';

    /**
     * View the admin settings interface.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getAdmin(Page $page)
    {
        $this->authorize('editAdmin', $page);

        return view("$this->viewPrefix.admin", [
            'page' => $page,
        ]);
    }

    public function getChildren(Page $page)
    {
        $this->authorize('editChildrenBasic', $page);

        list($orderCol, $orderDirection) = $page->getChildOrderingPolicy();
        $templates = TemplateFacade::findValid();

        return view("$this->viewPrefix.children", [
            'default_child_template'      => $page->getDefaultChildTemplateId(),
            'default_grandchild_template' => $page->getDefaultGrandchildTemplateId(),
            'templates'                   => $templates,
            'child_order_column'          => $orderCol,
            'child_order_direction'       => $orderDirection,
            'page'                        => $page,
        ]);
    }

    /**
     * Show the delete page confirmation.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getDelete(Page $page)
    {
        $this->authorize('delete', $page);

        return view($this->viewPrefix.'.delete', [
            'children' => $page->countChildren(),
            'page'     => $page,
        ]);
    }

    /**
     * Show the feature image settings.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getFeature(Page $page)
    {
        $this->authorize('editFeature', $page);

        return view("$this->viewPrefix.feature", [
            'featureImageId' => $page->getFeatureImageId(),
        ]);
    }

    /**
     * Show the page settings menu.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getIndex(Page $page)
    {
        return view("$this->viewPrefix.index", [
            'page' => $page,
        ]);
    }

    /**
     * Show the page navigation settings.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getNavigation(Page $page)
    {
        $this->authorize('editNavBasic', $page);

        return view("$this->viewPrefix.navigation", [
            'page' => $page,
        ]);
    }

    public function getSearch(Page $page)
    {
        $this->authorize('editSearchBasic', $page);

        return view("$this->viewPrefix.search", [
            'page' => $page,
        ]);
    }

    /**
     * Show the page visibility settings.
     *
     * @param Page $page
     *
     * @return View
     */
    public function getVisibility(Page $page)
    {
        $this->authorize('edit', $page);

        return view("$this->viewPrefix.visibility", [
            'page'        => $page,
        ]);
    }

    /**
     * Save the page admin settings.
     *
     * @param Request $request
     * @param Page    $page
     */
    public function postAdmin(Request $request, Page $page)
    {
        $this->authorize('editAdmin', $page);

        $page
            ->setInternalName($request->input('internal_name'))
            ->setAddPageBehaviour($request->input('add_behaviour'))
            ->setChildAddPageBehaviour($request->input('child_add_behaviour'))
            ->setDisableDelete($request->has('disable_delete'));

        PageFacade::save($page);
    }

    /**
     * Save the child page settings.
     *
     * @param Request $request
     * @param Page    $page
     */
    public function postChildren(Request $request, Page $page)
    {
        $this->authorize('editChildrenBasic', $page);

        $post = $request->input();

        $page->setChildTemplateId($request->input('children_template_id'));

        if (Gate::allows('editChildrenAdvanced', $page)) {
            $page
                ->setChildrenUrlPrefix($request->input('children_url_prefix'))
                ->setChildrenVisibleInNav($request->has('children_visible_in_nav'))
                ->setChildrenVisibleInNavCms($request->has('children_visible_in_nav_cms'))
                ->setGrandchildTemplateId($request->input('grandchild_template_id'));
        }

        if (isset($post['children_ordering_policy']) && isset($post['children_ordering_direction'])) {
            $page->setChildOrderingPolicy($post['children_ordering_policy'], $post['children_ordering_direction']);
        }

        PageFacade::save($page);
    }

    public function postDelete(Request $request, Page $page)
    {
        $this->authorize('delete', $page);

        $redirect = $page->isRoot() ? '/' : (string) $page->getParent()->url();

        Bus::dispatch(new DeletePage($page, $request->input()));

        return $redirect;
    }

    /**
     * Save the page feature image.
     *
     * @param Request $request
     * @param Page    $page
     */
    public function postFeature(Request $request, Page $page)
    {
        $this->authorize('editFeature', $page);

        $page->setFeatureImageId($request->input('feature_image_id'));

        PageFacade::save($page);
    }

    /**
     * Save the page navigation settings.
     *
     * @param Request $request
     * @param Page    $page
     */
    public function postNavigation(Request $request, Page $page)
    {
        $this->authorize('editNavigationBasic', $page);

        if (Gate::allows('editNavigationAdvanced', $page)) {
            $parent = Page::find($request->input('parent_id'));

            if ($parent) {
                $page->setParent($parent);
            }
        }

        $page
            ->setVisibleInNav($request->has('visible_in_nav'))
            ->setVisibleInCmsNav($request->has('visible_in_nav_cms'));

        PageFacade::save($page);
    }

    public function postSearch(Request $request, Page $page)
    {
        $this->authorize('editSearchBasic', $page);

        $page
            ->setDescription($request->input('description'))
            ->setKeywords($request->input('keywords'));

        if (Gate::allows('editSearchAdvanced', $page)) {
            $page
                ->setExternalIndexing($request->has('external_indexing'))
                ->setInternalIndexing($request->has('internal_indexing'));
        }

        PageFacade::save($page);

        Event::fire(new Events\PageSearchSettingsWereUpdated($page));
    }

    /**
     * Save the order of child pages.
     *
     * @param Request $request
     * @param Page    $page
     */
    public function postSortChildren(Request $request, Page $page)
    {
        $this->authorize('editChildrenBasic', $page);

        Bus::dispatch(new ReorderChildPages($page, $request->input('sequences')));
    }

    /**
     * Save the page visibility settings.
     *
     * @param Request $request
     * @param Page    $page
     *
     * @return int
     */
    public function postVisibility(Request $request, Page $page)
    {
        $this->authorize('editContent', $page);

        $wasVisible = $page->isVisible();

        $visibleFrom = $request->input('visible_from');
        $visibleFrom = $visibleFrom ? new DateTime($visibleFrom) : null;

        $visibleTo = ($request->has('toggle_visible_to')) ?
            new DateTime('@'.$request->input('visible_to'))
            : null;

        $page
            ->setVisibleFrom($visibleFrom)
            ->setVisibleTo($visibleTo);

        PageFacade::save($page);

        if (!$wasVisible && $page->isVisible()) {
            Event::fire(new Events\PageWasMadeVisible($page, auth()->user()));
        }

        return (int) $page->isVisible();
    }
}
