<?php

use BoomCMS\Core\Page\Page;
use BoomCMS\Support\Helpers;

return [
    'viewHelpers' => [
        'analytics' => function () {
            return Helpers::analytics();
        },
        'assetURL' => function (array $params) {
            return Helpers::assetURL($params);
        },
        'countPages' => function (array $params) {
            return Helpers::countPages($params);
        },
        'getPages' => function (array $params) {
            return Helpers::getPages($params);
        },
        'next' => function (array $params = []) {
            return Helpers::next($params);
        },
        'prev' => function (array $params = []) {
            return Helpers::prev($params);
        },
        'getTags' => function () {
            return call_user_func_array([Helpers::class, 'getTags'], func_get_args());
        },
        'getTagsInSection' => function (Page $page = null, $group = null) {
            return Helpers::getTagsInSection($page, $group);
        },
    ],
];
