<?php

use App\Http\Middleware\IsAdmin;
Route::get('/admin-get-download', 'Admin\HomeController@getDownload')->name('admin.get.download');
Route::name('admin.')->prefix('admin')->group(function(){
    Route::get('/login', 'Admin\AuthController@login_view');
    Route::post('/login', 'Admin\AuthController@login');
    Route::get('/logout', 'Admin\AuthController@logout');

    Route::group(['middleware' => ['admin'=>IsAdmin::class]], function () {

        Route::get('/dashboard', 'Admin\HomeController@dashboard')->name('admin.home');
        Route::get('/profile', 'Admin\HomeController@profile');
        Route::post('/profile/update', 'Admin\HomeController@update_profile');
        Route::post('/change_password', 'Admin\HomeController@change_password');


        // CATEGOTIES
        Route::get('categories', 'Admin\CategoryController@index')->name('categories.index')->middleware('permission:View category');
        Route::get('categories/create', 'Admin\CategoryController@create')->name('categories.create')->middleware('permission:Add category');
        Route::post('categories', 'Admin\CategoryController@store')->name('categories.store')->middleware('permission:Add category');
        Route::get('categories/{id}/edit', 'Admin\CategoryController@edit')->name('categories.edit')->middleware('permission:Edit category');
        Route::put('categories/{id}', 'Admin\CategoryController@update')->name('categories.update')->middleware('permission:Edit category');
        Route::post('category/delete', 'Admin\CategoryController@destroy')->name('categories.destroy')->middleware('permission:Delete category');
        Route::get('categories/export', 'Admin\CategoryController@export')->name('categories.export')->middleware('permission:View category');
        Route::get('categories/{id}/html', 'Admin\CategoryController@html');


        // PROVIDERS
        Route::get('/providers', 'Admin\ProviderController@index')->middleware('permission:View provider');
        Route::get('/provider/{id}/view', 'Admin\ProviderController@show')->middleware('permission:View provider');
        Route::get('/provider/create', 'Admin\ProviderController@create')->middleware('permission:Add provider');
        Route::post('/provider/store', 'Admin\ProviderController@store')->middleware('permission:Add provider');
        Route::get('/provider/{id}/edit', 'Admin\ProviderController@edit')->middleware('permission:Edit provider');
        Route::post('/provider/update', 'Admin\ProviderController@update')->middleware('permission:Edit provider');
        Route::post('/provider/delete', 'Admin\ProviderController@destroy')->middleware('permission:Delete provider');
        Route::post('/provider/change_state', 'Admin\ProviderController@change_state')->middleware('permission:Active provider');

        Route::post('/provider/subscriptions', 'Admin\ProviderSubscriptionController@store')->middleware('permission:Subscription provider');
        Route::get('/provider/{id}/subscriptions', 'Admin\ProviderSubscriptionController@edit')->middleware('permission:Subscription provider');

        Route::get('/provider/{id}/technicians', 'Admin\ProviderController@technicians')->middleware('permission:View provider');

        Route::get('/provider/{id}/invoice', 'Admin\BillController@materialsInvoiceDetails')->middleware('permission:View bills provider');
        Route::get('/provider/{id}/invoice_bills', 'Admin\BillController@servicesInvoiceDetails')->middleware('permission:View bills provider');
        Route::get('/provider/bills/all','Admin\BillController@index')->middleware('permission:View all bills provider');

        Route::get('/provider/bills/all/is-paid-store','Admin\BillController@isPaidStore')->name('is.paid.store');
        Route::get('/provider/bills/all/close-invoice','Admin\BillController@closeInvoice')->name('close.invoice');


        Route::get('/provider/bills/all/generate-monthly-invoice','Admin\BillController@generateMonthlyInvoice')->name('generate.monthly.invoice');
        Route::get('/provider/bills/all/search','Admin\BillController@search')->middleware('permission:View all bills provider');

        Route::get('/provider/{id}/statistics', 'Admin\ProviderStatistics@statistics')->middleware('permission:Statistics provider');
        Route::get('/provider/{id}/statistics/{type}', 'Admin\ProviderStatistics@date_year_orders')->middleware('permission:Statistics provider');
        Route::get('/provider/{id}/statistics/{type}/search', 'Admin\ProviderStatistics@date_search')->middleware('permission:Statistics provider');
        Route::get('/provider/{id}/statistics/items/{type}', 'Admin\ProviderStatistics@date_items')->middleware('permission:Statistics provider');
        Route::get('/provider/{id}/statistics/price/{type}', 'Admin\ProviderStatistics@date_price')->middleware('permission:Statistics provider');
        Route::get('/provider/{id}/statistics/rate/{type}', 'Admin\ProviderStatistics@date_rate')->middleware('permission:Statistics provider');

        // DEPRICATED
        Route::get('/provider/get_sub_company/{parent}', 'Admin\ProviderController@get_sub_company');
        Route::get('/provider/{id}/bills/search', 'Admin\ProviderController@bills_search');
        Route::post('/provider/{id}/bills/view', 'Admin\ProviderController@view_bills');
        Route::get('/provider/{company_id}/get_sub_category_provider/{parent}', 'Admin\ProviderController@get_sub_category_provider');
        Route::get('/provider/{id}/bills', 'Admin\ProviderController@bills');
        Route::get('/provider/{id}/get_sub_cats/{parent}', 'Admin\ProviderController@get_sub_cats')->middleware('permission:Statistics provider');
        Route::get('/provider/get_third_cats/{parent}', 'Admin\ProviderController@get_third_cats')->middleware('permission:Statistics provider');


        // ADMINS
        Route::get('/admins/index', 'Admin\AdminController@index')->middleware('permission:View admin');
        Route::get('/admins/search', 'Admin\AdminController@search')->middleware('permission:View admin');
        Route::get('/admins/{id}/view', 'Admin\AdminController@show')->middleware('permission:View admin');
        Route::get('/admin/create', 'Admin\AdminController@create')->middleware('permission:Add admin');
        Route::post('/admin/store', 'Admin\AdminController@store')->middleware('permission:Add admin');
        Route::get('/admin/{id}/edit', 'Admin\AdminController@edit')->middleware('permission:Edit admin');
        Route::post('/admin/update', 'Admin\AdminController@update')->middleware('permission:Edit admin');
        Route::post('/admin/delete', 'Admin\AdminController@destroy')->middleware('permission:Delete admin');
        Route::post('/admin/change_status', 'Admin\AdminController@change_status')->middleware('permission:Active admin|Suspend admin');


        // TECHNICIANS
        Route::resource('technicians', 'Admin\TechnicianController');




        Route::group(['middleware' => ['permission:View Address']], function ()
        {
            Route::get('/addresses/search', 'Admin\AddressController@search');
            Route::get('/addresses/{parent}', 'Admin\AddressController@index');

            Route::group(['middleware' => ['permission:Add city']], function ()
            {
                Route::get('/address/country/create', 'Admin\AddressController@country_create');
                Route::get('/address/city/create', 'Admin\AddressController@city_create');
                Route::post('/address/store', 'Admin\AddressController@store');
            });

            Route::group(['middleware' => ['permission:Edit city']], function ()
            {
                Route::get('/address/{id}/edit', 'Admin\AddressController@edit');
                Route::post('/address/update', 'Admin\AddressController@update');
            });

            Route::group(['middleware' => ['permission:Delete city']], function ()
            {
                Route::post('/address/delete', 'Admin\AddressController@destroy');
            });
        });




        Route::group(['middleware' => ['permission:View company']], function ()
        {
            Route::get('/companies/search', 'Admin\CompanyController@search');
            Route::get('/companies', 'Admin\CompanyController@index');
            Route::get('/company/{id}/view', 'Admin\CompanyController@show');
            Route::get('/company/{id}/users', 'Admin\CompanyController@show_users');
            Route::get('/company/{id}/users/search', 'Admin\CompanyController@show_users_search');
            Route::get('/company/user/{id}/view', 'Admin\CompanyController@view_user');

            Route::group(['middleware' => ['permission:Statistics company']], function ()
            {
                Route::get('/company/{id}/statistics', 'Admin\CompanyController@statistics');
                Route::get('/company/{id}/statistics/{type}', 'Admin\CompanyController@date_year_orders');
                Route::get('/company/{id}/statistics/{type}/search', 'Admin\CompanyController@date_search');
                Route::get('/company/{id}/statistics/items/{type}', 'Admin\CompanyController@date_items');
                Route::get('/company/{id}/statistics/price/{type}', 'Admin\CompanyController@date_price');
                Route::get('/company/{id}/statistics/rate/{type}', 'Admin\CompanyController@date_rate');
                Route::get('/company/{id}/get_sub_cats/{parent}', 'Admin\CompanyController@get_sub_cats');
            });

            Route::group(['middleware' => ['permission:Subscription company']], function ()
            {
                Route::get('/company/{id}/subscriptions', 'Admin\CompanyController@get_subscriptions');
                Route::post('/company/subscriptions', 'Admin\CompanyController@set_subscriptions');
            });

            Route::group(['middleware' => ['permission:Add company']], function ()
            {
                Route::get('/company/create', 'Admin\CompanyController@create');
                Route::post('/company/store', 'Admin\CompanyController@store');
            });

            Route::group(['middleware' => ['permission:Edit company']], function ()
            {
                Route::get('/company/{id}/edit', 'Admin\CompanyController@edit');
                Route::post('/company/update', 'Admin\CompanyController@update');
            });

            Route::group(['middleware' => ['permission:Delete company']], function ()
            {
                Route::post('/company/delete', 'Admin\CompanyController@destroy');;
            });

            Route::group(['middleware' => ['permission:Active company|Suspend company']], function ()
            {
                Route::post('/company/change_state', 'Admin\CompanyController@change_state');
            });
        });


        Route::group(['middleware' => ['permission:View collaboration']], function ()
        {
            Route::get('/collaborations', 'Admin\CollaborationController@index');
            Route::group(['middleware' => ['permission:Add collaboration']], function ()
            {
              Route::get('/collaboration/create', 'Admin\CollaborationController@create');
              Route::post('/collaboration/store', 'Admin\CollaborationController@store');
            });
            Route::group(['middleware' => ['permission:Edit collaboration']], function ()
            {
              Route::get('/collaboration/{provider_id}/edit', 'Admin\CollaborationController@edit');
              Route::post('/collaboration/update', 'Admin\CollaborationController@update');
            });
            Route::group(['middleware' => ['permission:Delete collaboration']], function ()
            {
              Route::post('/collaboration/delete', 'Admin\CollaborationController@destroy');
            });

            Route::get('/orders/dashboard/{type}','Admin\HomeController@date_orders');
            Route::get('/orders/{type}','Admin\HomeController@date_re_orders');
            Route::get('/items/dashboard/{type}', 'Admin\HomeController@date_items');
            Route::get('/price/dashboard/{type}', 'Admin\HomeController@date_price');
            Route::get('/orders/{type}/search', 'Admin\HomeController@search');
            Route::get('/order/{id}/view', 'Admin\HomeController@show');
            Route::get('/rate/{type}', 'Admin\HomeController@data_rate_orders');
        });
//        Route::get('/orders/dashboard/{type}', 'Provider\HomeController@date_orders');
//        Route::get('/items/dashboard/{type}', 'Provider\HomeController@date_items');
//        Route::get('/price/dashboard/{type}', 'Provider\HomeController@date_price');
//        Route::get('/{type}/search', 'Provider\HomeController@search');
//        Route::get('/rate/{type}', 'Provider\HomeController@data_rate_orders');


        Route::get('/individuals/user/{state}', 'Admin\IndividualController@user_index');
        Route::get('/individual/user/create', 'Admin\IndividualController@user_create');
        Route::post('/individual/user/store', 'Admin\IndividualController@user_store');
        Route::get('/individual/user/{id}/view', 'Admin\IndividualController@user_show');
        Route::get('/individual/user/{id}/edit', 'Admin\IndividualController@user_edit');
        Route::post('/individual/user/update', 'Admin\IndividualController@user_update');
        Route::post('/individual/user/change_state', 'Admin\IndividualController@user_change_status');
        Route::post('/individual/user/change_password', 'Admin\IndividualController@user_change_password');
        Route::post('/individual/user/delete', 'Admin\IndividualController@user_destroy');


        Route::get('/individuals/technician/{state}', 'Admin\IndividualController@index');
        Route::get('/individual/technician/create', 'Admin\IndividualController@create');
        Route::post('/individual/technician/store', 'Admin\IndividualController@store');
        Route::get('/individual/technician/{id}/view', 'Admin\IndividualController@show');
        Route::get('/individual/technician/{id}/edit', 'Admin\IndividualController@edit');
        Route::post('/individual/technician/update', 'Admin\IndividualController@update');
        Route::post('/individual/technician/change_state', 'Admin\IndividualController@change_status');
        Route::post('/individual/technician/change_password', 'Admin\IndividualController@change_password');
        Route::post('/individual/technician/delete', 'Admin\IndividualController@destroy');

        Route::get('/users/{state}', 'Admin\UserController@index');
        Route::get('/user/create', 'Admin\UserController@create');

        Route::group(['prefix' => '/settings'], function ()
        {
            Route::group(['middleware' => ['permission:View settings']], function ()
            {
                Route::get('/about', 'Admin\AboutController@index');
                Route::get('/terms', 'Admin\AboutController@terms');
                Route::get('/privacy', 'Admin\AboutController@privacy');
                Route::get('/complains', 'Admin\AboutController@complains');
                Route::post('/complains', 'Admin\AboutController@send_complains');

                Route::group(['middleware' => ['permission:Edit settings']], function ()
                {
                    Route::get('/about/edit', 'Admin\AboutController@edit');
                    Route::post('/about/update', 'Admin\AboutController@update');

                    Route::get('/terms/edit', 'Admin\AboutController@terms_edit');
                    Route::post('/terms/update', 'Admin\AboutController@terms_update');

                    Route::get('/privacy/edit', 'Admin\AboutController@privacy_edit');
                    Route::post('/privacy/update', 'Admin\AboutController@privacy_update');
                });
            });

        });


        //Ajax Routes
        Route::get('/get_cities/{parent}', 'Admin\HomeController@get_cities');
        Route::get('/get_sub_cats/{parent}', 'Admin\HomeController@get_sub_cats');
        Route::get('/get_sub_company/{parent}', 'Admin\HomeController@get_sub_company');
        //End Ajax Routes

        //Mail Routes
        Route::post('/mail/send', 'Admin\MailController@send');
        //End Mail Routes
    });

});


Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index')->middleware('auth.basic:admin');
