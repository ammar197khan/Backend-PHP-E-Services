<?php

Route::group(['prefix' => '/tech'], function(){
    Route::post('/login', 'Api\Tech\AuthController@login');
    Route::post('/register', 'Api\Tech\AuthController@register');
    Route::post('/splash', 'Api\Tech\AuthController@splash');
    Route::post('/code_send', 'Api\Tech\AuthController@code_send');
    Route::post('/code_check', 'Api\Tech\AuthController@code_check');
    Route::post('/password_change', 'Api\Tech\AuthController@password_change');
    Route::post('/location/set', 'Api\Tech\AuthController@set_location');
    Route::post('/status/switch', 'Api\Tech\AuthController@status_switch');

    Route::post('/profile', 'Api\Tech\AuthController@profile');
    Route::post('/profile/update', 'Api\Tech\AuthController@profile_update');

    Route::post('/home', 'Api\Tech\HomeController@index');
    Route::post('/orders', 'Api\Tech\OrderController@orders');
    Route::get('/about_us/{lang}', 'Api\Tech\HomeController@about_us');
    Route::get('/terms/{lang}', 'Api\Tech\HomeController@terms');
    Route::get('/privacy/{lang}', 'Api\Tech\HomeController@privacy');
    Route::post('/notifications', 'Api\Tech\HomeController@notifications');
    Route::post('/notification/seen', 'Api\Tech\HomeController@seen');
    Route::post('/rates', 'Api\Tech\HomeController@rates');

    Route::post('/orders', 'Api\Tech\OrderController@orders');
    Route::post('/order/details', 'Api\Tech\OrderController@details');
    Route::post('/order/track/update','Api\Tech\OrderController@update_track');
    Route::post('/warehouse/cats','Api\Tech\OrderController@warehouse_cats');
    Route::post('/warehouse/items','Api\Tech\OrderController@warehouse_items');
    Route::post('/warehouse/item/show','Api\Tech\OrderController@warehouse_show_item');
    Route::post('/warehouse/item/add','Api\Tech\OrderController@warehouse_add_item');
    Route::post('/warehouse/item/request','Api\Tech\OrderController@warehouse_request_item');
    Route::post('/warehouse/search','Api\Tech\OrderController@warehouse_search');
    Route::post('/order/get_third_levels','Api\Tech\OrderController@get_third_levels');
    Route::post('/order/change_status','Api\Tech\OrderController@change_status');
    Route::post('/order/cancel','Api\Tech\OrderController@cancel');
});
