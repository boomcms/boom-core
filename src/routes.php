<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cms', 'namespace' => 'Boom\Core'], function() {
    Route::group(array('middleware' => ['App\Http\Middleware\RedirectIfAuthenticated']), function() {
        Route::get('login', 'Cms_Auth_LoginController@showLoginForm');
        Route::post('login', 'Cms_Auth_LoginController');

        Route::get('logout', 'Cms_Auth_LogoutController@index');
        
        Route::get('recover', 'Cms_Auth_RecoverController@showForm');
        Route::get('recover', 'Cms_Auth_RecoverController@createToken');
        Route::get('recover', 'Cms_Auth_RecoverController@setNewPassword');
    });

    Route::group(array('middleware' => ['Authenticate']), function() {
        Route::get('profile', 'Cms_Profile@index');
        Route::post('profile', 'Cms_Profile@save');
    });
});

