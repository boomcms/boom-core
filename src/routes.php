<?php

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
            Route::get('login', ['as' => 'login', 'uses' => 'AuthController@getLogin']);

            // Password reset link request routes...
            Route::get('recover', ['as' => 'password', 'uses' => 'PasswordReset@getEmail']);
            Route::post('recover', 'PasswordReset@postEmail');
            Route::get('recover/{token}', 'PasswordReset@getReset');
            Route::post('recover/{token}', 'PasswordReset@postReset');
            Route::post('login', 'AuthController@postLogin');
        });

        Route::group(['middleware' => [Middleware\RequireLogin::class]], function () {
            Route::get('logout', 'Auth\AuthController@getLogout');

            Route::controller('autocomplete', 'Autocomplete');
            Route::controller('editor', 'Editor');
            Route::controller('account', 'Auth\Account');
            Route::controller('approvals', 'Approvals');
            Route::controller('settings', 'Settings');
            Route::controller('support', 'Support');
            Route::post('editor/state', 'Editor@setState');
            Route::get('editor/toolbar/{page}', 'Editor@getToolbar');

            Route::get('asset-manager', 'Assets\AssetManager@index');
            Route::post('asset/tags/add', 'Asset\Tags@add');
            Route::post('asset/tags/remove', 'Asset\Tags@remove');
            Route::get('asset/tags', 'Asset\Tags@listTags');
            Route::resource('asset', 'Asset\AssetController');
            Route::delete('asset', 'Asset\AssetController@destroy');
            Route::post('asset/{asset}/replace', 'Asset\AssetController@replace');
            Route::post('asset/{asset}/revert', 'Asset\AssetController@revert');

            Route::group([
                'prefix'    => 'assets',
                'namespace' => 'Assets',
            ], function () {
                Route::post('save/{asset}', 'AssetManager@save');
                Route::post('revert/{asset}', 'AssetManager@revert');

                Route::controller('', 'AssetManager');
            });

            Route::group([
                'namespace'  => 'People',
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
});

Route::group(['prefix' => 'asset'], function () {
    Route::get('version/{id}/{width?}/{height?}', [
        'as'         => 'asset-version',
        'middleware' => ['web', Middleware\RequireLogin::class],
        'uses'       => function ($versionId, $width = null, $height = null) {
            $asset = Asset::findByVersionId($versionId);

            return App::make(AssetHelper::controller($asset), [$asset])->view($width, $height);
        },
    ]);

    Route::get('{asset}/download', [
        'asset'      => 'asset-download',
        'middleware' => [
            Middleware\LogAssetDownload::class,
        ],
        'uses' => function ($asset) {
            return App::make(AssetHelper::controller($asset), [$asset])->download();
        },
    ]);

    Route::get('{asset}/{action}.{extension}', [
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

    Route::get('{asset}/{action?}/{width?}/{height?}', [
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
        'web',
        Middleware\RoutePage::class,
        Middleware\InsertCMSToolbar::class,
    ],
    'uses' => 'BoomCMS\Http\Controllers\PageController@show',
])->where([
    'location' => '(.*?)',
    'format'   => '([a-z]+)',
]);
