<?php

use BoomCMS\Contracts\Models\Asset as AssetContract;
use BoomCMS\Http\Middleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
    'web',
    Middleware\DefineCMSViewSharedVariables::class,
]], function () {
    Route::group(['prefix' => 'boomcms', 'namespace' => 'BoomCMS\Http\Controllers'], function () {
        Route::group([
            'namespace'  => 'Auth',
            'middleware' => [Middleware\RequireGuest::class],
        ], function () {
            Route::get('login', ['as' => 'login', 'uses' => 'AuthController@showLoginForm']);

            // Password reset link request routes...
            Route::get('recover', ['as' => 'password', 'uses' => 'SendResetEmail@showLinkRequestForm']);
            Route::post('recover', 'SendResetEmail@sendResetLinkEmail');
            Route::get('recover/{token}', 'PasswordReset@showResetForm');
            Route::post('recover/{token}', 'PasswordReset@reset');
            Route::post('login', ['as' => 'processLogin', 'uses' => 'AuthController@login']);
        });

        Route::group(['middleware' => [Middleware\RequireLogin::class]], function () {
            Route::get('', ['as' => 'dashboard', 'uses' => 'Dashboard@index']);
            Route::get('logout', 'Auth\AuthController@logout');

            Route::get('autocomplete/assets', 'Autocomplete@getAssets');
            Route::get('autocomplete/page-titles', 'Autocomplete@getPageTitles');

            Route::get('editor/toolbar', 'Editor@getToolbar');
            Route::post('editor/state', ['as' => 'editor.state', 'uses' => 'Editor@postState']);
            Route::post('editor/time', 'Editor@postTime');

            Route::get('account', 'Auth\Account@getIndex');
            Route::post('account', 'Auth\Account@postIndex');

            Route::get('settings', 'Settings@getIndex');
            Route::post('settings', 'Settings@postIndex');

            Route::get('support', 'Support@getIndex');
            Route::post('support', 'Support@postIndex');
            Route::post('editor/state', 'Editor@setState');
            Route::get('editor/toolbar/{page}', 'Editor@getToolbar');

            Route::group([
                'prefix'    => 'asset',
                'namespace' => 'Asset',
            ], function () {
                Route::get('download', 'AssetSelectionController@download');
                Route::delete('', 'AssetSelectionController@destroy');
                Route::post('{asset}/replace', 'AssetController@replace');
                Route::post('{asset}/revert', 'AssetController@revert');
            });

            Route::get('asset-picker', 'Asset\AssetPickerController@index');

            Route::get('asset-manager{path}', 'Asset\AssetManagerController@index')
                ->where([
                    'path' => '(/.*)?',
                ]);

            Route::resource('asset', 'Asset\AssetController');
            Route::post('asset/create-from-blob', 'Asset\AssetController@createFromBlob');

            Route::resource('album', 'Asset\AlbumController');
            Route::resource('album/{album}/assets', 'Asset\AlbumAssetsController');
            Route::delete('album/{album}/assets', 'Asset\AlbumAssetsController@destroy');

            Route::group([
                'namespace' => 'People',
            ], function () {
                Route::get('people-manager', [
                    'uses' => 'PeopleManager@index',
                    'as'   => 'people-manager',
                ]);

                Route::group(['prefix' => 'person'], function () {
                    Route::resource('{person}/group', 'PersonGroup');
                    Route::resource('{person}/site', 'PersonSite');
                });

                Route::resource('person', 'Person');
                Route::resource('group/{group}/roles', 'GroupRole');
                Route::resource('group', 'Group');
            });

            Route::get('template-manager', [
                'uses' => 'TemplateManagerController@index',
                'as'   => 'template-manager',
            ]);

            Route::resource('template', 'TemplateController');

            Route::get('page-manager', [
                'as'   => 'page-manager',
                'uses' => 'PageManager@index',
            ]);

            Route::get('page', 'Page\PageController@getIndex');
            Route::post('page/{page}/add', 'Page\PageController@postAdd');

            Route::group(['prefix' => 'page/{page}', 'namespace' => 'Page'], function () {
                Route::post('version/template/{template}', 'Version@postTemplate');

                Route::get('version/embargo', 'Version@getEmbargo');
                Route::get('version/status', 'Version@getStatus');
                Route::get('version/template', 'Version@getTemplate');
                Route::post('version/embargo', 'Version@postEmbargo');
                Route::post('version/status', 'Version@postStatus');
                Route::post('version/template', 'Version@postTemplate');
                Route::post('version/title', 'Version@postTitle');
                Route::post('version/request-approval', 'Version@requestApproval');
                Route::post('version/restore', 'Version@postRestore');

                Route::get('settings/admin', 'Settings@getAdmin');
                Route::get('settings/children', 'Settings@getChildren');
                Route::get('settings/delete', 'Settings@getDelete');
                Route::get('settings/feature', 'Settings@getFeature');
                Route::get('settings/history', 'Settings@getHistory');
                Route::get('settings/index', 'Settings@getIndex');
                Route::get('settings/info', 'Settings@getInfo');
                Route::get('settings/navigation', 'Settings@getNavigation');
                Route::get('settings/search', 'Settings@getSearch');
                Route::get('settings/visibility', 'Settings@getVisibility');

                Route::post('settings/admin', 'Settings@postAdmin');
                Route::post('settings/children', 'Settings@postChildren');
                Route::post('settings/delete', 'Settings@postDelete');
                Route::post('settings/feature', 'Settings@postFeature');
                Route::post('settings/history', 'Settings@postHistory');
                Route::post('settings/navigation', 'Settings@postNavigation');
                Route::post('settings/search', 'Settings@postSearch');
                Route::post('settings/visibility', 'Settings@postVisibility');
                Route::post('settings/sort-children', 'Settings@postSortChildren');

                Route::get('chunk/edit', 'Chunk@getEdit');
                Route::post('chunk/save', 'Chunk@postSave');

                Route::group(['prefix' => 'urls'], function () {
                    Route::get('', 'Urls@index');
                    Route::get('create', 'Urls@create');
                    Route::post('', 'Urls@store');
                    Route::post('{url}/make-primary', 'Urls@makePrimary');
                    Route::delete('{url}', 'Urls@destroy');
                    Route::get('{url}/move', 'Urls@getMove');
                    Route::post('{url}/move', 'Urls@postMove');
                });

                Route::get('tags', 'Tags@view');
                Route::post('tags', 'Tags@add');
                Route::delete('tags/{tag}', 'Tags@remove');

                Route::get('relations', 'Relations@index');
                Route::post('relations/{related}', 'Relations@store');
                Route::delete('relations/{related}', 'Relations@destroy');

                Route::get('acl', 'Acl@index');
                Route::put('acl', 'Acl@update');
                Route::post('acl/{group}', 'Acl@store');
                Route::delete('acl/{group}', 'Acl@destroy');

                Route::get('', 'PageController@getIndex');
            });
        });
    });
});

