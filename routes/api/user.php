<?php

Route::group(['prefix' => '/user'], function(){
    Route::post('/login', 'Api\User\AuthController@login');
    Route::post('/register', 'Api\User\AuthController@register');
    Route::post('/splash', 'Api\User\AuthController@splash');
    Route::post('/code_send', 'Api\User\AuthController@code_send');
    Route::post('/code_check', 'Api\User\AuthController@code_check');
    Route::post('/password_change', 'Api\User\AuthController@password_change');
    Route::post('/location/set', 'Api\User\AuthController@set_location');

    Route::post('/profile', 'Api\User\AuthController@profile');
    Route::post('/profile/update', 'Api\User\AuthController@profile_update');

    Route::post('/home', 'Api\User\HomeController@index');
    Route::post('/home/sub_cats', 'Api\User\HomeController@sub_cats');
    Route::get('/about_us/{lang}', 'Api\User\HomeController@about_us');
    Route::get('/terms/{lang}', 'Api\User\HomeController@terms');
    Route::get('/privacy/{lang}', 'Api\User\HomeController@privacy');
    Route::get('/complain_titles/{lang}', 'Api\User\HomeController@complain_view');
    Route::post('/complain', 'Api\User\HomeController@complain');
    Route::post('/notifications', 'Api\User\HomeController@notifications');
    Route::post('/notification/seen', 'Api\User\HomeController@seen');

    Route::post('/get_techs', 'Api\User\OrderController@get_techs');
    Route::post('/search_techs', 'Api\User\OrderController@search_techs');
    Route::post('/view_tech', 'Api\User\OrderController@view_tech');

    Route::post('/order', 'Api\User\OrderController@order');
    Route::post('/orders', 'Api\User\OrderController@orders');
    Route::post('/order/details', 'Api\User\OrderController@details');
    Route::post('/order/item_change_status', 'Api\User\OrderController@item_change_status');
    Route::post('/order/view_tech_to_rate', 'Api\User\OrderController@view_tech_to_rate');
    Route::post('/order/rate', 'Api\User\OrderController@rate');
    Route::post('/order/re_schedule', 'Api\User\OrderController@re_schedule');
    Route::post('/order/cancel', 'Api\User\OrderController@cancel');
    Route::post('/order/items/submit', 'Api\User\OrderController@items_submit');
    Route::post('/v.0.2/order/items/submit', 'Api\User\OrderController@new_items_submit');
});
