<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
//    'BoomCMS\Core\Http\Middleware\RequireLoginForDevelopmentSites',
    'BoomCMS\Core\Http\Middleware\DisableHttpCacheIfLoggedIn',
]], function () {
    Route::group(['prefix' => 'cms', 'namespace' => 'BoomCMS\Core\Controllers\CMS'], function () {
        Route::group(['namespace' => 'Auth', 'middleware' => ['BoomCMS\Core\Http\Middleware\RedirectIfAuthenticated']], function () {
            Route::get('login', [
                'as' => 'login',
                'uses' => 'Login@showLoginForm'
            ]);

            Route::post('login', 'Login@processLogin');

            Route::get('logout', 'Logout@index');

            Route::get('recover', 'Recover@showForm');
            Route::get('recover', 'Recover@createToken');
            Route::get('recover', 'Recover@setNewPassword');
        });

        Route::group(['middleware' => ['BoomCMS\Core\Http\Middleware\RequireLogin']], function () {
            Route::get('profile', 'Auth\Profile@index');
            Route::post('profile', 'Auth\Profile@save');

            Route::group(['prefix' => 'assets', 'namespace' => 'Assets'], function () {
                Route::get('', 'AssetManager@index');
            });
        });
    });

    Route::get('asset/{action}/{asset}', [
        'as' => 'asset',
        'uses' => function($action, $asset = null) {
            return App::make('BoomCMS\Core\Controllers\AssetController')->$action($asset);
        }
    ]);
});