Route::group([
    'prefix'     => 'asset',
    'middleware' => [
        'web',
        Middleware\RequireAssetVisible::class,
    ],
], function () {
    Route::get('{asset}/download{extension?}', [
        'as'         => 'asset-download',
        'middleware' => [
            Middleware\LogAssetDownload::class,
        ],
        'uses' => 'BoomCMS\Http\Controllers\Asset\AssetController@download',
    ])
    ->where([
        'extension' => '.[a-z]+',
    ]);

    Route::get('{asset}/embed', 'BoomCMS\Http\Controllers\Asset\AssetController@embed');

    Route::get('{asset}/{action}.{extension}', [
        'as'         => 'asset',
        'middleware' => [
            Middleware\CheckAssetETag::class,
        ],
        'uses' => function ($asset, $action = 'view', $width = null, $height = null) {
            App::instance(AssetContract::class, $asset);

            $has_asset = AssetHelper::controller($asset);

            if ($has_asset) {
                return App::make($has_asset)->$action($width, $height);
            }

            return '';
        },
    ])->where([
        'action'    => 'view|thumb|download|crop|embed',
        'extension' => '[a-z]+',
    ]);

    Route::get('{asset}/{action?}/{width?}/{height?}', [
        'as'         => 'asset',
        'middleware' => [
            Middleware\CheckAssetETag::class,
        ],
        'uses' => function ($asset, $action = 'view', $width = null, $height = null) {
            App::instance(AssetContract::class, $asset);

            $has_asset = AssetHelper::controller($asset);

            if ($has_asset) {
                return App::make($has_asset)->$action($width, $height);
            }

            return '';
        },
    ])->where([
        'action' => 'view|thumb|crop',
        'width'  => '\d+',
        'height' => '\d+',
    ]);
});

Route::any('{location}.{format?}', [
    'middleware' => [
        'web',
        Middleware\RoutePage::class,
        Middleware\CheckPageAcl::class,
        Middleware\InsertCMSToolbar::class,
    ],
    'uses' => 'BoomCMS\Http\Controllers\PageController@show',
])->where([
    'location' => '(.*?)',
    'format'   => '([a-z]+)',
]);
