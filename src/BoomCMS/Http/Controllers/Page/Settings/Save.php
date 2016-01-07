<?php

namespace BoomCMS\Http\Controllers\Page\Settings;

use BoomCMS\Events\PageSearchSettingsWereUpdated;
use BoomCMS\Events\PageWasMadeVisible;
use BoomCMS\Jobs\DeletePage;
use BoomCMS\Jobs\ReorderChildPages;
use BoomCMS\Support\Facades\Page;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;

class Save extends Settings
{
    public function admin()
    {
        parent::admin();

        $internal_name = $this->request->input('internal_name');
        $add_behaviour = $this->request->input('add_behaviour');
        $child_add_behaviour = $this->request->input('child_add_behaviour');
        $disable_delete = $this->request->input('disable_delete');

        $this->page
            ->setInternalName($internal_name)
            ->setAddPageBehaviour($add_behaviour)
            ->setChildAddPageBehaviour($child_add_behaviour);

        if (Auth::check('editDeletable', $this->page)) {
            $this->page->setDisableDelete($disable_delete == '1');
        }

        Page::save($this->page);
    }

    public function children()
    {
        parent::children();

        $post = $this->request->input();

        $this->page->setChildTemplateId($this->request->input('children_template_id'));

        if ($this->allowAdvanced) {
            $this->page
                ->setChildrenUrlPrefix($this->request->input('children_url_prefix'))
                ->setChildrenVisibleInNav($this->request->input('children_visible_in_nav') == 1)
                ->setChildrenVisibleInNavCms($this->request->input('children_visible_in_nav_cms') == 1)
                ->setGrandchildTemplateId($this->request->input('grandchild_template_id'));
        }

        if (isset($post['children_ordering_policy']) && isset($post['children_ordering_direction'])) {
            $this->page->setChildOrderingPolicy($post['children_ordering_policy'], $post['children_ordering_direction']);
        }

        Page::save($this->page);
    }

    public function delete()
    {
        $redirect = $this->page->isRoot() ? '/' : $this->page->getParent()->url();

        Bus::dispatch(new DeletePage($this->page, $this->request->input()));

        return (string) $redirect;
    }

    public function feature()
    {
        parent::feature();

        $this->page->setFeatureImageId($this->request->input('feature_image_id'));
        Page::save($this->page);
    }

    public function navigation()
    {
        parent::navigation();

        if ($this->allowAdvanced) {
            $parent = Page::find($this->request->input('parent_id'));

            if ($parent) {
                $this->page->setParent($parent);
            }
        }

        $this->page
            ->setVisibleInNav($this->request->input('visible_in_nav'))
            ->setVisibleInCmsNav($this->request->input('visible_in_nav_cms'));

        Page::save($this->page);
    }

    public function search()
    {
        parent::search();

        $this->page
            ->setDescription($this->request->input('description'))
            ->setKeywords($this->request->input('keywords'));

        if ($this->allowAdvanced) {
            $this->page
                ->setExternalIndexing($this->request->input('external_indexing'))
                ->setInternalIndexing($this->request->input('internal_indexing'));
        }

        Page::save($this->page);
        Event::fire(new PageSearchSettingsWereUpdated($this->page));
    }

    public function sort_children()
    {
        parent::children();

        Bus::dispatch(new ReorderChildPages($this->page, $this->request->input('sequences')));
    }

    public function visibility()
    {
        parent::visibility();

        $wasVisible = $this->page->isVisible();

        $this->page->setVisibleAtAnyTime($this->request->input('visible') == 1);

        if ($this->page->isVisibleAtAnyTime()) {
            $visibleTo = ($this->request->input('toggle_visible_to') == 1) ? new DateTime($this->request->input('visible_to')) : null;

            $this->page
                ->setVisibleFrom(new DateTime($this->request->input('visible_from')))
                ->setVisibleTo($visibleTo);
        }

        Page::save($this->page);

        if (!$wasVisible && $this->page->isVisible()) {
            Event::fire(new PageWasMadeVisible($this->page, Auth::user()));
        }

        return (int) $this->page->isVisible();
    }
}
