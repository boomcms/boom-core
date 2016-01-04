<?php

use BoomCMS\Http\Middleware;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
    Middleware\DisableHttpCacheIfLoggedIn::class,
    Middleware\DefineCMSViewSharedVariables::class,
]], function () {
    Route::group(['prefix' => 'boomcms', 'namespace' => 'BoomCMS\Http\Controllers'], function () {
        Route::group(['namespace' => 'Auth'], function () {
            Route::get('login', ['as' => 'login', 'uses' => 'AuthController@getLogin']);
            Route::post('login', 'AuthController@postLogin');
            Route::get('logout', 'AuthController@getLogout');

            // Password reset link request routes...
            Route::get('recover', ['as' => 'password', 'uses' => 'PasswordReset@getEmail']);
            Route::post('recover', 'PasswordReset@postEmail');
            Route::get('recover/{token}', 'PasswordReset@getReset');
            Route::post('recover/{token}', 'PasswordReset@postReset');
        });

        Route::group(['middleware' => [Middleware\RequireLogin::class]], function () {
            Route::controller('autocomplete', 'Autocomplete');
            Route::controller('ui', 'UI');
            Route::controller('editor', 'Editor');
            Route::controller('account', 'Auth\Account');
            Route::controller('approvals', 'Approvals');
            Route::controller('settings', 'Settings');
            Route::controller('search', 'Search');

            Route::group([
                'prefix'    => 'assets',
                'namespace' => 'Assets',
            ], function () {
                Route::get('', 'AssetManager@index');
                Route::post('get', 'AssetManager@get');
                Route::any('{action}', function ($action = 'index') {
                    return App::make('BoomCMS\Http\Controllers\Assets\AssetManager')->$action();
                });

                Route::get('view/{asset}', 'AssetManager@view');
                Route::post('save/{asset}', 'AssetManager@save');
                Route::post('replace/{asset}', 'AssetManager@replace');
                Route::post('revert/{asset}', 'AssetManager@revert');
                Route::post('tags/add', 'Tags@add');
                Route::post('tags/remove', 'Tags@remove');
                Route::get('tags/list/{assets}', 'Tags@listTags');
            });

            Route::group(['namespace' => 'People', 'middleware' => [Middleware\PeopleManager::class]], function () {
                Route::get('people', 'PeopleManager@index');

                Route::get('person/add', 'Person\ViewPerson@add');
                Route::post('person/add', 'Person\SavePerson@add');
                Route::get('person/view/{person}', 'Person\ViewPerson@view');
                Route::post('person/save/{person}', 'Person\SavePerson@save');
                Route::post('person/delete', 'Person\SavePerson@delete');
                Route::get('person/add_group/{person}', 'Person\ViewPerson@addGroup');
                Route::post('person/add_group/{person}', 'Person\SavePerson@addGroup');
                Route::get('person/remove_group/{person}', 'Person\ViewPerson@removeGroup');
                Route::post('person/remove_group/{person}', 'Person\SavePerson@removeGroup');
            });

            Route::group([
                'namespace'  => 'Group',
                'middleware' => [Middleware\PeopleManager::class],
            ], function () {
                Route::get('group/add', 'View@add');
                Route::post('group/add', 'Save@add');
                Route::get('group/list_roles/{group}', 'View@listRoles');
                Route::post('group/remove_role/{group}', 'Save@removeRole');
                Route::post('group/add_role/{group}', 'Save@addRole');
                Route::post('group/delete/{group}', 'Save@delete');
                Route::post('group/save/{group}', 'Save@save');

                Route::get('group/edit/{group}', [
                    'as'   => 'group-edit',
                    'uses' => 'View@edit',
                ]);
            });

            Route::group(['prefix' => 'templates'], function () {
                Route::get('', 'Templates@index');
                Route::get('pages/{template}.{format?}', 'Templates@pages');

                Route::post('save', 'Templates@save');
                Route::post('delete/{template}', 'Templates@delete');
            });

            Route::group(['prefix' => 'pages'], function () {
                Route::get('', 'Pages@index');
            });

            Route::controller('chunk/{page}', 'Chunk');

            Route::group(['prefix' => 'page', 'namespace' => 'Page'], function () {
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
            Route::get('page/{page}/urls', 'Page\PageController@urls');
            Route::get('page/{page}/urls/add', 'Page\Urls@getAdd');
            Route::get('page/{page}/urls/{url}/move', 'Page\Urls@getMove');
            Route::post('page/{page}/urls/add', 'Page\Urls@postAdd');
            Route::post('page/{page}/urls/{url}/make_primary', 'Page\Urls@postMakePrimary');
            Route::post('page/{page}/urls/{url}/move', 'Page\Urls@postMove');
            Route::post('page/{page}/urls/{url}/delete/', 'Page\Urls@postDelete');

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
                        return App::make('BoomCMS\Http\Controllers\Page\Settings\View')->$action();
                    },
                ]);

                Route::post('{action}/{page}', [
                    'uses' => function ($action, $page) {
                        return App::make('BoomCMS\Http\Controllers\Page\Settings\Save')->$action($page);
                    },
                ]);
            });
        });
    });

    Route::get('page/children', 'BoomCMS\Http\Controllers\PageController@children');

    Route::get('asset/version/{id}/{width?}/{height?}', [
        'as'         => 'asset-version',
        'middleware' => [Middleware\RequireLogin::class],
        'uses'       => function (Auth $auth, $id, $width = null, $height = null) {
            $asset = Asset::findByVersionId($id);

            return App::make(AssetHelper::controller($asset), [$auth, $asset])->view($width, $height);
        },
    ]);

    Route::get('asset/{asset}/download', [
        'asset'      => 'asset-download',
        'middleware' => [
            Middleware\LogAssetDownload::class,
        ],
        'uses' => function (Auth $auth, $asset) {
            return App::make(AssetHelper::controller($asset), [$auth, $asset])->download();
        },
    ]);

    Route::get('asset/{asset}/{action}.{extension}', [
        'as'         => 'asset',
        'middleware' => [
            Middleware\CheckAssetETag::class,
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
            Middleware\CheckAssetETag::class,
        ],
        'uses' => function (Auth $auth, $asset, $action = 'view', $width = null, $height = null) {
            return App::make(AssetHelper::controller($asset), [$auth, $asset])->$action($width, $height);
        },
    ]);
});

Route::any('{location}.{format?}', [
    'middleware' => [
        Middleware\ProcessSiteURL::class,
        Middleware\InsertCMSToolbar::class,
    ],
    'uses' => 'BoomCMS\Http\Controllers\PageController@show',
])->where([
    'location' => '(.*?)',
    'format'   => '([a-z]+)',
]);
