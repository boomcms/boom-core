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
            Route::post('editor/state', 'Editor@setState');
            Route::get('editor/toolbar/{page}', 'Editor@getToolbar');

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

            Route::group([
                'namespace'  => 'People',
                'middleware' => [Middleware\PeopleManager::class],
            ], function () {
                Route::get('people', 'PeopleManager@index');

                Route::delete('person', 'Person@destroy');
                Route::get('person/{person}/groups', 'Person@availableGroups');
                Route::delete('person/{person}/groups/{group}', 'Person@removeGroup');
                Route::post('person/{person}/groups', 'Person@addGroups');
                Route::resource('person', 'Person');

                Route::get('group/{group}/roles', 'Group@roles');
                Route::delete('group/{group}/roles', 'Group@removeRole');
                Route::put('group/{group}/roles', 'Group@addRole');
                Route::resource('group', 'Group');
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

            Route::group(['namespace' => 'Sites'], function () {
                Route::get('sites', 'SiteManager@index');
                Route::resource('site', 'Site');
            });

            Route::group(['prefix' => 'page/{page}', 'namespace' => 'Page'], function () {
                Route::post('version/template/{template}', 'Version@postTemplate');
                Route::controller('version', 'Version');
                Route::controller('settings', 'Settings');
                Route::controller('chunk', 'Chunk');

                Route::group(['prefix' => 'urls'], function () {
                    Route::get('', 'Urls@index');
                    Route::get('create', 'Urls@create');
                    Route::post('', 'Urls@store');
                    Route::post('{url}/make-primary', 'Urls@makePrimary');
                    Route::delete('{url}', 'Urls@destroy');
                    Route::get('{url}/move', 'Urls@getMove');
                    Route::post('{url}/move', 'Urls@postMove');
                    Route::controller('', 'Urls');
                });

                Route::get('tags', 'Tags@view');
                Route::post('tags', 'Tags@add');
                Route::delete('tags/{tag}', 'Tags@remove');

                Route::get('relations', 'Relations@index');
                Route::post('relations/{related}', 'Relations@store');
                Route::delete('relations/{related}', 'Relations@destroy');

                Route::controller('', 'PageController');
            });
        });
    });

    Route::get('asset/version/{id}/{width?}/{height?}', [
        'as'         => 'asset-version',
        'middleware' => [Middleware\RequireLogin::class],
        'uses'       => function ($id, $width = null, $height = null) {
            $asset = Asset::findByVersionId($id);

            return App::make(AssetHelper::controller($asset), [$asset])->view($width, $height);
        },
    ]);

    Route::get('asset/{asset}/download', [
        'asset'      => 'asset-download',
        'middleware' => [
            Middleware\LogAssetDownload::class,
        ],
        'uses' => function ($asset) {
            return App::make(AssetHelper::controller($asset), [$asset])->download();
        },
    ]);

    Route::get('asset/{asset}/{action}.{extension}', [
        'as'         => 'asset',
        'middleware' => [
            Middleware\CheckAssetETag::class,
        ],
        'uses' => function ($asset, $action = 'view', $width = null, $height = null) {
            return App::make(AssetHelper::controller($asset), [$asset])->$action($width, $height);
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
        'uses' => function ($asset, $action = 'view', $width = null, $height = null) {
            return App::make(AssetHelper::controller($asset), [$asset])->$action($width, $height);
        },
    ]);
});

Route::any('{location}.{format?}', [
    'middleware' => [
        Middleware\RoutePage::class,
        Middleware\InsertCMSToolbar::class,
    ],
    'uses' => 'BoomCMS\Http\Controllers\PageController@show',
])->where([
    'location' => '(.*?)',
    'format'   => '([a-z]+)',
]);
