<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
    'BoomCMS\Core\Http\Middleware\DisableHttpCacheIfLoggedIn',
    'BoomCMS\Core\Http\Middleware\DefineCMSViewSharedVariables'
]], function () {
    Route::group(['prefix' => 'cms', 'namespace' => 'BoomCMS\Core\Controllers\CMS'], function () {
        Route::get('logout', 'Auth\Logout@index');

        Route::group(['namespace' => 'Auth', 'middleware' => ['BoomCMS\Core\Http\Middleware\RedirectIfAuthenticated']], function () {
            Route::get('login', [
                'as' => 'login',
                'uses' => 'Login@showLoginForm'
            ]);

            Route::post('login', 'Login@processLogin');

            Route::get('recover', 'Recover@showForm');
            Route::post('recover', 'Recover@createToken');
            Route::any('recover/set-password', 'Recover@setPassword');
        });

        Route::group(['middleware' => ['BoomCMS\Core\Http\Middleware\RequireLogin']], function () {
            Route::get('autocomplete/assets', 'Autocomplete@assets');
            Route::get('autocomplete/asset_tags', 'Autocomplete@asset_tags');
			Route::get('autocomplete/page_tags', 'Autocomplete@pageTags');

            Route::get('editor/toolbar', 'Editor@toolbar');
            Route::post('editor/state', 'Editor@state');

            Route::get('profile', 'Auth\Profile@index');
            Route::post('profile', 'Auth\Profile@save');

            Route::group(['prefix' => 'assets', 'namespace' => 'Assets'], function () {
                Route::get('', 'AssetManager@index');
                Route::post('get', 'AssetManager@get');
                Route::any('{action}', function($action = 'index') {
                    return App::make('BoomCMS\Core\Controllers\CMS\Assets\AssetManager')->$action();
                });

                Route::get('view/{asset}', 'AssetManager@view');
                Route::get('tags/list/{assets}', 'Tags@listTags');
                Route::post('save/{asset}', 'AssetManager@save');
                Route::post('tags/add', 'Tags@add');
                Route::post('tags/remove', 'Tags@remove');
            });

            Route::group(['namespace' => 'People', 'middleware' => ['BoomCMS\Core\Http\Middleware\PeopleManager']], function() {
                Route::get('people', 'PeopleManager@index');

                Route::get('person/add', 'Person\ViewPerson@add');
                Route::post('person/add', 'Person\SavePerson@add');
            });

            Route::get('person/{id}/{action?}', [
                'as' => 'person',
               // 'middleware' => ['BoomCMS\Core\Http\Middleware\GetPerson'],
                'uses' => function($action = 'view') {
                    return App::make('BoomCMS\Core\Controllers\CMS\People\Person\View')->$action();
                }
            ]);
        });

        Route::group(['prefix' => 'templates'], function() {
            Route::get('', 'Templates@index');
            Route::get('pages/{id}', 'Templates@pages');

            Route::post('save', 'Templates@save');
            Route::post('delete/{id}', 'Templates@delete');
        });

        Route::post('page/add/{page}', 'Page\PageController@add');
        Route::get('page/urls/{page}', 'Page\PageController@urls');
        Route::get('page/urls/add', 'Page\Urls\View@add');
        Route::get('page/urls/move/{id}', 'Page\Urls\View@move');
        Route::post('page/urls/add', 'Page\Urls\Save@add');
        Route::post('page/urls/make_primary/{id}', 'Page\Urls\Save@makePrimary');
        Route::post('page/urls/move/{id}', 'Page\Urls\Save@move');
        Route::post('page/urls/delete/{id}', 'Page\Urls\Save@delete');

        Route::group(['prefix' => 'page/tags'], function() {
            Route::get('list/{page}', 'Page\Tags@listTags');
			Route::post('add/{page}', 'Page\Tags@add');
			Route::post('remove/{page}', 'Page\Tags@remove');
        });

        Route::group(['prefix' => 'page/settings'], function() {
            Route::get('{action}/{page}', [
                'uses' => function($action) {
                    return App::make('BoomCMS\Core\Controllers\CMS\Page\Settings\View')->$action();
                }
            ]);

            Route::post('{action}/{page}', [
                'uses' => function($action, $page) {
                    return App::make('BoomCMS\Core\Controllers\CMS\Page\Settings\Save')->$action($page);
                }
            ]);
        });
    });

    Route::post('page/children', 'BoomCMS\Core\Controllers\Page@children');

    Route::get('asset/{action}/{asset}/{width?}/{height?}', [
        'as' => 'asset',
        'uses' => function(BoomCMS\Core\Auth\Auth $auth, $action, $asset = null, $width = null, $height = null) {
            if ( ! $asset) {
                abort(404);
            }

            return App::make('BoomCMS\Core\Controllers\Asset\\' . class_basename($asset), [$auth, $asset])->$action($width, $height);
        }
    ]);
});

Route::any('{location}', [
    'middleware' => [
        'BoomCMS\Core\Http\Middleware\ProcessSiteURL',
        'BoomCMS\Core\Http\Middleware\InsertCMSToolbar',
    ],
    'uses' => 'BoomCMS\Core\Controllers\Page@show',
])->where(['location' => '.*']);
