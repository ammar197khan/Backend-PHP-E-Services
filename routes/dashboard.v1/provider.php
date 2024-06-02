<?php

use App\Http\Middleware\IsProvider;

Route::get('/provider-get-download', 'Provider\HomeController@getDownload')->name('provider.get.download');
Route::group(['prefix' => '/provider'], function(){

    Route::get('/login', 'Provider\AuthController@login_view');
    Route::post('/login', 'Provider\AuthController@login');

    Route::group(['middleware' => ['provider'=>IsProvider::class]], function () {

        Route::get('/dashboard', 'Provider\HomeController@dashboard')->name('provider.home');
        Route::get('/categories', 'Provider\CategoryController@index');
        Route::get('/logout', 'Provider\AuthController@logout');

        Route::group(['middleware' => ['permission:View admin']], function ()
        {
            Route::get('/admins/index', 'Provider\AdminController@index');
            Route::get('/admins/search', 'Provider\AdminController@search');
            Route::get('/admins/{id}/view', 'Provider\AdminController@show');
            Route::get('/admin/create', 'Provider\AdminController@create');
            Route::post('/admin/store', 'Provider\AdminController@store');
            Route::get('/admin/{id}/edit', 'Provider\AdminController@edit');
            Route::post('/admin/update', 'Provider\AdminController@update');
            Route::post('/admin/delete', 'Provider\AdminController@destroy');
            Route::post('/admin/change_status', 'Provider\AdminController@change_status');
        });

            Route::get('/profile', 'Provider\HomeController@profile');
            Route::post('/profile/update', 'Provider\HomeController@update_profile');
            Route::post('/change_password', 'Provider\HomeController@change_password');

            Route::group(['middleware' => ['permission:View provider info']], function () {
                Route::get('/info', 'Provider\HomeController@info');
            });

            Route::group(['middleware' => ['permission:Edit provider info']], function () {
                Route::post('/info/update', 'Provider\HomeController@update_info');
            });

        Route::group(['middleware' => ['permission:View collaboration']], function () {
          Route::get('/collaborations', 'Provider\CollaborationController@index');
            Route::get('/collaborations', 'Provider\CollaborationController@index');

            Route::group(['middleware' => ['permission:Statistics collaboration']], function () {
                Route::get('/collaboration/{id}/statistics', 'Provider\CollaborationController@statistics');
            });

            Route::group(['middleware' => ['permission:Bills collaboration']], function () {
                Route::get('/collaboration/{id}/bills', 'Provider\CollaborationController@bills');
                Route::post('/collaboration/{company_id}/bills/view', 'Provider\CollaborationController@view_bills');
                Route::get('/collaboration/{id}/bills/search', 'Provider\CollaborationController@bills_search');
                Route::get('/collaboration/{id}/bills_export', 'Provider\CollaborationController@bills_export');
                Route::get('/collaboration/{id}/bills_export/{search}', 'Provider\CollaborationController@bills_export_search');
            });

            Route::get('/collaboration/{id}/statistics/{type}', 'Provider\CollaborationController@date_year_orders');
            Route::get('/collaboration/{id}/statistics/{type}/search', 'Provider\CollaborationController@search');
            Route::get('/collaboration/{id}/statistics/items/{type}', 'Provider\CollaborationController@date_items');
            Route::get('/collaboration/{id}/statistics/price/{type}', 'Provider\CollaborationController@date_price');
            Route::get('/collaboration/{id}/statistics/rate/{type}', 'Provider\CollaborationController@date_rate');
            Route::get('/collaboration/{collaboration_id}/order/{id}/view', 'Provider\CollaborationController@show');
            Route::get('/get_sub_company/{parent}', 'Provider\CollaborationController@get_sub_company');
            Route::get('/{company_id}/get_sub_category_provider/{parent}', 'Provider\CollaborationController@get_sub_category_provider');

            Route::group(['middleware' => ['permission:Service fee collaboration']], function () {
                Route::get('/collaboration/{id}/services/fees/view', 'Provider\ServiceFeeController@view');
                Route::post('/collaboration/services/fees/update', 'Provider\ServiceFeeController@update');
            });

            Route::group(['middleware' => ['permission:Third fee collaboration']], function () {
                Route::get('/collaboration/{id}/third/fees/view', 'Provider\ThirdCategoryFeeController@view');
                Route::post('/collaboration/third/fees/update', 'Provider\ThirdCategoryFeeController@update');
//        Route::get('/collaboration/{company_id}/print/bills', 'Provider\CollaborationController@print_bills');
            });

        });

        Route::group(['middleware' => ['permission:View collaboration']], function () {
            Route::get('invoices', 'Provider\InvoiceController@index');
            Route::get('close-invoice', 'Provider\InvoiceController@closeInvoice')->name('provider.close.invoice');
            Route::get('is-paid-store', 'Provider\InvoiceController@isPaidStore')->name('provider.is.paid.store');

            Route::get('get-monthly-invoices', 'Provider\InvoiceController@getMonthlyInvoice');
            Route::get('generate-monthly-invoice', 'Provider\InvoiceController@generateMonthlyInvoice')->name('provider.generate.monthly.invoice');
            Route::get('invoices/{company_id}/orders/print', 'Provider\InvoiceController@printOrdersInvoice');
            Route::get('invoices/{company_id}/materials/print', 'Provider\InvoiceController@printMaterialsInvoice');
        });



        Route::group(['middleware' => ['permission:View technician']], function () {
            Route::get('/technician/excel/view', 'Provider\TechnicianController@excel_view');

            Route::group(['middleware' => ['permission:Upload excel technician']], function () {
                Route::post('/technician/excel/upload', 'Provider\TechnicianController@excel_upload');
            });

            Route::get('/technician/{state}/excel/export', 'Provider\TechnicianController@excel_export');

            Route::group(['middleware' => ['permission:Upload image technician']], function () {
                Route::get('/technician/images/view', 'Provider\TechnicianController@images_view');
                Route::post('/technician/images/upload', 'Provider\TechnicianController@images_upload');
            });

            Route::group(['middleware' => ['permission:Statistics technician']], function () {
                Route::get('/technicians/statistics', 'Provider\TechnicianController@statistics');
                Route::get('/technician/{id}/orders/request', 'Provider\TechnicianController@orders_request');
                Route::post('/technician/orders/invoice/show', 'Provider\TechnicianController@orders_show');
                Route::post('/technician/orders/invoice/export', 'Provider\TechnicianController@orders_export');
                Route::get('/technicians/statistics/search', 'Provider\TechnicianController@statistics_search');
            });

            Route::get('/technicians/{state}/search', 'Provider\TechnicianController@search');
            Route::get('/technicians/{state}', 'Provider\TechnicianController@index');
            Route::get('/technicians/{state}/tech_status', 'Provider\TechnicianController@tech_status');

            Route::group(['middleware' => ['permission:Add technician']], function () {
                Route::get('/technician/create', 'Provider\TechnicianController@create');
                Route::post('/technician/store', 'Provider\TechnicianController@store');
            });

            Route::get('/technician/{id}/view', 'Provider\TechnicianController@show');

            Route::group(['middleware' => ['permission:Add technician']], function () {
                Route::get('/technician/{id}/edit', 'Provider\TechnicianController@edit');
                Route::post('/technician/update', 'Provider\TechnicianController@update');
                Route::post('/technician/change_password', 'Provider\TechnicianController@change_password');
            });

            Route::group(['middleware' => ['permission:Active technician|Suspend technician']], function () {
                Route::post('/technician/change_state', 'Provider\TechnicianController@change_state');
            });
//        Route::post('/technician/delete', 'Provider\TechnicianController@destroy');
        });

        Route::group(['middleware' => ['permission:View rotation']], function () {
            Route::get('/rotations/index', 'Provider\RotationController@index');

            Route::group(['middleware' => ['permission:Add rotation']], function () {
                Route::get('/rotation/create', 'Provider\RotationController@create');
                Route::post('/rotation/store', 'Provider\RotationController@store');
            });

            Route::group(['middleware' => ['permission:Edit rotation']], function () {
                Route::get('/rotation/{id}/edit', 'Provider\RotationController@edit');
                Route::post('/rotation/update', 'Provider\RotationController@update');
            });

            Route::group(['middleware' => ['permission:Delete rotation']], function () {
                Route::post('/rotation/delete', 'Provider\RotationController@destroy');
            });
        });

        Route::group(['middleware' => ['permission:View warehouse']], function () {

            Route::group(['middleware' => ['permission:Upload excel warehouse']], function () {
                Route::get('/warehouse/excel/view', 'Provider\WarehouseController@excel_view');
                Route::post('/warehouse/excel/upload', 'Provider\WarehouseController@excel_upload');
            });

            Route::group(['middleware' => ['permission:Upload images warehouse']], function () {
                Route::get('/warehouse/images/view', 'Provider\WarehouseController@images_view');
                Route::post('/warehouse/images/upload', 'Provider\WarehouseController@images_upload');
            });

            Route::group(['middleware' => ['permission:Export categories warehouse']], function () {
                Route::get('/warehouse/excel/categories/export', 'Provider\WarehouseController@categories_excel_export');
            });

            Route::group(['middleware' => ['permission:Export parts warehouse']], function () {
                Route::get('/warehouse/excel/parts/export', 'Provider\WarehouseController@parts_excel_export');
            });

            Route::get('/warehouse/search', 'Provider\WarehouseController@search');
            Route::get('/warehouse/{parent}', 'Provider\WarehouseController@index');
            Route::get('/warehouse/{parent}/items', 'Provider\WarehouseController@items');

            Route::group(['middleware' => ['permission:Add item warehouse']], function () {
                Route::get('/warehouse/item/create', 'Provider\WarehouseController@create');
                Route::post('/warehouse/item/store', 'Provider\WarehouseController@store');
            });

            Route::group(['middleware' => ['permission:Edit item warehouse']], function () {
                Route::get('/warehouse/item/{id}/edit', 'Provider\WarehouseController@edit');
                Route::post('/warehouse/item/update', 'Provider\WarehouseController@update');
            });

            Route::group(['middleware' => ['permission:Suspend item warehouse']], function () {
                Route::post('/warehouse/item/change_status', 'Provider\WarehouseController@change_status');
            });
        });
//        Route::post('/warehouse/item/delete', 'Provider\WarehouseController@destroy');

        Route::group(['middleware' => ['permission:View warehouse request']], function () {
            Route::get('/warehouse_requests', 'Provider\WarehouseRequestController@index');
//        Route::post('/warehouse_request/delete', 'Provider\WarehouseRequestController@destroy');
        });

        Route::group(['middleware' => ['permission:View orders']], function () {
            Route::get('/orders/{type}/invoice/request', 'Provider\OrderController@orders_request');
            Route::post('/orders/invoice/show', 'Provider\OrderController@orders_show');
            Route::post('/orders/invoice/export', 'Provider\OrderController@orders_export');
            Route::get('/orders/{state}/search', 'Provider\OrderController@search');
            Route::get('/orders/{type}', 'Provider\OrderController@index');
            Route::get('/orders/open/waiting', 'Provider\OrderController@waiting');
            Route::get('/orders/open/waiting/upload/view', 'Provider\OrderController@waiting_upload_view');
            Route::post('/orders/open/waiting/upload', 'Provider\OrderController@waiting_upload');
            Route::get('/order/{id}/view', 'Provider\OrderController@show');
            Route::post('/order/cancel/{type}', 'Provider\OrderController@cancel');
            Route::get('/orders/excel/view', 'Provider\OrderController@excel_view');
            Route::post('/orders/excel/upload', 'Provider\OrderController@excel_upload');
            Route::get('/orders/excel/tech/view', 'Provider\OrderController@excel_tech_request_view');
            Route::post('/orders/excel/tech/upload', 'Provider\OrderController@excel_tech_request_upload');
            Route::get('/orders/images/view', 'Provider\OrderController@images_view');
            Route::post('/orders/images/upload', 'Provider\OrderController@images_upload');
            Route::get('get_sub_category_provider/{parent}', 'Provider\OrderController@get_sub_category_provider');
            Route::get('get_third_category_provider/{parent}', 'Provider\OrderController@get_third_category_provider');
            Route::get('/orders/tech_status/{id}', 'Provider\OrderController@tech_status_orders');
            Route::post('/order/order_expenses', 'Provider\OrderController@order_expenses');
            Route::post('/order/order_finish', 'Provider\OrderController@order_finish');
            Route::post('/order/order_update', 'Provider\OrderController@order_Update');
            Route::get('/order/finish', 'Provider\OrderController@finish');

            Route::get('/order/{id}/company/statistics/view', 'Provider\OrderController@show');
        });


        //Start dashboard month and year orders
        Route::get('/orders/dashboard/{type}', 'Provider\HomeController@date_orders');
        Route::get('/items/dashboard/{type}', 'Provider\HomeController@date_items');
        Route::get('/price/dashboard/{type}', 'Provider\HomeController@date_price');
        Route::get('/{type}/search', 'Provider\HomeController@search');
        Route::get('/rate/{type}', 'Provider\HomeController@data_rate_orders');
        //End dashboard year orders

        //Ajax Routes
        Route::get('/get_cities/{parent}', 'Provider\HomeController@get_cities');
        Route::get('/get_sub_cats/{parent}', 'Provider\HomeController@get_sub_cats');
        Route::get('/tech_get_sub_cats/{parent}', 'Provider\HomeController@tech_get_sub_cats');
        //End Ajax Routes
    });
});
