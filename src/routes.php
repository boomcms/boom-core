<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => [
//    'BoomCMS\Core\Http\Middleware\RequireLoginForDevelopmentSites',
    'BoomCMS\Core\Http\Middleware\DisableHttpCacheIfLoggedIn',
]], function() {
    Route::group(['prefix' => 'cms', 'namespace' => 'BoomCMS\Core'], function() {
        Route::group(array('middleware' => ['BoomCMS\Core\Http\Middleware\RedirectIfAuthenticated']), function() {
            Route::get('login', [
                'as' => 'login',
                'uses' => 'Controllers\CMS\Auth\Login@showLoginForm'
            ]);

            Route::post('login', 'Controllers\CMS\Auth\Login@processLogin');

            Route::get('logout', 'Controllers\CMS\Auth\Logout@index');

            Route::get('recover', 'Controllers\CMS\Auth\Recover@showForm');
            Route::get('recover', 'Controllers\CMS\Auth\Recover@createToken');
            Route::get('recover', 'Controllers\CMS\Auth\Recover@setNewPassword');
        });

        Route::group(array('middleware' => ['BoomCMS\Core\Http\Middleware\RequireLogin']), function() {
            Route::get('profile', 'Controllers\CMS\Profile@index');
            Route::post('profile', 'Controllers\CMS\Auth\Profile@save');
        });
    });
});


