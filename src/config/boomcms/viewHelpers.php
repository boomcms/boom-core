<?php

use BoomCMS\Core\Page;
use BoomCMS\Core\Tag;

return [
    'viewHelpers' => [
        'assetURL' => function(array $params) {	
			if (isset($params['asset']) && is_object($params['asset'])) {
				$params['asset'] = $params['asset']->getId();
			}

            if ( !isset($params['action'])) {
                $params['action'] = 'view';
            }

            if (isset($params['height']) && !isset($params['width'])) {
                $params['width'] = 0;
            }

            return route('asset', $params);
        },
		'countPages' => function(array $params) {
			return (new Page\Query($params))->countPages();
		},
        'getPages' => function(array $params) {
            return (new Page\Query($params))->getPages();
        },
        'next' => function(array $params = []) {
            return (new Page\Query($params))->getNextTo(Editor::getActivePage(), 'after');
        },
        'prev' => function(array $params = []) {
            return (new Page\Query($params))->getNextTo(Editor::getActivePage(), 'before');
        },
		'getTags' => function(Page\Page $page = null, $group = null) {
			$page = $page?: Editor::getActivePage();

			$finder = new Tag\Finder\Finder();
			$finder->addFilter(new Tag\Finder\AppliedToPage($page));
			$finder->addFilter(new Tag\Finder\Group($group));

			return $finder->setOrderBy('name', 'asc')->findAll();
		},
		'getTagsInSection' => function(Page\Page $page = null, $group = null) {
			$page = $page?: Editor::getActivePage();

			$finder = new Tag\Finder\Finder();
			$finder->addFilter(new Tag\Finder\AppliedToPageDescendants($page));
			$finder->addFilter(new Tag\Finder\Group($group));

			return $finder->setOrderBy('name', 'asc')->findAll();
		},
    ],
];