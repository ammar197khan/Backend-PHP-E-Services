<?php

use App\Http\Middleware\IsCompany;
Route::get('/company-get-download', 'Company\HomeController@getDownload')->name('company.get.download');
Route::group(['prefix' => 'company','as'=>'company.'], function(){
    Route::get('/login', 'Company\AuthController@login_view')->name('view_login');
    Route::post('/login', 'Company\AuthController@login')->name('login');
    Route::get('/logout', 'Company\AuthController@logout')->name('logout');

    Route::group(['middleware' => ['company', IsCompany::class]], function () {
        Route::get('/dashboard', 'Company\HomeController@dashboard')->name('home');
        Route::get('/categories', 'Company\CategoryController@index');

        //Start Admin Profile
        Route::get('/admin/profile', 'Company\AdminController@profile')->name('admin.profile.index');
        Route::post('/admin/profile/update', 'Company\AdminController@update_profile')->name('admin.profile.update');
        Route::post('/admin/profile/change_password', 'Company\AdminController@update_profile_password')->name('admin.profile.update_password');
        //End Admin Profile

        //Start Company Profile
        Route::group(['middleware' => ['permission:View company info']], function ()
        {
            Route::get('/profile', 'Company\CompanyController@index')->name('profile');;
        });
        Route::group(['middleware' => ['permission:View company info']], function ()
        {
            Route::get('/profile/info', 'Company\CompanyController@info')->name('profile.info');;
        });
        Route::group(['middleware' => ['permission:Edit company info']], function () {
            Route::post('/profile/info/update', 'Company\CompanyController@update')->name('profile.info.update');
        });
        //End Company Profile

        //Start Admins
        Route::group(['middleware' => ['permission:View admin']], function ()
        {
            Route::get('/admins/index', 'Company\AdminController@index')->name('admins.index');
            Route::get('/admins/search', 'Company\AdminController@search')->name('admins.search');
            Route::get('/admins/{id}/view', 'Company\AdminController@view')->name('admins.view');

            Route::group(['middleware' => ['permission:Add admin']], function () {
                Route::get('/admin/create', 'Company\AdminController@create')->name('admins.create');
                Route::post('/admin/store', 'Company\AdminController@store')->name('admins.store');
            });
            Route::group(['middleware' => ['permission:Edit admin']], function ()
            {
                Route::get('/admin/{id}/edit', 'Company\AdminController@edit')->name('admins.edit');
                Route::post('/admin/update', 'Company\AdminController@update')->name('admins.update');
            });
            Route::group(['middleware' => ['permission:Delete admin']], function ()
            {
                Route::post('/admin/delete', 'Company\AdminController@destroy')->name('admins.destroy');
            });
            Route::group(['middleware' => ['permission:Active admin| Suspend admin']], function ()
            {
                Route::post('/admin/change_status', 'Company\AdminController@change_status')->name('admins.change_status');
            });
        });
        //End Admins

        Route::group(['middleware' => ['permission:View sub company']], function () {
            Route::get('/sub_company/{status}/search', 'Company\SubCompanyController@search');
            Route::get('/sub_companies/{state}', 'Company\SubCompanyController@index');
            Route::get('/sub_company/{id}/users', 'Company\SubCompanyController@users');
//        Route::post('/sub_company/delete', 'Company\SubCompanyController@destroy');

            Route::group(['middleware' => ['permission:Add sub company']], function ()
            {
                Route::get('/sub_company/create', 'Company\SubCompanyController@create');
                Route::post('/sub_company/store', 'Company\SubCompanyController@store');
            });
            Route::group(['middleware' => ['permission:Edit sub company']], function ()
            {
                Route::get('/sub_company/{id}/edit', 'Company\SubCompanyController@edit');
                Route::post('/sub_company/update', 'Company\SubCompanyController@update');
            });
            Route::group(['middleware' => ['permission:Suspend sub company']], function ()
            {
                Route::post('/sub_company/status/change', 'Company\SubCompanyController@change_status');
            });
        });

        Route::group(['middleware' => ['permission:View collaboration']], function ()
        {
            Route::get('/collaborations', 'Company\CollaborationController@index');

            Route::get('/collaboration/{id}/statistics/{type}', 'Company\CollaborationController@date_year_orders');
            Route::get('/collaboration/{id}/statistics/{type}/search', 'Company\CollaborationController@search');
            Route::get('/collaboration/{id}/statistics/items/{type}', 'Company\CollaborationController@date_items');
            Route::get('/collaboration/{id}/statistics/price/{type}', 'Company\CollaborationController@date_price');
            Route::get('/collaboration/{id}/statistics/rate/{type}', 'Company\CollaborationController@date_rate');
            Route::get('/collaboration/{collaboration_id}/order/{id}/view', 'Company\CollaborationController@show');

            Route::group(['middleware' => ['permission:Statistics collaboration']], function ()
            {
                Route::get('/collaboration/{collaboration_id}/statistics', 'Company\CollaborationController@statistics');
            });
            Route::group(['middleware' => ['permission:Order sheet collaboration']], function ()
            {
                Route::get('/collaboration/{provider_id}/orders/request', 'Company\CollaborationController@orders_request');
                Route::post('/collaboration/orders/invoice/show', 'Company\CollaborationController@orders_show');
                Route::post('/collaboration/orders/invoice/export', 'Company\CollaborationController@orders_export');
            });
            Route::group(['middleware' => ['permission:Fees sheet collaboration']], function ()
            {
                Route::get('/collaboration/{provider_id}/fees/show', 'Company\CollaborationController@fees_show');
                Route::get('/collaboration/{provider_id}/fees/export', 'Company\CollaborationController@fees_export');
            });
        });


        Route::group(['middleware' => ['permission:View user']], function () {

            Route::get('/users/{state}/search', 'Company\UserController@search');
            Route::get('/users/{state}', 'Company\UserController@index');
            Route::get('/user/{id}/view', 'Company\UserController@show')->where(['id' => '[0-9]+']);

            Route::get('/user/{id}/orders/request', 'Company\UserController@orders_request');
            Route::post('/user/orders/invoice/show', 'Company\UserController@orders_show');
            Route::post('/user/orders/invoice/export', 'Company\UserController@orders_export');

            Route::group(['middleware' => ['permission:Upload excel user']], function ()
            {
                Route::get('/user/excel/view', 'Company\UserController@excel_view');
                Route::post('/user/excel/upload', 'Company\UserController@excel_upload');
                Route::get('/users/{state}/excel/export', 'Company\UserController@excel_export');
            });
            Route::group(['middleware' => ['permission:Upload image user']], function ()
            {
                Route::get('/user/images/view', 'Company\UserController@images_view');
                Route::post('/user/images/upload', 'Company\UserController@images_upload');
            });
            Route::group(['middleware' => ['permission:Add user']], function () {
                Route::get('/user/create', 'Company\UserController@create');
                Route::post('/user/store', 'Company\UserController@store');
                Route::post('/user/order/store', 'Company\UserController@order_store');
            });
            Route::group(['middleware' => ['permission:Edit user']], function ()
            {
                Route::get('/user/{id}/edit', 'Company\UserController@edit');
                Route::post('/user/update', 'Company\UserController@update');
                Route::post('/user/change_password', 'Company\UserController@change_password');
            });
            Route::group(['middleware' => ['permission:Active user|Suspend user']], function () {
                Route::post('/user/change_state', 'Company\UserController@change_state');
            });
            //        Route::post('/user/delete', 'Company\UserController@destroy');

            Route::resource('house_types', 'Company\HouseTypeController');
        });


        Route::group(['middleware' => ['permission:View orders']], function ()
        {
            Route::get('/orders/dashboard/{type}', 'Company\HomeController@date_orders');
            Route::get('/items/dashboard/{type}', 'Company\HomeController@date_items');
            Route::get('/price/dashboard/{type}', 'Company\HomeController@date_price');
            Route::get('/{type}/search', 'Company\HomeController@search');
            Route::get('/rate/{type}', 'Company\HomeController@data_rate_orders');

            Route::get('/orders/{type}/search', 'Company\OrderController@search');
            Route::get('/orders/{type}', 'Company\OrderController@index');
            Route::get('/orders/{type}/{user_id}', 'Company\OrderController@user_orders');
            Route::get('/orders/{type}/monthly_orders', 'Company\OrderController@index');
            Route::get('/orders/{type}/monthly_open', 'Company\OrderController@index');
            Route::get('/orders/{type}/monthly_closed', 'Company\OrderController@index');
            Route::group(['middleware' => ['permission:View details order']], function ()
            {
                Route::get('/order/{id}/view', 'Company\OrderController@show');
            });
            Route::get('/orders/{type}/excel/view', 'Company\OrderController@excel_view');
            Route::post('/orders/{type}/excel/upload', 'Company\OrderController@excel_upload');
            Route::get('/orders/open/{type}/excel/view', 'Company\OrderController@excel_open_view');
            Route::post('/orders/open/{type}/excel/upload', 'Company\OrderController@excel_open_upload');

//            Route::get('/bills', 'Company\OrderController@bills');
//            Route::get('/bills/excel/export', 'Company\OrderController@bills_export');


//            Route::get('/{type}', 'Company\HomeController@date_year_orders');
//            Route::get('/{type}/search', 'Company\HomeController@search');
//            Route::get('/rate/{type}', 'Company\HomeController@data_rate_orders');
        });

        Route::group(['middleware' => ['permission:Add user']], function ()
        {
            Route::get('/user/{id}/order/create', 'Company\UserController@order_create');
            Route::get('/orders/{type}/invoice/request', 'Company\OrderController@orders_request');
            Route::post('/orders/invoice/show', 'Company\OrderController@orders_show');
            Route::post('/orders/invoice/export', 'Company\OrderController@orders_export');
        });

        Route::group(['middleware' => ['permission:SLA Order Dashboard']], function ()
        {
            Route::get('sla/order-dashboard', 'Company\SLAController@orderDashboard')->name('company.sla.orderDashboard');
            Route::get('sla/filter-order-dashboard', 'Company\SLAController@searchOrderDashboard')->name('company.sla.searchOrderDashboard');
        });
        Route::group(['middleware' => ['permission:Create SLA']], function ()
        {
        Route::get('SLA/index', 'Company\SLAController@index');
        Route::post('sla/update', 'Company\SLAController@update')->name('company.sla.update');
        });

        Route::get('/get_sub_category/{parent}', 'Company\HomeController@get_sub_category');
        Route::get('/get_sub_cat/{parent}', 'Company\HomeController@get_sub_cate');
        Route::get('/get_sub_company', 'Company\HomeController@get_sub_company');

        Route::group(['middleware' => ['permission:View item request']], function ()
        {
            Route::get('/show/item_requests/{status}/search', 'Company\ItemRequestController@search');
            Route::get('/show/item_requests/{status}', 'Company\ItemRequestController@index');
        });

        Route::group(['middleware' => ['permission:Approve item request']], function () {
            Route::post('/change/item_request/change_status', 'Company\ItemRequestController@change_status');
            Route::post('/change/item_request/change_status_order', 'Company\ItemRequestController@change_status_order');
        });

        //Ajax Routes
        Route::get('/get_cities/{parent}', 'Company\HomeController@get_cities');
        Route::get('/get_sub_cats_company/{parent}', 'Company\HomeController@get_subs');
        Route::get('/get_technician/{parent}', 'Company\HomeController@get_technician');
        //End Ajax Routes

        Route::get('appliances', 'Company\ApplianceController@index');
        Route::get('appliances/create', 'Company\ApplianceController@create');
        Route::post('appliances/store', 'Company\ApplianceController@store');
        Route::get('appliances/{id}/view', 'Company\ApplianceController@show');
        Route::get('appliances/{id}/edit', 'Company\ApplianceController@edit');
        Route::put('appliances/{id}/update', 'Company\ApplianceController@update');
        Route::delete('appliances/{id}/delete', 'Company\ApplianceController@destroy');
    });

});
