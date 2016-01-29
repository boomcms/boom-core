<?php

use BoomCMS\Contracts\Models\Page;
use BoomCMS\Support\Helpers;

return [
    'viewHelpers' => [
        'analytics' => function () {
            return Helpers::analytics();
        },
        'assetEmbed' => function ($asset, $height = null, $width = null) {
            return Helpers::assetEmbed($asset, $height, $width);
        },
        'assetURL' => function (array $params) {
            return Helpers::assetURL($params);
        },
        'chunk' => function () {
            return call_user_func_array([Helpers::class, 'chunk'], func_get_args());
        },
        'countAssets' => function (array $params) {
            return Helpers::countAssets($params);
        },
        'countPages' => function (array $params) {
            return Helpers::countPages($params);
        },
        'getAssets' => function (array $params) {
            return Helpers::getAssets($params);
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
        'pub' => function () {
            return call_user_func_array([Helpers::class, 'pub'], func_get_args());
        },
        'view' => function () {
            return call_user_func_array([Helpers::class, 'view'], func_get_args());
        },
    ],
];
