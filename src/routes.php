<?php

use BoomCMS\Core\Auth\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
    'BoomCMS\Http\Middleware\DisableHttpCacheIfLoggedIn',
    'BoomCMS\Http\Middleware\DefineCMSViewSharedVariables',
]], function () {
    Route::group(['prefix' => 'cms', 'namespace' => 'BoomCMS\Http\Controllers\CMS'], function () {
        Route::get('logout', 'Auth\Logout@index');

        Route::group(['namespace' => 'Auth', 'middleware' => ['BoomCMS\Http\Middleware\RedirectIfAuthenticated']], function () {
            Route::get('login', [
                'as'   => 'login',
                'uses' => 'Login@showLoginForm',
            ]);

            Route::post('login', 'Login@processLogin');

            Route::get('recover', 'Recover@showForm');
            Route::post('recover', 'Recover@createToken');
            Route::any('recover/set-password', 'Recover@setPassword');
        });

        Route::group(['middleware' => ['BoomCMS\Http\Middleware\RequireLogin']], function () {
            Route::controller('autocomplete', 'Autocomplete');
            Route::controller('ui', 'UI');
            Route::controller('editor', 'Editor');
            Route::controller('account', 'Auth\Account');
            Route::controller('approvals', 'Approvals');
            Route::controller('settings', 'Settings');

            Route::group([
                'middleware' => ['BoomCMS\Http\Middleware\SaveUrlForRedirect'],
            ], function () {
                Route::group([
                    'prefix'    => 'assets',
                    'namespace' => 'Assets',
                ], function () {
                    Route::get('', 'AssetManager@index');
                    Route::post('get', 'AssetManager@get');
                    Route::any('{action}', function ($action = 'index') {
                        return App::make('BoomCMS\Http\Controllers\CMS\Assets\AssetManager')->$action();
                    });

                    Route::get('view/{asset}', 'AssetManager@view');
                    Route::post('save/{asset}', 'AssetManager@save');
                    Route::post('replace/{asset}', 'AssetManager@replace');
                    Route::post('revert/{asset}', 'AssetManager@revert');
                    Route::post('tags/add', 'Tags@add');
                    Route::post('tags/remove', 'Tags@remove');
                    Route::get('tags/list/{assets}', 'Tags@listTags');
                });

                Route::group(['namespace' => 'People', 'middleware' => ['BoomCMS\Http\Middleware\PeopleManager']], function () {
                    Route::get('people', 'PeopleManager@index');

                    Route::get('person/add', 'Person\ViewPerson@add');
                    Route::post('person/add', 'Person\SavePerson@add');
                    Route::get('person/view/{id}', 'Person\ViewPerson@view');
                    Route::post('person/save/{id}', 'Person\SavePerson@save');
                    Route::post('person/delete', 'Person\SavePerson@delete');
                    Route::get('person/add_group/{id}', 'Person\ViewPerson@addGroup');
                    Route::post('person/add_group/{id}', 'Person\SavePerson@addGroup');
                    Route::get('person/remove_group/{id}', 'Person\ViewPerson@removeGroup');
                    Route::post('person/remove_group/{id}', 'Person\SavePerson@removeGroup');
                });

                Route::group([
                    'namespace'  => 'Group',
                    'middleware' => ['BoomCMS\Http\Middleware\PeopleManager'],
                ], function () {
                    Route::get('group/add', 'View@add');
                    Route::post('group/add', 'Save@add');
                    Route::get('group/list_roles/{id}', 'View@listRoles');
                    Route::post('group/remove_role/{id}', 'Save@removeRole');
                    Route::post('group/add_role/{id}', 'Save@addRole');
                    Route::post('group/delete/{id}', 'Save@delete');
                    Route::post('group/save/{id}', 'Save@save');

                    Route::get('group/edit/{id}', [
                        'as'   => 'group-edit',
                        'uses' => 'View@edit',
                    ]);
                });

                Route::group(['prefix' => 'templates'], function () {
                    Route::get('', 'Templates@index');
                    Route::get('pages/{id}.{format?}', 'Templates@pages');

                    Route::post('save', 'Templates@save');
                    Route::post('delete/{id}', 'Templates@delete');
                });

                Route::group(['prefix' => 'pages'], function () {
                    Route::get('', 'Pages@index');
                });
            });

            Route::controller('chunk/{page}', 'Chunk');

            Route::group(['prefix' => 'page', 'namespace' => 'Page'], function () {
                Route::get('delete/{page}', 'Delete@confirm');
                Route::post('delete/{page}', 'Delete@delete');
                Route::post('discard/{page}', 'PageController@discard');

                Route::group(['prefix' => 'version', 'namespace' => 'Version'], function () {
                    Route::get('template/{page}', 'View@template');
                    Route::post('template/{page}', 'Save@template');
                    Route::post('title/{page}', 'Save@title');
                    Route::get('embargo/{page}', 'View@embargo');
                    Route::post('embargo/{page}', 'Save@embargo');
                    Route::get('status/{page}', 'View@status');
                });
            });

            Route::post('page/add/{page}', 'Page\PageController@add');
            Route::get('page/urls/{page}', 'Page\PageController@urls');
            Route::get('page/urls/add', 'Page\Urls\View@add');
            Route::get('page/urls/move/{id}', 'Page\Urls\View@move');
            Route::post('page/urls/add', 'Page\Urls\Save@add');
            Route::post('page/urls/make_primary/{id}', 'Page\Urls\Save@makePrimary');
            Route::post('page/urls/move/{id}', 'Page\Urls\Save@move');
            Route::post('page/urls/delete/{id}', 'Page\Urls\Save@delete');

            Route::group(['prefix' => 'page/tags'], function () {
                Route::get('list/{page}', 'Page\Tags@listTags');
                Route::post('add/{page}', 'Page\Tags@add');
                Route::post('remove/{page}', 'Page\Tags@remove');
            });

            Route::post('page/relations/add/{page}', 'Page\Relations@add');
            Route::post('page/relations/remove/{page}', 'Page\Relations@remove');
            Route::get('page/relations/view/{page}', 'Page\Relations@view');

            Route::group(['prefix' => 'page/settings'], function () {
                Route::get('{action}/{page}', [
                    'uses' => function ($action) {
                        return App::make('BoomCMS\Http\Controllers\CMS\Page\Settings\View')->$action();
                    },
                ]);

                Route::post('{action}/{page}', [
                    'uses' => function ($action, $page) {
                        return App::make('BoomCMS\Http\Controllers\CMS\Page\Settings\Save')->$action($page);
                    },
                ]);
            });
        });
    });

    Route::get('page/children', 'BoomCMS\Http\Controllers\PageController@children');

    Route::get('asset/version/{id}/{width?}/{height?}', [
        'as'         => 'asset-version',
        'middleware' => ['BoomCMS\Http\Middleware\RequireLogin'],
        'uses'       => function (Auth $auth, $id, $width = null, $height = null) {
            $asset = Asset::findByVersionId($id);

            return App::make(AssetHelper::controller($asset), [$auth, $asset])->view($width, $height);
        },
    ]);

    Route::get('asset/{asset}/download', [
        'asset'      => 'asset-download',
        'middleware' => [
            'BoomCMS\Http\Middleware\LogAssetDownload',
        ],
        'uses' => function (Auth $auth, $asset) {
            return App::make(AssetHelper::controller($asset), [$auth, $asset])->download();
        },
    ]);

    Route::get('asset/{asset}/{action}.{extension}', [
        'as'         => 'asset',
        'middleware' => [
            'BoomCMS\Http\Middleware\CheckAssetETag',
        ],
        'uses' => function (Auth $auth, $asset, $action = 'view', $width = null, $height = null) {
            return App::make(AssetHelper::controller($asset), [$auth, $asset])->$action($width, $height);
        },
    ])->where([
        'action'    => '[a-z]+',
        'extension' => '[a-z]+',
    ]);

    Route::get('asset/{asset}/{action?}/{width?}/{height?}', [
        'as'         => 'asset',
        'middleware' => [
            'BoomCMS\Http\Middleware\CheckAssetETag',
        ],
        'uses' => function (Auth $auth, $asset, $action = 'view', $width = null, $height = null) {
            return App::make(AssetHelper::controller($asset), [$auth, $asset])->$action($width, $height);
        },
    ]);
});

Route::any('{location}.{format?}', [
    'middleware' => [
        'BoomCMS\Http\Middleware\ProcessSiteURL',
        'BoomCMS\Http\Middleware\InsertCMSToolbar',
        'BoomCMS\Http\Middleware\SaveUrlForRedirect',
    ],
    'uses' => 'BoomCMS\Http\Controllers\PageController@show',
])->where([
    'location' => '(.*?)',
    'format'   => '([a-z]+)',
]);
