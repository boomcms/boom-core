<?php

Route::get('cms/login', 'CMSAuthLoginController@showLoginForm');
Route::post('cms/login', 'CMSAuthLoginController@processLogin');